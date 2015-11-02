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
    if($activity_level == 1)
    {
        $needed_results = $group_size * ($group_size-1); //in the first level, it's no. of choices * student count
    }
    else{
        $st_count = count($peer_group_combined_ids_temp);
        $needed_results = $group_size * $st_count; //because now every student is rating two answers, need to occupy all answers
    }

    return $needed_results;
}

function check_if_group_finished_level($fid, $lvl, $peer_group_id, $needed_results, $mysql_link)
{
    global $link, $sid, $fid, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $cgfl_result_1= mysqli_query($link, "select * from flow_student_rating where fsr_fid = '$fid' and fsr_level = '$lvl' and fsr_group_id = '$peer_group_id'");
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