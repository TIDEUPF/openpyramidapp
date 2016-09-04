<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
ini_set("display_errors", 1);
ini_set('html_errors', true);

session_start();

include 'libs.php';
init_cfg();

$action = 'student_login';
if(isset($_REQUEST)) {
    $action = $_REQUEST['action'];
}