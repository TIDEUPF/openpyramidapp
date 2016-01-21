<?php

Student\enforce_login();

$sname = Student\get_username();
$sid = $_SESSION['student'];

// $levels, $fname, $fdes, $fid, $fid_timestamp
if(!$fid = \Pyramid\get_current_flow()) {
    //no flow available
    exit;
}

\Pyramid\flow_add_student($fid, $sid);

//avoid race condition
$remaining_pyramids = \Pyramid\remaining_pyramids();
if(($pid = \Pyramid\get_student_pyramid($fid, $sid) === false)) {
    \Answer\submit();
    if ($remaining_pyramids and !\Answer\is_submitted()) {
        \Answer\request();
        exit;
    } else {
        \Pyramid\wait_pyramid();
        exit;
    }
}

//$activity_level
\Pyramid\get_current_activity_level();

//$peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp
\Group\get_members();

//check if the group has completed the level and upgrade the level
\Pyramid\upgrade_level();

//forced upgrade if hard timeout is reached
if(\Group\is_level_timeout()) {
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
        //reload values
        \Pyramid\upgrade_level();
        \Group\get_members();
    }
}

//wrong answer
if(\Answer\submit_error()) {

    exit;
}

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
if(\Group\check_if_previous_groups_completed_task() and !\Student\level_is_rated()) {
    if(\Answer\is_available_answers())
        \Answer\request_rate();
    else
        \Answer\skip_rating();
}

//wait
\Pyramid\wait();
exit;