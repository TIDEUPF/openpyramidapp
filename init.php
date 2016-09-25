<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
ini_set("display_errors", 1);
ini_set('html_errors', true);

//set the session variables
session_start(/*['read_and_close' => true]*/);
session_write_close();

include 'libs.php';
init_cfg();

$action = 'student_login';
if(isset($_REQUEST)) {
    $action = $_REQUEST['action'];
}

global $pyramid_jscache_break;
$pyramid_dir = dirname(__FILE__);
$js_source = array(
    '/chat/client/chat.js',
    '/chat/client/embedded.css',
    '/resources/css/teacher/styles.css',
    '/lib/actions.js',
);

$js_modified_string = '';
foreach($js_source as $js) {
    $js_modified = filemtime($pyramid_dir . $js);
    $js_modified_string .= "{$js_modified}";
}
$pyramid_jscache_break = hash("crc32b", $js_modified_string);
