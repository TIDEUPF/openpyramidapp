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

//check if the group has completed the level and upgrade the level
\Pyramid\upgrade_level();

if(\Group\is_level_timeout()) {
    \Student\timeout_view();
    exit;
}

//enter submitted information
\Answer\submit();
\Answer\submit_rate();

//new data entered
if(\Answer\is_new_data()) {
    //wait
    \Pyramid\wait();
    exit;
}

//wrong answer
if(\Answer\submit_error()) {
    //TODO: implement retry
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
    if(\Answer\is_available_answers())
        \Answer\request_rate();
    else
        \Answer\skip_rating();
}

//wait
\Pyramid\wait();
exit;