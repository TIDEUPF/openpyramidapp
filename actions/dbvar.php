<?php
$mysql_host = "";
$mysql_database = "";
$mysql_user = "";
$mysql_password = "";

$link = mysqli_connect("$mysql_host", "$mysql_user", "$mysql_password") or die("Database is down for few minutes, Please try again.");
mysqli_set_charset($link, 'utf8');
mysqli_select_db($link, $mysql_database);

global $node_path, $email, $url;
$node_path = "socket.io";
$url="https://pyramidapp.edu/";

$email = [
    'address' => '',
    'username' => '',
    'password' => '',
    'host' => '',
    'port' => '',
];