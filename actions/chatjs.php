<?php
//javascript vars controller for the chat application
Student\enforce_login();
$sname = Student\get_username();
$sid = $_SESSION['student'];

Student\enforce_login();
\Pyramid\get_current_flow();

//$activity_level
\Pyramid\get_current_activity_level();

//$peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp
\Group\get_members();
global $fid, $peer_group_id, $activity_level;

header('Content-Type: application/javascript; charset=utf-8');
$jsdata = [
    'username' => $sname,
    'room' => 'room_' . $fid . '_' . $peer_group_id . '_' . $activity_level,
    'chat_url' => 'http://localhost:8000',
    'chat_url' => 'http://sos.gti.upf.edu',
];

foreach($jsdata as $jskey => $jsvalue) {
    echo 'var ' . $jskey . ' = ' . json_encode($jsvalue) . ';' . PHP_EOL;
}
