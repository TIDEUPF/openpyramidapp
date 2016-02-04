<?php

namespace Group;


function get_group_name() {

}

function get_members($params) {
    global $link, $sid, $fid, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $sa_result_1 = mysqli_query($link, "select * from pyramid_groups where pg_fid = '$fid' and pg_level = '$activity_level'");
    if(mysqli_num_rows($sa_result_1) > 0){ //get current level pyramid group info
        while($sa_data_1 = mysqli_fetch_assoc($sa_result_1))
        {
            $peer_array_temp = explode(",",$sa_data_1['pg_group']);
            if(in_array($sid,$peer_array_temp)){
                $peer_array = $peer_array_temp;
                $peer_group_id = $sa_data_1['pg_group_id'];
                $peer_group_combined_ids = $sa_data_1['pg_combined_group_ids'];
            }
        }
    }
    $peer_group_combined_ids_temp = explode(",",$peer_group_combined_ids);
}

function get_next_level_groups($params) {
    global $link, $sid, $fid, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $next_activity_level = $activity_level + 1;
    $sa_result_1 = mysqli_query($link, "select * from pyramid_groups where pg_fid = '$fid' and pg_level = '$next_activity_level'");
    if(mysqli_num_rows($sa_result_1) > 0){ //get current level pyramid group info
        while($sa_data_1 = mysqli_fetch_assoc($sa_result_1))
        {
            $peer_array_temp = explode(",",$sa_data_1['pg_group']);
            if(in_array($sid,$peer_array_temp)){
                $result['peer_array']                   = $peer_array_temp;
                $result['peer_group_id']                = $sa_data_1['pg_group_id'];
                $result['peer_group_combined_ids']      = $sa_data_1['pg_combined_group_ids'];
                $result['peer_group_combined_ids_temp'] = explode(",",$result['peer_group_combined_ids']);
            }
        }
    }
    return $result;
}

function get_needed_results_to_end_level($full_requirements = false, $level = null) {
    global $link, $sid, $fid, $activity_level, $peer_array, $answer_submit_required_percentage, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp, $answer_required_percentage;

    $group_size = count($peer_array); //no of peers in the branch
    if(!\Answer\is_submitted() or $level == 'answer') {
        $needed_results = count($peer_array);
        $status_percentage = $answer_submit_required_percentage;
        $opt_count = 1;
    } elseif($activity_level == 0) {
        //in the rating stage we can have more users than questions and skips
        $opt_count = count(\Answer\get_selected_ids(true));
        $needed_results = $opt_count * $group_size; //in the first level, it's no. of choices * student count
        $status_percentage = $answer_submit_required_percentage;
    } else{
        $opt_count = count(\Answer\get_selected_ids(true));
        $needed_results = $group_size * $opt_count; //because now every student is rating two answers, need to occupy all answers
        $status_percentage = $answer_required_percentage;
    }

    if(!$full_requirements) {
        $needed_results = floor(floor($needed_results * $status_percentage / 100.0)/$opt_count)*$opt_count;
    }

    if(empty($needed_results))
        $needed_results = 1;

    return $needed_results;
}

function check_if_group_finished_level()
{
    global $link, $sid, $fid, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    if(!($activity_level == 0 and !\Answer\is_submitted())) {
        $cgfl_result_1 = mysqli_query($link, "select * from flow_student_rating where fsr_fid = '$fid' and fsr_level = '$activity_level' and fsr_group_id = '$peer_group_id'");
        $cgfl_result_1_count = mysqli_num_rows($cgfl_result_1);
    } else {
        $peer_array_sql = implode("','", \Util\sanitize_array($peer_array));

        $cgfl_result_1 = mysqli_query($link, "select * from flow_student where fid = '{$fid}' and sid in ('{$peer_array_sql}')");
        $cgfl_result_1_count = mysqli_num_rows($cgfl_result_1);
    }

    if(\Group\is_level_timeout())
        return true;

    //all members submitted
    if($cgfl_result_1_count >= get_needed_results_to_end_level(true))
        return true;

    return false;
}

function check_if_previous_groups_completed_task()
{
    global $link, $sid, $fid, $flow_data, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $activity_level_previous = $activity_level-1;

    //the first stage is always true
    if($activity_level == 0 and !\Answer\is_submitted() and !is_level_zero_rating_started())
        return true;

    //check if every group member has submitted the answer or the timeout is expired
    if($activity_level_previous == -1)
        return \Answer\is_timeout();

    //the previous groups have selected an answer
    $required_sa_count = count(explode(",",$peer_group_combined_ids));
    $previous_sa_count = get_previous_groups_rated_count();
    if($previous_sa_count >= $required_sa_count)
        return true;

    //if all the groups are timed out the level is complete
    $time = time();
    $max_start_time = $time - $flow_data['hardtimer_rating'];
    $complete_query = mysqli_query($link, "select * from pyramid_groups where pg_start_timestamp <= '{$max_start_time}' and pg_group_id in ({$peer_group_combined_ids}) and pg_level = '{$activity_level_previous}' and pg_started = 1 and pg_fid = '$fid'");
    $all_query = mysqli_query($link, "select * from pyramid_groups where pg_group_id in ({$peer_group_combined_ids}) and pg_level = '{$activity_level_previous}' and pg_fid = '$fid'");
    if(mysqli_num_rows($complete_query) == mysqli_num_rows($all_query))
        return true;

    return false;
}

function check_if_sibling_groups_hardtimer_expired() {
    global $link, $sid, $fid, $flow_data, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    //if all the groups are timed out the level is complete
    $time = time();
    $max_start_time = $time - $flow_data['hardtimer_rating'];
    $complete_query = mysqli_query($link, "select * from pyramid_groups where pg_start_timestamp <= '{$max_start_time}' and pg_level = '{$activity_level}' and pg_started = 1 and pg_fid = '$fid'");
    $all_query = mysqli_query($link, "select * from pyramid_groups where pg_level = '{$activity_level}' and pg_fid = '$fid'");
    if(mysqli_num_rows($complete_query) == mysqli_num_rows($all_query))
        return true;

    return false;
}


function get_previous_groups_rated_count() {
    global $link, $sid, $levels, $fid, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    if($activity_level >= $levels) {
        $final_level = $levels - 1;
        $cipgct_result_1 = mysqli_query($link, "select * from selected_answers where sa_fid = '$fid' and sa_level = '$final_level'");
        return mysqli_num_rows($cipgct_result_1);
    }

    $peer_group_combined_ids_array = explode(",",$peer_group_combined_ids);
    foreach($peer_group_combined_ids_array as $temp_id)
    {
        $sql1_ids[] = "sa_group_id = ".$temp_id;
    }

    $activity_level_previous = $activity_level-1;
    $sql1 = implode(" or ", $sql1_ids);
    $cipgct_result_1 = mysqli_query($link, "select * from selected_answers where sa_fid = '$fid' and sa_level = '$activity_level_previous' and ($sql1) ");
    $cipgct_result_1_count = mysqli_num_rows($cipgct_result_1);

    return $cipgct_result_1_count;
}

function get_next_groups_rated_count() {
    global $link, $sid, $fid, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $n_groups = get_next_level_groups();

    foreach($n_groups['peer_group_combined_ids_temp'] as $temp_id)
    {
        $sql1_ids[] = "sa_group_id = ".$temp_id;
    }

    $sql1 = implode(" or ", $sql1_ids);
    $cipgct_result_1 = mysqli_query($link, "select * from selected_answers where sa_fid = '$fid' and sa_level = '$activity_level' and ($sql1) ");
    $cipgct_result_1_count = mysqli_num_rows($cipgct_result_1);

    return $cipgct_result_1_count;
}

function is_level_zero_rating_started() {
    global $link, $activity_level, $fid, $peer_group_id;

    $gcal_result_1 = mysqli_query($link, "select * from pyramid_groups where pg_group_id = '{$peer_group_id}' and pg_level = 0 and pg_started = 1 and pg_fid = '$fid'");
    if (mysqli_num_rows($gcal_result_1) > 0)
        return true;

    return false;
}

function is_level_timeout() {
    global $timeout, $activity_level, $fid, $peer_group_id, $flow_data;

    //if($activity_level == 0 and !\Answer\is_submitted() and !is_level_zero_rating_started())
    //    return \Answer\is_timeout();

    if($activity_level == 0 and !is_level_zero_rating_started())
        return \Answer\is_timeout();

    if(sa_exists())
        return true;

    //hardtimer
    $time = time();
    $level_start_time = get_level_start_timestamp($fid, $activity_level, $peer_group_id);
    if($level_start_time > 0 and $time > $level_start_time + $flow_data['hardtimer_rating'])
        return true;

    //satisfaction timeout
    if(is_level_minimun_required_answers_to_set_timestamps_reached()) {
        if(!$timestamp = get_level_timeout_timestamp()) {
            $timestamp = set_level_timeout_timestamp();
        }

        if(time() > $timestamp + $timeout)
            return true;
    }

    return false;
}

function is_level_minimun_required_answers_to_set_timestamps_reached() {
    global $link, $sid, $fid, $sname, $levels, $activity_level, $peer_group_id;

    $submitted_group_answers_query = mysqli_query($link, "select * from flow_student_rating where fsr_fid='{$fid}' and fsr_level='{$activity_level}' and fsr_group_id='{$peer_group_id}'");
    $submitted_group_answers_count = mysqli_num_rows($submitted_group_answers_query);

    if($submitted_group_answers_count >= get_needed_results_to_end_level())
        return true;

    return false;
}

function set_level_timeout_timestamp()
{
    global $link, $sid, $fid, $sname, $levels, $activity_level, $peer_group_id;

    $timestamp = time();
    mysqli_query($link, "update pyramid_groups set pg_timestamp='{$timestamp}' where pg_fid='{$fid}' and pg_level='{$activity_level}' and pg_group_id='{$peer_group_id}'");

    return $timestamp;
}

function get_time_left() {
    global $link, $sid, $fid, $ftimestamp, $answer_timeout, $answer_skip_timeout, $peer_array, $timeout, $flow_data;

    $timestamp = get_level_timeout_timestamp();
    $start_timestamp = get_level_start_timestamp();

    if(!\Answer\is_timeout()) {
        $hardtime_left = $flow_data['hardtimer_question'] + $start_timestamp - time();
        if($hardtime_left < $answer_timeout or ($timestamp and is_numeric($timestamp))) {
            $satisfaction_left = ($timestamp + $answer_timeout) - time();
            return ($hardtime_left > $satisfaction_left and $timestamp) ? $satisfaction_left : $hardtime_left;
        }
    } else {
        $hardtime_left = $flow_data['hardtimer_rating'] + $start_timestamp - time();
        if($hardtime_left < $timeout or ($timestamp and is_numeric($timestamp))) {
            $satisfaction_left = ($timestamp + $timeout) - time();
            return ($hardtime_left > $satisfaction_left and $timestamp) ? $satisfaction_left : $hardtime_left;
        }
    }

    return null;
}

function get_level_timeout_timestamp($fid, $activity_level, $peer_group_id) {
    global $link, $peer_group_id, $activity_level, $fid;

    if(!\Answer\is_timeout()) {
        $answer_user_timeout = \Answer\get_answer_timeout();
        return $answer_user_timeout['time_left'];
    }

    $submitted_group_answers_timestamp_query = mysqli_query($link, "select * from pyramid_groups where pg_timestamp > 0 and pg_fid='{$fid}' and pg_level='{$activity_level}' and pg_group_id='{$peer_group_id}' order by pg_timestamp asc limit 1");
    if(mysqli_num_rows($submitted_group_answers_timestamp_query) > 0) {
        $submitted_group_answers_timestamp_row_array = mysqli_fetch_assoc($submitted_group_answers_timestamp_query);
        return $submitted_group_answers_timestamp_row_array['pg_timestamp'];
    } else {
        return FALSE;
    }
}

function get_level_start_timestamp($fid, $activity_level, $peer_group_id) {
    global $link, $peer_group_id, $activity_level, $fid;

    if(!\Answer\is_timeout()) {
        $answer_user_timeout = \Answer\get_answer_timeout();
        return $answer_user_timeout['start_timestamp'];
    }

    $submitted_group_answers_timestamp_query = mysqli_query($link, "select * from pyramid_groups where pg_start_timestamp > 0 and pg_fid='{$fid}' and pg_level='{$activity_level}' and pg_group_id='{$peer_group_id}' limit 1");
    if(mysqli_num_rows($submitted_group_answers_timestamp_query) > 0) {
        $submitted_group_answers_timestamp_row_array = mysqli_fetch_assoc($submitted_group_answers_timestamp_query);
        return $submitted_group_answers_timestamp_row_array['pg_start_timestamp'];
    } else {
        return FALSE;
    }
}

function get_status_bar_peers() {
    global $link, $sid, $fid, $sname, $levels, $activity_level, $peer_group_id, $peer_array, $peer_group_combined_ids;

    if(empty($peer_array) and $activity_level == $levels) {
        $top_level = $levels-1;
        $result = mysqli_query($link, "select * from pyramid_groups where pg_fid='{$fid}' and pg_level='{$top_level}'");
        $sid_string_array = array();
        while($result_array = mysqli_fetch_assoc($result)) {
            $sid_string_array[] = $result_array['pg_group'];
        }
        $full_sid_string = implode(',', $sid_string_array);
        return explode(',', $full_sid_string);
    }

    if($activity_level == 0)
        return $peer_array;

    if(\Group\check_if_previous_groups_completed_task())
        return $peer_array;
    else {
        $top_level = $activity_level-1;
        $result = mysqli_query($link, "select * from pyramid_groups where pg_fid='{$fid}' and pg_level='{$top_level}' and pg_group_id in ({$peer_group_combined_ids})");
        $sid_string_array = array();
        while($result_array = mysqli_fetch_assoc($result)) {
            $sid_string_array[] = $result_array['pg_group'];
        }
        $full_sid_string = implode(',', $sid_string_array);
        return explode(',', $full_sid_string);
    }

    return $peer_array;
}

function get_status_bar_groups_count() {
    global $link, $sid, $fid, $sname, $levels, $activity_level, $peer_group_id, $peer_array, $peer_group_combined_ids;

    if($activity_level == $levels) {
        $top_level = $levels-1;
        $result = mysqli_query($link, "select * from pyramid_groups where pg_fid='{$fid}' and pg_level='{$top_level}'");
        $i=0;
        while($result_array = mysqli_fetch_assoc($result)) {
            $i++;
        }
        return $i;
    }

    $peer_group_combined_ids_array = explode(",",$peer_group_combined_ids);
    $array_size = count($peer_group_combined_ids_array);

    return $array_size;
}

function get_peers_sname() {
    global $sname, $peer_array;

    $sname_list = get_sname_list($peer_array);
    $peer_list = [];
    foreach($sname_list as $peer) {
        if($peer != $sname)
            $peer_list[] = $peer;
    }

    return $peer_list;
}

function get_sname_list($sid_array) {
    global $link;

    $result = [];
    $sid_list = implode("','", $sid_array);
    $sname_result = mysqli_query($link, "select * from students where sid in ('$sid_list')");
    if(mysqli_num_rows($sname_result) > 0) {
        while($sname_result_array = mysqli_fetch_assoc($sname_result)) {
            $result[] = $sname_result_array['sname'];
        }
    }

    return $result;
}

function sa_exists() {
    global $link, $sid, $fid, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids;

    $gcal_result_2 = mysqli_query($link, "select * from selected_answers where sa_fid = '$fid' and sa_level = '$activity_level' and sa_group_id = '$peer_group_id'");
    if (mysqli_num_rows($gcal_result_2) > 0) {
        return true;
    }

    return false;
}