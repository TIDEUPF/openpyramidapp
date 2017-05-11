<?php
global $url;

session_start();
$_SESSION['ldshake_guid'] = (int)$_REQUEST['ldshake_guid'];
session_write_close();

header("location: {$url}student.php");
