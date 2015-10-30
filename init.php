<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
ini_set("display_errors", 1);
ini_set('html_errors', true);

session_start();
include_once('./actions/dbvar.php');
include_once('./lib/view.php');
include_once('./lib/action.php');
include_once('./lib/user.php');

$action = 'student_login';
if(isset($_REQUEST)) {
    $action = $_REQUEST['action'];
}