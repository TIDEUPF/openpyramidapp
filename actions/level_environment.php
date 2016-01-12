<?php
Student\enforce_login();

$sname = Student\get_username();
$sid = $_SESSION['student'];
global $fid;

// $levels, $fname, $fdes, $fid, $fid_timestamp
$reset_flow = !\Pyramid\get_current_flow();

//without pyramid cannot apply the timers
if(($pid = \Pyramid\get_student_pyramid($fid, $sid) === false)) {
    $output = array(
        'reset' => false,
        'expired' => false,
        'countdown_started' => false,
        'time_left' => 9999999,
        'a_lvl' => 0,
        'rating' => false,
    );

    header('Connection: close');
    header('Content-type: application/json');
    echo json_encode($output);
    exit;
}

global $activity_level;
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
    'a_lvl' => (int)$activity_level,
    'rating' => \Answer\is_timeout(),
);

header('Connection: close');
header('Content-type: application/json');
echo json_encode($output);