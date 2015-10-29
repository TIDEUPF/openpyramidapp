<?php
session_start();
include_once('./lib/view.php');
include_once('./lib/action.php');
include_once('./lib/user.php');

$action = 'student_login';
if(isset($_REQUEST)) {
    $action = $_REQUEST['action'];
}