<?php
session_start();

if(!empty($_SESSION['student'])){
    unset($_SESSION['fid']);
    unset($_SESSION['student']);
    unset($_SESSION['user']);
    header("location:student_login.php");
    exit(0);
}

if(!empty($_SESSION['user'])){
    unset($_SESSION['user']);
    unset($_SESSION['student']);
    unset($_SESSION['fid']);
    header("location:login.php");
    exit(0);
}
elseif(!empty($_SESSION['student'])){
    unset($_SESSION['student']);
    unset($_SESSION['student']);
    unset($_SESSION['fid']);
    header("location:student_login.php");
    exit(0);
}
header("location:student_login.php");
exit(0);