<?php
global $url;
$_SESSION['ldshake_guid'] = (int)$_REQUEST['ldshake_guid'];

header("location: {$url}student.php");
