<?php
Student\enforce_login();

$sname = Student\get_username();
$sid = $_SESSION['student'];
$client_level = (int)$_REQUEST['level'];

global $fid, $link, $ps, $flow_data;

// $levels, $fname, $fdes, $fid, $fid_timestamp
$reset_flow = !\Pyramid\get_current_flow();

//without pyramid cannot apply the timers
if(($pid = \Pyramid\get_student_pyramid($fid, $sid)) === false) {
    $output = array(
        'reset' => $reset_flow,
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

if((int)$flow_data['sync'] == 0) {
    $output = array(
        'reset' => $reset_flow,
        'expired' => false,
        'countdown_started' => false,
        'time_left' => 9999999,
        'a_lvl' => 0,
        'rating' => false,
    );

    if($reset_flow)
        $output['reset'] = true;

    $client_level = (int)$_REQUEST['level'] - 1;
    $result = mysqli_query($link, "select * from selected_answers where {$ps['sa']} and sa_level = {$client_level}");
    if (mysqli_num_rows($result) > 0) {
         $output['expired'] = true;
    }

    header('Connection: close');
    header('Content-type: application/json');
    echo json_encode($output);
    exit;
}

global $activity_level, $levels;
\Pyramid\get_current_activity_level();

//$peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp
\Group\get_members();

//check if the group has completed the level and upgrade the level
\Pyramid\upgrade_level();

if($client_level == -1) {
    if(\Group\is_level_zero_rating_started()) {
        $expired = true;
    }
} else {
   if ($activity_level > $client_level)
        $expired = true;

   if ($activity_level + 1 == $levels and \Pyramid\is_complete())
        $expired = true;
}

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