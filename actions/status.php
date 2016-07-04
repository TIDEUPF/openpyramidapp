<?php
Student\enforce_login();

$sname = Student\get_username();
$sid = $_SESSION['student'];
global $fid, $flow_data;

// $levels, $fname, $fdes, $fid, $fid_timestamp
if(\Pyramid\get_current_flow() === -1) {
    //no flow
    //\Flow\wait_flow();
}

global $activity_level, $levels;
$answer_submitted = \Answer\is_submitted();

\Pyramid\get_student_pyramid($fid, $sid);

//$activity_level
\Pyramid\get_current_activity_level();

//$peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp
\Group\get_members();

//rating has started(even if still is not submitted by anyone)
$rating_started = \Pyramid\is_rating_started();

$n_inactive_peers = count(\Pyramid\get_inactive_level_group_peers()) - 1;

$flow_timestamps = \Flow\get_timestamps();

$timestamp_level = $activity_level + 1 - 1*((int)(!$rating_started));
$ui_level = $activity_level + 2 - 1*((int)(!$rating_started));

$time_remaining = $flow_timestamps[$timestamp_level] - time();

$question_submitted = \Answer\is_submitted();

$params = [
    'n_inactive_peers'  => $n_inactive_peers,
    'levels'  => $levels + 1,
    'level'             => $ui_level,
    'time_remaining'    => $time_remaining,
    'question_submitted'    => $question_submitted,
];

\Pyramid\activity_status($params);