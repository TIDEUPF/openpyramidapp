<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
ini_set("display_errors", 1);
ini_set('html_errors', true);

session_start();

include_once(__DIR__ . '/config.php');
include_once(__DIR__ . '/actions/dbvar.php');
include_once(__DIR__ . '/lib/ldshake.php');
include_once(__DIR__ . '/lib/util.php');
include_once(__DIR__ . '/lib/view.php');
include_once(__DIR__ . '/lib/action.php');

include_once(__DIR__ . '/lib/group.php');
include_once(__DIR__ . '/lib/pyramid.php');
include_once(__DIR__ . '/lib/flow.php');
include_once(__DIR__ . '/lib/answer.php');
include_once(__DIR__ . '/lib/student.php');
include_once(__DIR__ . '/lib/request.php');
include_once(__DIR__ . '/lib/inc_pyramid_func.php');
include_once(__DIR__ . '/lib/translation.php');

init_cfg();

$action = 'student_login';
if(isset($_REQUEST)) {
    $action = $_REQUEST['action'];
}