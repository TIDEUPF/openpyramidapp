<?php
//javascript vars controller for the chat application
Student\enforce_login();
$sname = Student\get_username();
$sid = $_SESSION['student'];

Student\enforce_login();
\Pyramid\get_current_flow();

global $fid;
if(($pid = \Pyramid\get_student_pyramid($fid, $sid)) === false) {
    //TODO: error
}

//$activity_level
\Pyramid\get_current_activity_level();

//$peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp
\Group\get_members();
global $peer_group_id, $activity_level;

header('Content-Type: application/javascript; charset=utf-8');
$jsdata = [
    'username' => $sname,
    //TODO: filter by url
    'room' => 'room_' . $fid . '_' . $peer_group_id . '_' . $activity_level . '_' . $pid,
    'fid' => $fid,
    'pid' => $pid,
];

foreach($jsdata as $jskey => $jsvalue) {
    echo 'var ' . $jskey . ' = ' . json_encode($jsvalue) . ';' . PHP_EOL;
}
