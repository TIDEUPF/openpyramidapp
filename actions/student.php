<?php

Student\enforce_login();

$sname = Student\get_username();
$sid = $_SESSION['student'];

// $levels, $fname, $fdes, $fid
\Pyramid\get_current_flow();

//enter submitted information
\Answer\submit();
\Answer\submit_rate();

//$activity_level
//$peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp
\Pyramid\get_current_activity_level();
//$peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp
\Group\get_members();

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