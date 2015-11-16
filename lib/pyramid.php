<?php

namespace Pyramid;

function get_current_activity_level() {
    global $link, $sid, $fid, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids;

    //get the highest rated level for the user
    $gcal_result_1 = mysqli_query($link, "select * from flow_student_rating where fsr_sid = '$sid' and fsr_fid = '$fid' order by fsr_level desc limit 1 ");
    if(mysqli_num_rows($gcal_result_1) > 0)
    {
        $gcal_data_1 = mysqli_fetch_assoc($gcal_result_1);
        $activity_level = $gcal_data_1['fsr_level'];
    }
    else
    {
        $activity_level = 0;
    }

    return $activity_level;
}

function upgrade_level() {
    global $link, $sid, $fid, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids;

    if(!\Student\level_is_rated())
        return false;

    $upgrade = false;
    //check if the highest rated level has a selected answer
    $gcal_result_2 = mysqli_query($link, "select * from selected_answers where sa_fid = '$fid' and sa_level = '$activity_level' and sa_group_id = '$peer_group_id'");
    if(mysqli_num_rows($gcal_result_2) > 0)
    {
        $upgrade = true;
    }
    else
    {
        //here decide the criteria to allow to proceed to the next level
        $cgfl_temp = \Group\check_if_group_finished_level();

        if($cgfl_temp and !($activity_level == 0 and !\Student\level_is_rated()))
        {
            set_selected_answers();
            $upgrade = true;
        }
    }

    if($upgrade) {
        $activity_level++;
        \Group\get_members();
    }
}

function set_selected_answers() {
    global $link, $sid, $fid, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids;

    //to sum the ratings
    $ssa_result_1= mysqli_query($link, "SELECT fsr_to_whom_rated_id, skip, SUM(fsr_rating) as sum FROM `flow_student_rating` where fsr_fid = '$fid' and fsr_level = '$activity_level' and fsr_group_id = '$peer_group_id' group by fsr_to_whom_rated_id order by SUM(fsr_rating) desc limit 1");
    $ssa_data_1 = mysqli_fetch_assoc($ssa_result_1);

    $selected_id = $ssa_data_1['fsr_to_whom_rated_id'];
    $selected_id_rating_sum = $ssa_data_1['sum'];
    $skip = $ssa_data_1['skip'];
    mysqli_query($link,"insert into selected_answers values ('$fid', '$activity_level', '$peer_group_id', '$selected_id', '$selected_id_rating_sum', '$skip')");
}

/*
function get_level_info() {
    global $link, $sid, $fid, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids;
    $sa_result_1 = mysqli_query($link, "select * from pyramid_groups where pg_fid = '$fid' and pg_level = '$activity_level'");
    if(mysqli_num_rows($sa_result_1) > 0){
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
}
*/

function get_groups($params) {

}

function is_level_computed($params) {
    global $link, $sid, $fid, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $pre_task_completed = array();
    $activity_level_previous = $activity_level-1;

    //check if groups completed previous task
    foreach($peer_group_combined_ids_temp as $pgcid_group_id_temp){
        $sa_result_2 = mysqli_query($link, "select * from selected_answers where sa_fid = '$fid' and sa_level = '$activity_level_previous' and sa_group_id = '$pgcid_group_id_temp'");
        if(mysqli_num_rows($sa_result_2) > 0){
            $pre_task_completed[] = 1;
        }
    }

    if(count($peer_group_combined_ids_temp) == count($pre_task_completed))
        return true;

    return false;
}

/*
function is_level_completed() {
    global $link, $sid, $fid, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $needed_results = get_needed_results_to_end_level();

    $actual_result= mysqli_query($link, "select * from flow_student_rating where fsr_fid = '$fid' and fsr_level = '$activity_level' and fsr_group_id = '$peer_group_id'");

    if(mysqli_num_rows($actual_result) >= $needed_results) {
        if(\Group\is_level_timeout())
            return true;
    }

    return false;
}
*/

function get_needed_results_to_end_level() {
    global $link, $sid, $fid, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $group_size = count($peer_array); //no of peers in the branch
    if($activity_level == 1)
    {
        $needed_results = $group_size * ($group_size-1); //in the first level, it's no. of choices * student count
    }
    else {
        $st_count = count($peer_group_combined_ids_temp);
        $needed_results = $group_size * $st_count; //because now every student is rating two answers, need to occupy all answers
    }

    return $needed_results;
}

function compute_level_rating() {
    global $link, $sid, $fid, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $result_3= mysqli_query($link, "SELECT fsr_to_whom_rated_id, SUM(fsr_rating) as sum FROM `flow_student_rating` where fsr_fid = '$fid' and fsr_level = '$activity_level' and fsr_group_id = '$peer_group_id' group by fsr_to_whom_rated_id order by SUM(fsr_rating) desc limit 1");
    $data_t_2 = mysqli_fetch_assoc($result_3);

    $selected_id = $data_t_2['fsr_to_whom_rated_id'];
    $selected_id_rating_sum = $data_t_2['sum'];
    mysqli_query($link,"insert into selected_answers values ('$fid', '$activity_level', '$peer_group_id', '$selected_id', '$selected_id_rating_sum')");
}

function is_complete() {
    global $link, $sid, $fid, $levels, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    //last level-- to show selected answers
    if($activity_level == $levels) {
        //all users answered so proceed to show the final results
        if( mysqli_num_rows(mysqli_query($link, "select * from pyramid_groups where pg_fid = '$fid'")) == mysqli_num_rows(mysqli_query($link, "select * from selected_answers where sa_fid = '$fid'")) ) {
            return true;
        }
    }
    return false;
}

function get_current_level() {
    global $levels, $activity_level;

    $current_level = $activity_level+1;

    if($current_level > $levels)
        $current_level = $levels;

    return $current_level;
}

function show_final_answer() {
    global $link, $sid, $fid, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $activity_level_previous = $activity_level-1;
    $result_11 = mysqli_query($link, "select * from selected_answers where sa_fid = '$fid' and sa_level = '$activity_level_previous'");
    $answers = array();
    while ($data_t_11 = mysqli_fetch_assoc($result_11)) {
        $qa_last_selected_id = $data_t_11['sa_selected_id'];
        $result_12 = mysqli_query($link, "select * from flow_student where fid = '$fid' and sid = '$qa_last_selected_id'");
        $data_t_12 = mysqli_fetch_assoc($result_12);
        $answers[] = $data_t_12['fs_answer'];
    }
    \Answer\view_final_answer(array('final_answer_array' => $answers));

}

function get_current_flow() {
    global $levels, $fname, $fdes, $fid, $link, $ftimestamp;
    //get information the latest flow
    $res3 = mysqli_query($link, "select * from flow order by fid desc limit 1");
    if(mysqli_num_rows($res3) > 0){
        $data3 = mysqli_fetch_assoc($res3);
        $levels = $data3["levels"];
        $fname = $data3["fname"];
        $fdes = $data3["fdes"];
        $fid = $data3["fid"];
        $ftimestamp = (int)$data3["timestamp"];
        return $data3;
    }
    else{
        //throw new Exception("There are no flows");
        //show activity explanation
        $activity_explanation_view = \View\element("activity_explanation", array());

        \View\page(array(
            'title' => 'Activity explanation',
            'body' => $activity_explanation_view,
        ));

        exit;
    }
}

function wait($params) {
    global $link, $sid, $fid, $sname, $levels, $activity_level, $peer_group_id, $peer_array, $peer_group_combined_ids;

    $initial_level = $activity_level;
    upgrade_level();

    $vars = array(
        'username' 					=> $sname . ' + ' . (count(\Group\get_status_bar_peers())-1),
        'level' 				    => 'Level ' . \Pyramid\get_current_level() .'/' . $levels,
        'answer_text' 			=> 'Write a question',
        'answer_submit_button' 	=> 'Submit your question',
        'hidden_input_array' 		=> array(
            'a_lvl' 			=> $activity_level,
            'a_peer_group_id'	=> $peer_group_id,
        ),
    );

    if(count($peer_array) != count(get_inactive_level_group_peers())) {
        $vars['inactive_peers_count'] = max(count(get_inactive_level_group_peers()), 1);
    } else {
        $array_size = \Group\get_status_bar_groups_count();
        $rated_count = \Group\get_previous_groups_rated_count();
        $vars['inactive_groups_count'] = max($array_size - $rated_count, 1);
    }

    if(isset($params['error']))
        $vars['error'] = $params['error'];

    $body = \View\element("answer_waiting", $vars);

    \View\page(array(
        'title' => 'Question',
        'body' => $body,
    ));
    exit;
}

//remove users that didn't submit due the timeout in the previous level
function set_previous_level_peer_active_group_ids() {
    global $link, $sid, $fid, $sname, $levels, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    if(\Group\is_level_timeout())
        return false;

    if(!\Answer\is_submitted())
        return false;

    if(!\Group\check_if_previous_groups_completed_task())
        return false;

    $previous_activity_level = $activity_level-1;
    $peer_group_combined_ids_array = explode(',',$peer_group_combined_ids);
    $peer_group_combined_ids_sql = implode("','",\Util\sanitize_array($peer_group_combined_ids_array));
    $peer_array_sql = implode("','", \Util\sanitize_array($peer_array));

    if($previous_activity_level == -1)
        $submitted_group_answers_query = mysqli_query($link, "select distinct sid as active_sid from flow_student where fid = '{$fid}' and sid in ('{$peer_array_sql}')");
    else
        $submitted_group_answers_query = mysqli_query($link, "select distinct fsr_sid as active_sid from flow_student_rating where fsr_fid='{$fid}' and fsr_level='{$previous_activity_level}' and fsr_group_id in ('{$peer_group_combined_ids_sql}')");

    $active_ids = array();
    while ($rating = mysqli_fetch_assoc($submitted_group_answers_query)) {
        $active_ids[] = $rating['active_sid'];
    }

    if(count($active_ids)) {
        $active_ids_string = implode(',', $active_ids);
        mysqli_query($link, "update pyramid_groups set pg_group='{$active_ids_string}' where pg_fid='{$fid}' and pg_level='{$activity_level}' and pg_group_id='{$peer_group_id}'");
    }

    $peer_array = $active_ids;

    return $active_ids;
}

function get_inactive_level_group_peers() {
    global $link, $sid, $fid, $sname, $levels, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $peer_group_combined_ids_array = explode(',',$peer_group_combined_ids);
    $peer_array_sql = implode("','", \Util\sanitize_array($peer_array));

    if($activity_level == 0 and !\Student\level_is_rated())
        $inactive_peers_result = mysqli_query($link, "select distinct sid from students where sid in ('{$peer_array_sql}') and sid not in (select distinct sid as active_sid from flow_student where fid = '{$fid}' and sid  in ('{$peer_array_sql}'))");
    else
        $inactive_peers_result = mysqli_query($link, "select distinct sid from students where sid in ('{$peer_array_sql}') and sid not in (select distinct fsr_sid as active_sid from flow_student_rating where fsr_fid='{$fid}' and fsr_level='{$activity_level}' and fsr_group_id = '$peer_group_id')");

    $inactive_peers = array();
    while ($row = mysqli_fetch_assoc($inactive_peers_result)) {
        $inactive_peers[] = $row;
    }

    return $inactive_peers;
}
