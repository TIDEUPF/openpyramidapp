<?php
Student\enforce_login();

$sname = Student\get_username();
$sid = $_SESSION['student'];

// $levels, $fname, $fdes, $fid, $fid_timestamp
$reset_flow = !\Pyramid\get_current_flow();

//$activity_level
\Pyramid\get_current_activity_level();

//$peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp
\Group\get_members();

//check if the group has completed the level and upgrade the level
\Pyramid\upgrade_level();

if(\Group\is_level_timeout())
    $expired = true;

$time_left = \Group\get_time_left();

$output = array(
    'reset' => $reset_flow,
    'expired' => !empty($expired),
    'countdown_started' => ($time_left > 0),
    'time_left' => (int)$time_left,
);

header('Connection: close');
header('Content-type: application/json');
echo json_encode($output);