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

function get_needed_results_to_end_level() {
    global $link, $sid, $fid, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $group_size = count($peer_array); //no of peers in the branch
    if($activity_level == 0)
    {
        $needed_results = $group_size * ($group_size); //in the first level, it's no. of choices * student count
    }
    else{
        $st_count = count($peer_group_combined_ids_temp);
        $needed_results = $group_size * $st_count; //because now every student is rating two answers, need to occupy all answers
    }
    return $needed_results;
}

function check_if_group_finished_level()
{
    global $link, $sid, $fid, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $cgfl_result_1= mysqli_query($link, "select * from flow_student_rating where fsr_fid = '$fid' and fsr_level = '$activity_level' and fsr_group_id = '$peer_group_id'");
    $cgfl_result_1_count = mysqli_num_rows($cgfl_result_1);

    $needed_results = get_needed_results_to_end_level();

    if($cgfl_result_1_count != $needed_results)
    {
        return false;
    }
    else
    {
        return true;
    }
}

function check_if_previous_groups_completed_task()
{
    global $link, $sid, $fid, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $activity_level_previous = $activity_level-1;

    if($activity_level_previous == -1)
        return true;

    $peer_group_combined_ids_array = explode(",",$peer_group_combined_ids);
    $array_size = count($peer_group_combined_ids_array);
    foreach($peer_group_combined_ids_array as $temp_id)
    {
        $sql1_ids[] = "sa_group_id = ".$temp_id;
    }

    $sql1 = implode(" or ", $sql1_ids);
    $cipgct_result_1 = mysqli_query($link, "select * from selected_answers where sa_fid = '$fid' and sa_level = '$activity_level_previous' and ($sql1) ");
    $cipgct_result_1_count = mysqli_num_rows($cipgct_result_1);

    if($array_size == $cipgct_result_1_count)
    {
        return true;
    }
    else
    {
        return false;
    }

    return $result;
}

function is_level_timeout() {
    global $timeout;

    if(is_level_minimun_required_answers_reached()) {
        if(!$timestamp = get_level_timeout_timestamp()) {
            $timestamp = set_level_timeout_timestamp();
        }

        if(time() > $timestamp + $timeout)
            return true;
    }

    return false;
}

function is_level_minimun_required_answers_reached() {
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
    mysqli_query("update pyramid_groups set pg_timestamp='{$timestamp}' where pg_fid='{$fid}' and pg_level='{$activity_level}' and pg_group_id='{$peer_group_id}'");
}

function get_level_timeout_timestamp() {
    global $link, $sid, $fid, $sname, $levels, $activity_level, $peer_group_id;

    $submitted_group_answers_timestamp_query = mysqli_query($link, "select * from pyramid_groups where pg_timestamp > 0 and pg_fid='{$fid}' and pg_level='{$activity_level}' and pg_group_id='{$peer_group_id}' order by pg_timestamp asc limit 1");
    if(mysqli_num_rows($submitted_group_answers_timestamp_query)) {
        $submitted_group_answers_timestamp_row_array = mysqli_fetch_assoc($submitted_group_answers_timestamp_query);
        return $submitted_group_answers_timestamp_row_array['pg_timestamp'];
    } else {
        return FALSE;
    }
}
