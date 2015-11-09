<?php

Student\enforce_login();

$sname = Student\get_username();
$sid = $_SESSION['student'];

// $levels, $fname, $fdes, $fid, $fid_timestamp
\Pyramid\get_current_flow();

//$activity_level
\Pyramid\get_current_activity_level();

//$peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp
\Group\get_members();

//enter submitted information
\Answer\submit();
\Answer\submit_rate();

//TODO:improve the timeout thing
if(\Group\is_level_timeout()) {
    \Student\timeout_view();
    exit;
}

//new data entered
if(\Answer\new_data()) {
    //wait
    \Pyramid\wait();
    exit;
}

//wrong answer
if(\Answer\submit_error()) {
    //retry
    \Answer\retry();
    exit;
}

//delete inactive students from the current level
\Pyramid\set_previous_level_peer_active_group_ids();

if(\Pyramid\is_complete()) {
    \Pyramid\show_final_answer();
    exit;
}

if(!\Answer\is_submitted()) {
    \Answer\request();
    exit;
}

//we need the answers for other groups too
if(\Group\check_if_previous_groups_completed_task() and !\Student\level_is_rated()) {
    \Answer\request_rate();
    exit;
}

//wait
\Pyramid\wait();
exit;