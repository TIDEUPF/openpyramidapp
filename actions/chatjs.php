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

/* the location_id allows to use the same nodejs service for several instances of the application
 * */
$location_id = hash('crc32b', $_SERVER['SCRIPT_NAME']);

header('Content-Type: application/javascript; charset=utf-8');
$jsdata = [
    'username' => $sname,
    'room' => 'room_' . $fid . '_' . $peer_group_id . '_' . $activity_level . '_' . $pid . '_' . $location_id,
    'fid' => $fid,
    'pid' => $pid,
];

foreach($jsdata as $jskey => $jsvalue) {
    echo 'var ' . $jskey . ' = ' . json_encode($jsvalue) . ';' . PHP_EOL;
}
