<?php

namespace Group;

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

function get_needed_results_to_end_level($full_requirements = false) {
    global $link, $sid, $fid, $activity_level, $peer_array, $answer_submit_required_percentage, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp, $answer_required_percentage;

    $group_size = count($peer_array); //no of peers in the branch
    if($activity_level == 0 and !\Student\level_is_rated()) {
        $needed_results = count($peer_array);
        $status_percentage = $answer_submit_required_percentage;
    } elseif($activity_level == 0) {
        $needed_results = $group_size * $group_size; //in the first level, it's no. of choices * student count
        $status_percentage = $answer_submit_required_percentage;
    } else{
        $st_count = count($peer_group_combined_ids_temp);
        $needed_results = $group_size * $st_count; //because now every student is rating two answers, need to occupy all answers
        $status_percentage = $answer_required_percentage;
    }

    if(!$full_requirements) {
        $needed_results = floor($needed_results * $status_percentage / 100.0);
    }

    if(empty($needed_results))
        $needed_results = 1;

    return $needed_results;
}

function check_if_group_finished_level()
{
    global $link, $sid, $fid, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    if(!($activity_level == 0 and !\Student\level_is_rated())) {
        $cgfl_result_1 = mysqli_query($link, "select * from flow_student_rating where fsr_fid = '$fid' and fsr_level = '$activity_level' and fsr_group_id = '$peer_group_id'");
        $cgfl_result_1_count = mysqli_num_rows($cgfl_result_1);
    } else {
        $peer_array_sql = implode("','", \Util\sanitize_array($peer_array));

        $cgfl_result_1 = mysqli_query($link, "select * from flow_student where fid = '{$fid}' and sid in ('{$peer_array_sql}')");
        $cgfl_result_1_count = mysqli_num_rows($cgfl_result_1);
    }

    $needed_results = get_needed_results_to_end_level();
    $full_needed_results = get_needed_results_to_end_level(true);

    if($cgfl_result_1_count < $needed_results) {
        return false;
    } else {
        if(\Group\is_level_timeout() or $full_needed_results <= $cgfl_result_1_count)
            return true;
    }

    return false;
}

function check_if_previous_groups_completed_task()
{
    global $link, $sid, $fid, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $activity_level_previous = $activity_level-1;

    //check if every group member has submitted the answer or the timeout is expired
    if($activity_level_previous == -1 and \Answer\is_submitted()) {
        if(\Answer\is_timeout())
            return true;

        $peer_array_sql = implode("','", \Util\sanitize_array($peer_array));
        $n_answers_query = mysqli_query($link, "select * from flow_student where fid = '{$fid}' and sid in ('{$peer_array_sql}')");
        $n_answers = mysqli_num_rows($n_answers_query);
        $needed_results = count($peer_array);
        if($n_answers >= $needed_results)
            return true;
        else
            return false;
    } elseif($activity_level_previous == -1)
        return true;

    $peer_group_combined_ids_array = explode(",",$peer_group_combined_ids);
    $array_size = count($peer_group_combined_ids_array);

    //TODO: replace with get_previous_groups_rated_count()
    foreach($peer_group_combined_ids_array as $temp_id)
    {
        $sql1_ids[] = "sa_group_id = ".$temp_id;
    }

    $sql1 = implode(" or ", $sql1_ids);
    $cipgct_result_1 = mysqli_query($link, "select * from selected_answers where sa_fid = '$fid' and sa_level = '$activity_level_previous' and ($sql1) ");
    $cipgct_result_1_count = mysqli_num_rows($cipgct_result_1);

    if($array_size == $cipgct_result_1_count) {
        return true;
    }
    else {
        return false;
    }
}

function get_previous_groups_rated_count() {
    global $link, $sid, $levels, $fid, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    if($levels == $activity_level) {
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

function is_level_timeout() {
    global $timeout, $activity_level;

    if($activity_level == 0 and !\Answer\is_submitted() and !\Student\level_is_rated()) {
        return \Answer\is_timeout();
    }

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

function get_level_timeout_timestamp($fid, $activity_level, $peer_group_id) {
    global $link, $peer_group_id, $activity_level, $fid;

    $submitted_group_answers_timestamp_query = mysqli_query($link, "select * from pyramid_groups where pg_timestamp > 0 and pg_fid='{$fid}' and pg_level='{$activity_level}' and pg_group_id='{$peer_group_id}' order by pg_timestamp asc limit 1");
    if(mysqli_num_rows($submitted_group_answers_timestamp_query)) {
        $submitted_group_answers_timestamp_row_array = mysqli_fetch_assoc($submitted_group_answers_timestamp_query);
        return $submitted_group_answers_timestamp_row_array['pg_timestamp'];
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