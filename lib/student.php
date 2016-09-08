<?php
namespace Student;

function get_username() {
    global $link, $sid, $sname;

    if(empty($_SESSION['student']))
        return false;

    $sid = $_SESSION['student'];
    $res2 = mysqli_query($link, "select * from students where sid = '$sid'");
    if(mysqli_num_rows($res2) > 0){
        while($data2 = mysqli_fetch_assoc($res2)){
            $sname = $data2["sname"];
        }
        return $sname;
    }

    return false;
}

function enforce_login() {

    if(!\Student\get_username()) {
        unset($_SESSION['student']);
        unset($_SESSION['sname']);
    }

    if(!isset($_SESSION['student'])) {
        header("location: student_login.php");
        exit(0);
    }
}

function level_is_rated() {
    global $link, $sid,  $fid, $ps, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids;

    $result = mysqli_query($link, "select * from flow_student_rating where {$ps['fsr']} and fsr_sid= '$sid' and fsr_level = '$activity_level'");
    if(mysqli_num_rows($result) > 0) {
        return true;
    }

    return false;
}

function get_rating($answer_id) {
    global $link, $sid,  $fid, $ps, $activity_level, $peer_group_id;

    $answer_rating_result = mysqli_query($link, "select * from flow_student_rating where {$ps['fsr']} and fsr_sid= '$sid' and fsr_level = '$activity_level' and fsr_group_id = '{$peer_group_id}' and fsr_to_whom_rated_id='{$answer_id}'");
    if(mysqli_num_rows($answer_rating_result) > 0) {
        $answer_rating_result_array = mysqli_fetch_assoc($answer_rating_result);

        return $answer_rating_result_array['fsr_rating'];
    }
    return false;
}

function timeout_view($params) {
    global $link, $sid,  $fid, $ps, $sname, $levels, $activity_level, $peer_group_id;

    $vars = array(
        'username' 					=> $sname,
        'level' 					=> T('Level') . ' ' . $activity_level . '/' . $levels,
        'answer_text' 			=> 'Write a question',
        'answer_submit_button' 	=> 'Submit your question',
        'hidden_input_array' 		=> array(
            'a_lvl' 			=> $activity_level,
            'a_peer_group_id'	=> $peer_group_id,
        ),
    );

    if(isset($params['error']))
        $vars['error'] = $params['error'];

    $body = \View\element("timeout_view", $vars);

    \View\page(array(
        'title' => 'Question',
        'body' => $body,
    ));
    exit;
}
