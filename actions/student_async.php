<?php

Student\enforce_login();

$sname = Student\get_username();
$sid = $_SESSION['student'];
global $fid;

// $levels, $fname, $fdes, $fid, $fid_timestamp
if(!\Pyramid\get_current_flow()) {
    //flow changed
}

\Pyramid\flow_add_student($fid, $sid);

\Util\log(['activity' => 'page_load']);

//TODO:check if it is manteinance time


//avoid race condition
$remaining_pyramids = \Pyramid\remaining_pyramids();
if(($pid = \Pyramid\get_student_pyramid($fid, $sid) === false)) {
    \Answer\submit();
    if ($remaining_pyramids and !\Answer\is_submitted()) {
        \Answer\request();
        exit;
    } else {
        /*\Pyramid\wait_pyramid();*/
        //TODO: answer_form_filled
        exit;
    }
}

//$activity_level
\Pyramid\get_current_activity_level();

//$peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp
\Group\get_members();

if(\Pyramid\is_complete()) {
    \Pyramid\show_final_answer();
    exit;
}

//this should not happen in async case
if(\Pyramid\is_final_level_complete()) {
    \Pyramid\wait();
    exit;
}

//check if the group has completed the level and upgrade the level
//this should not happen in async case
\Pyramid\upgrade_level();

//forced upgrade if hard timeout is reached
//this should not happen in async case
if(\Group\is_level_timeout()) {
    \Util\log(['activity' => 'level_timeout']);
    \Pyramid\upgrade_level(true);
}

//enter submitted information
\Answer\submit();
\Answer\submit_rate();

//new data entered
if(\Answer\is_new_data()) {
    if(\Answer\submit_error()) {
        //TODO: implement retry
        \Answer\retry();
        exit;
    } else {
        //this should not happen in async case
        //reload values
        \Pyramid\upgrade_level();
        \Group\get_members();

    }
}

//wrong answer
/*if(\Answer\submit_error()) {

    exit;
}
*/

//not needed, inclusive
//delete inactive students from the current level
//\Pyramid\set_previous_level_peer_active_group_ids();

if(\Pyramid\is_complete()) {
    \Pyramid\show_final_answer();
    exit;
}

if(!\Answer\is_timeout() and !\Answer\is_submitted()) {
    \Answer\request();
    exit;
}

//we need the answers for other groups too
/*if(\Group\check_if_previous_groups_completed_task() and !\Student\level_is_rated() and !\Group\sa_exists()) {
    if(\Answer\is_available_answers())
        \Answer\request_rate();
    else
        \Answer\skip_rating();
}

//wait
\Pyramid\wait();
*/
if(\Answer\is_available_answers())
    \Answer\request_rate();
else//TODO: screen telling users tomorrow they will continue
    \Answer\skip_rating();
exit;
