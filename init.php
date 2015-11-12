<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
ini_set("display_errors", 1);
ini_set('html_errors', true);

session_start();

include_once('./config.php');
include_once('./actions/dbvar.php');
include_once('./lib/util.php');
include_once('./lib/view.php');
include_once('./lib/action.php');

include_once('./lib/group.php');
include_once('./lib/pyramid.php');
include_once('./lib/answer.php');
include_once('./lib/student.php');
include_once('./lib/request.php');

init_cfg();

$action = 'student_login';
if(isset($_REQUEST)) {
    $action = $_REQUEST['action'];
}