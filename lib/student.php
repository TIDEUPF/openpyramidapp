<?php
namespace Student;

function get_student_details($student, $group_level) {
    global $ps, $peer_array;

    $group_id = \Group\get_student_group($student);
    set_activity_level($group_level, $group_id);

    //is started?
    $group_level_started = is_level_started();

    //is finished
    $group_is_finished = is_level_started() and is_level_timeout();

    //ratings
    $group_ratings = get_group_ratings();

    //group_users
    $group_users = $peer_array;

    //chat messages
    $chat_messages = get_student_chat();

    //pyramid creation timestamp
    $answer_timeout_data = \Answer\get_answer_timeout();
    $pyramid_creation_timestamp = (int)$answer_timeout_data['start_timestamp'];


    $student_activity = [
        'group_level_started' => $group_level_started,
        'group_is_finished' => $group_is_finished,
        'group_ratings' => $group_ratings,
        'group_users' => $group_users,
        'chat_messages' => $chat_messages,
        'pyramid_creation_timestamp' => $pyramid_creation_timestamp,
    ];

    return $student_activity;
}

function get_student_answer($sid) {
    global $link, $sid, $fid, $ps, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $sql = <<< SQL
select 
fs_answer as `answer`,
`skip`,
`timestamp`
from flow_student
where {$ps['e']} and
sid = '{$sid}' and
SQL;

    $answer = \Util\exec_sql($sql);

    return $answer;
}

function get_student_ratings($sid) {
    global $link, $sid, $fid, $ps, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $sql = <<< SQL
select 
fsr_sid as `sid`, 
fsr_level as `level`,
fsr_group_is as `group_id`,
fsr_rating as `rating`,
fsr_to_whom_rated_id as `question_id`,
UNIX_TIMESTAMP(fsr_datetime) as `timestamp`
from flow_student_rating 
where {$ps['fsr']} and
fsr_sid = '{$sid}' and
fsr_to_whom_rated_id <> -1
order by fsr_level asc, fsr_to_whom_rated_id asc
SQL;

    $ratings = \Util\exec_sql($sql);

    return $ratings;
}

function get_student_chat() {
    global $link, $sid, $fid, $pid, $ps, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $room = \Util\get_room_string($fid, $pid, $activity_level, $peer_group_id);

    $sql = <<< SQL
select 
`sid`, 
`message`,
`room`,
UNIX_TIMESTAMP(`date`) as `timestamp`
from chat
where {$ps['e']} and
`room` LIKE '{$room}'
order BY `date` asc
SQL;

    $messages = \Util\exec_sql($sql);
    $level_messages = [];

    foreach($messages as $message_item) {
        $room_id = explode('_', $message_item);
        unset($message_item['room']);
        $level_messages[$room_id[3]] = $message_item;
    }

    return $level_messages;
}

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

function get_student_level_activity($sid, $group_id, $level) {
    global $link, $sid, $fid, $pid, $ps, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    //ratings
    $ratings_sql = <<< SQL
select sid as username, fsr_rating, fsr_to_whom_rated_id, fsr_datetime
from flow_student_rating
where {$ps['fsr']} 
and fsr_level = {$level}
and sid = '{$sid}'
and fsr_to_whom_rated_id <> '-1'
SQL;

    $ratings = \Util\exec_sql($ratings_sql);

    //chat
    $room = \Util\get_room_string($fid, $pid, $level, $group_id);
    $chat_sql = <<< SQL
select sid as `username`, `message`, `date`
from chat
where {$ps['e']} 
and room = {$room}
and sid = '{$sid}'
and fsr_to_whom_rated_id <> '-1'
SQL;

    $chat_messages = \Util\exec_sql($chat_sql);

    return [
        'ratings' => $ratings,
        'chat_messages' => $chat_messages,
    ];
}
