<?php
//javascript vars controller for the chat application

Student\enforce_login();

$sname = Student\get_username();
$sid = $_SESSION['student'];


header('Content-Type: application/javascript; charset=utf-8');
$jsdata = [
    'username' => $sname,
];
echo '';