<?php

namespace Flow;

function add_student() {
    global $sid, $fid, $link;

    $result = mysqli_query($link,"insert into flow_students values ('', '$fid', '$sid')");

    return !!mysqli_affected_rows($result);
}

function wait_flow($params) {
    global $link, $sid,  $fid, $ps, $sname, $levels, $activity_level, $peer_group_id, $peer_array, $peer_group_combined_ids;

    $initial_level = $activity_level;
    //upgrade_level();

    $vars = array(
        'username' 					=> $sname,
        'level' 				    => 'Level 1' . '/' . $levels,
        'answer_text' 			=> 'Write a question',
        'answer_submit_button' 	=> 'Submit your question',
        'hidden_input_array' 		=> array(
            'a_lvl' 			=> $activity_level,
            'a_peer_group_id'	=> $peer_group_id,
        ),
    );

    $hidden_input_array['username'] = $sname;
    $hidden_input_array['fid'] = $fid;
    $hidden_input_array['level'] = 1;
    $hidden_input_array['page'] = "flow_creation_waiting";

    $vars['hidden_input_array'] = array_merge($vars['hidden_input_array'], $hidden_input_array);

    $body = \View\element("flow_waiting", $vars);

    \View\page(array(
        'title' => 'Question',
        'body' => $body,
    ));
    exit;
}
/*
 * Reports the last set of unexpired pyramids timestamps.
 */
function get_timestamps() {
    global $flow_data;

    $init_day = get_last_pyramid_expired_timestamp();
    $submission_timer = (int)$flow_data['question_timeout'];
    $rating_timer = (int)$flow_data['rating_timeout'];

    $level_timestamps = [
        $init_day + $submission_timer,
        $init_day + $submission_timer + 1*$rating_timer,
        $init_day + $submission_timer + 2*$rating_timer,
        $init_day + $submission_timer + 3*$rating_timer,
        $init_day + $submission_timer + 4*$rating_timer,
    ];

    return $level_timestamps;
}

function end_date_string($timestamp_level) {
    $timestamps = get_timestamps();

    $start_timestamp = $timestamps[$timestamp_level];

    $date_string = date("l jS G:i", $start_timestamp);

    return $date_string;
}

function get_not_full_pyramids() {
    global $link, $sid,  $fid, $flow_data, $ps, $sname, $levels, $activity_level, $peer_group_id, $peer_array, $peer_group_combined_ids;

    $submission_timer = (int)$flow_data['question_timeout'];
    $rating_timer = (int)$flow_data['rating_timeout'];

    $max_users = (int)$flow_data['pyramid_minsize'] * 2 - 1;
    $min_timestamp = \Util\pyramid_time() - $rating_timer*$levels;

    $sql = <<<SQL
select * from
(
select pid, MIN(timestamp) timestamp, count(distinct sid) user_count 
from pyramid_students 
where fid = {$fid} group by pid
) pts where
timestamp > {$min_timestamp} and user_count < {$max_users}
SQL;

    $r_start = mysqli_query($link, $sql);

    if(!(mysqli_num_rows($r_start) > 0))
        return [];

    $available_pyramids = [];
    while($pyramid_row = mysqli_fetch_assoc($r_start)) {
        $available_pyramids[] = [
            'pid' => (int)$pyramid_row['pid'],
            'slots' => $max_users - (int)$pyramid_row['user_count']
        ];
    }

    return $available_pyramids;
}

function get_available_students() {
    global $link, $sid,  $fid, $flow_data, $ps, $sname, $levels, $activity_level, $peer_group_id, $peer_array, $peer_group_combined_ids;

    $result_avail = mysqli_query($link, "select distinct * from flow_available_students where fid='$fid' and sid not in (select sid from pyramid_students where fid = '$fid')");

    $users = [];

    while ($user = mysqli_fetch_assoc($result_avail))
        $users[] = $user['sid'];

    return $users;
}

function get_last_pyramid_expired_timestamp() {
    global $link, $sid,  $fid, $flow_data, $ps, $sname, $levels, $activity_level, $peer_group_id, $peer_array, $peer_group_combined_ids;

    $rating_timer = (int)$flow_data['rating_timeout'];
    $max_users = (int)$flow_data['pyramid_minsize'] * 2 - 1;

    //last created pyramid timestamp
    $sql = <<<SQL
select * from
(
select MIN(timestamp) created_timestamp, pid, count(distinct sid) user_count 
from pyramid_students pst
where fid = {$fid}
group by pid
having user_count < {$max_users}
) pts 
order by created_timestamp DESC limit 1
SQL;

    $r_start = mysqli_query($link, $sql);

    if(!(mysqli_num_rows($r_start) > 0)) {
        $created_timestamp = null;
    } else {
        $last_user_row = mysqli_fetch_assoc($r_start);
        $created_timestamp = (int)$last_user_row['created_timestamp'];
    }

    //last filled pyramid timestamp
    $sql = <<<SQL
select * from
(
select MAX(timestamp) last_user_timestamp, count(distinct sid) user_count 
from pyramid_students pst
where fid = {$fid}
group by pid
having user_count >= {$max_users}
) pts
order by last_user_timestamp desc
limit 1
SQL;

    $r_start = mysqli_query($link, $sql);

    if(!(mysqli_num_rows($r_start) > 0)) {
        //no pyramids filled
        $filled_timestamp = null;
    } else {
        $last_user_row = mysqli_fetch_assoc($r_start);
        $filled_timestamp = (int)$last_user_row['last_user_timestamp'];

        //the filled_timestamp pertains to an older batch of pyramids
        if($filled_timestamp < $created_timestamp)
            $filled_timestamp = null;
    }

    if(!$filled_timestamp and !$created_timestamp) {
        //no pyramids created
        return (int)$flow_data['start_timestamp'];
    }

    if(!$created_timestamp) {
        return $filled_timestamp;
    }

    if($filled_timestamp and ($filled_timestamp - $levels * $rating_timer) > $created_timestamp) {
        //the last created unfilled pyramid was expired when $filled_timestamp one was created
        return $filled_timestamp;
    }

    //both timestamps are from the same batch of pyramids
    $last_expired_timestamp = $filled_timestamp ? max($created_timestamp + $rating_timer * $levels, $filled_timestamp) : $created_timestamp + $rating_timer * $levels;

    return $last_expired_timestamp;
}

function get_pyramid_ids()
{
    global $link, $sid, $fid, $flow_data, $ps, $sname, $levels, $activity_level, $peer_group_id, $peer_array, $peer_group_combined_ids;

    $result = mysqli_query($link, "select distinct pg_pid as pid from pyramid_groups where pg_fid='$fid' group by pg_pid");
    $pyramid_ids = [];
    while ($pyramid_row = mysqli_fetch_assoc($result)) {
        $pyramid_ids[] = (int)$pyramid_row['pid'];
    }

    return $pyramid_ids;
}

function get_last_pyramid_id()
{
    global $link, $sid, $fid, $flow_data, $ps, $sname, $levels, $activity_level, $peer_group_id, $peer_array, $peer_group_combined_ids;

    $result = mysqli_query($link, "select pg_pid as pid from pyramid_groups where pg_fid='$fid' order by pg_pid desc limit 1");
     if($pyramid_row = mysqli_fetch_assoc($result)) {
        return (int)$pyramid_row['pid'];
    }

    return false;
}

function set_fid($new_fid) {
    global $levels, $fname, $fdes, $fid, $ps, $n_selected_answers, $random_selection, $link, $ftimestamp, $flow_data, $timeout, $answer_timeout, $pyramid_size, $pyramid_minsize;

    //get information the latest flow
    $new_fid = (int)$new_fid;
    $res3 = mysqli_query($link, "select * from flow where fid = '{$new_fid}' limit 1");
    if(mysqli_num_rows($res3) > 0){
        $data3 = mysqli_fetch_assoc($res3);
        $flow_data = $data3;
        $levels = $data3["levels"];
        $fname = $data3["fname"];
        $fdes = $data3["fdes"];
        $fid = $data3["fid"];
        $ftimestamp = (int)$data3["timestamp"];
        $pyramid_size = (int)$data3["pyramid_size"];
        $pyramid_minsize = (int)$data3["pyramid_minsize"];
        $timeout = (int)$data3["rating_timeout"];
        $answer_timeout = (int)$data3["question_timeout"];
        $n_selected_answers = (int)$data3["n_selected_answers"];
        $random_selection = (int)$data3["random_selection"];

        return true;
    } else {
        return false;
    }
}

function get_available_students_with_question($new_pyramid_size, $excluded_students = array()) {
    global $levels, $fname, $fdes, $fid, $ps, $n_selected_answers, $random_selection, $link, $ftimestamp, $flow_data, $timeout, $answer_timeout, $pyramid_size, $pyramid_minsize;

    $excluded_sql = "";

    if(count($excluded_students)) {
        $excluded_string = implode('\',\'', $excluded_students);
        $excluded_sql = " and sid not in ('{$excluded_string}') ";
    }

    //select available flow students
    $result = mysqli_query($link, "select distinct * from flow_available_students where fid='$fid' and sid not in(select sid from pyramid_students where fid = '$fid') and sid in(select sid from flow_student where fid = '$fid') {$excluded_sql} limit {$new_pyramid_size}");

    if(!mysqli_num_rows($result))
        $students = [];
    else {
        while($result_row = mysqli_fetch_assoc($result)) {
            $students[] = $result_row['sid'];
        }
    }

    return $students;
}

function get_available_students_without_question($new_pyramid_size) {
    global $levels, $fname, $fdes, $fid, $ps, $n_selected_answers, $random_selection, $link, $ftimestamp, $flow_data, $timeout, $answer_timeout, $pyramid_size, $pyramid_minsize;

    //select available flow students
    $result = mysqli_query($link, "select distinct * from flow_available_students where fid='$fid' and sid not in(select sid from pyramid_students where fid = '$fid') and sid not in(select sid from flow_student where fid = '$fid') limit {$new_pyramid_size}");

    if(!mysqli_num_rows($result))
        $students = [];
    else {
        while($result_row = mysqli_fetch_assoc($result)) {
            $students[] = $result_row['sid'];
        }
    }

    return $students;
}

function get_flow_default_fields() {
    $flow_fields = [
        'activity',
        'task_description',
        'learning_setting',
        'discussion',
        'expected_students',
        'first_group_size',
        'n_levels',
        'multiple_pyramids',
        'min_students_per_pyramid',
        'satisfaction',
        's_question',
        'h_question',
        's_rating',
        'h_rating',
        'sync',
        'random_selection',
        'n_selected_answers'
    ];

    return $flow_fields;
}

function get_default_field_values() {
    $defaults = [
        'async' => [
            'discussion' => [
                'sync' => 0,
                's_question' => 24*3600,
                'h_question' => 36*3600,
                's_rating' => 24*3600,
                'h_rating' => 36*3600,
                'satisfaction' => 60,
                'discussion' => 1,
                'n_selected_answers' => 1,
                'random_selection' => 1,
                'n_levels' => 3,
                'first_group_size' => 4,
                'multiple_pyramids' => 1,
            ],
            'no_discussion' => [
                'sync' => 0,
                's_question' => 24*3600,
                'h_question' => 36*3600,
                's_rating' => 24*3600,
                'h_rating' => 2*24*3600,
                'satisfaction' => 60,
                'discussion' => 0,
                'n_selected_answers' => 1,
                'random_selection' => 1,
                'n_levels' => 3,
                'first_group_size' => 4,
                'multiple_pyramids' => 1,
            ],
        ],
        'sync' => [
            'discussion' => [
                'sync' => 1,
                's_question' => 120,
                'h_question' => 240,
                's_rating' => 180,
                'h_rating' => 300,
                'satisfaction' => 60,
                'discussion' => 1,
                'n_selected_answers' => 1,
                'random_selection' => 1,
                'n_levels' => 3,
                'first_group_size' => 4,
                'multiple_pyramids' => 1,
            ],
            'no_discussion' => [
                'sync' => 1,
                's_question' => 120,
                'h_question' => 240,
                's_rating' => 60,
                'h_rating' => 180,
                'satisfaction' => 60,
                'discussion' => 0,
                'n_selected_answers' => 1,
                'random_selection' => 1,
                'n_levels' => 3,
                'first_group_size' => 4,
                'multiple_pyramids' => 1,
            ],
        ],
    ];

    return $defaults;
}

function get_flow_status() {
    global $fid;

    $properties = [];

    //available students
    $sql = <<<SQL
select fid, sid, `timestamp` from flow_available_students
where fid = {$fid}
SQL;

    $properties['available_students'] = \Util\exec_sql($sql);

    //pyramids
    $sql = <<<SQL
select fid, pid, `timestamp` from pyramid_students
where fid = {$fid}
group by pid
SQL;

    $properties['pyramids'] = \Util\exec_sql($sql);

    //students with pyramid
    $sql = <<<SQL
select fid, pid, sid, `timestamp` from pyramid_students
where fid = {$fid}
SQL;

    $properties['students_with_pyramid'] = \Util\exec_sql($sql);

    //students without pyramid
    $sql = <<<SQL
select fid, pid, sid, `timestamp` from available_students
where fid = {$fid}
and sid not in (
    select * from pyramid_students
    where fid = {$fid}
)
SQL;

    $properties['students_without_pyramid'] = \Util\exec_sql($sql);

    return $properties;
}