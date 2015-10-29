<?php
namespace User;

function get_student_username() {
    global $link;
    $sid = $_SESSION['student'];
    $res2 = mysqli_query($link, "select * from students where sid = '$sid'");
    if(mysqli_num_rows($res2) > 0){
        while($data2 = mysqli_fetch_assoc($res2)){
            $sname = $data2["sname"];
        }
        return $sname;
    }
}

function enforce_student_login() {
    if(!isset($_SESSION['student'])) {
        header("location: student_login.php");
        exit(0);
    }
}