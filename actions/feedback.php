<?php
Student\enforce_login();

$sname = Student\get_username();
$sid = $_SESSION['student'];
global $fid, $link, $ps;

// $levels, $fname, $fdes, $fid, $fid_timestamp
$reset_flow = !\Pyramid\get_current_flow();

if(($pid = \Pyramid\get_student_pyramid($fid, $sid)) === false) {
}

mysqli_query($link, "insert into feedback values (null, '$fid', '$sid', 1)");
\Util\log(['activity' => 'feedback_clicked']);
header('Location: https://goo.gl/forms/Hp7Y25cDWwM9c8dK2');
exit;