<?php
namespace Student;

function get_username() {
    global $link, $sid, $sname;

    $sid = $_SESSION['student'];
    $res2 = mysqli_query($link, "select * from students where sid = '$sid'");
    if(mysqli_num_rows($res2) > 0){
        while($data2 = mysqli_fetch_assoc($res2)){
            $sname = $data2["sname"];
        }
        return $sname;
    }
}

function enforce_login() {
    if(!isset($_SESSION['student'])) {
        header("location: student_login.php");
        exit(0);
    }
}

function level_is_rated() {
    global $link, $sid, $fid, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids;

    $sa_result_4 = mysqli_query($link, "select * from flow_student_rating where fsr_fid = '$fid' and fsr_sid= '$sid' and fsr_level = '$activity_level'");
    if(mysqli_num_rows($sa_result_4) > 0) {
        return true;
    }

    return false;
}