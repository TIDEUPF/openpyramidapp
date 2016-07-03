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

function get_timestamps() {
    global $flow_data;

    $init_day = (int)$flow_data['start_timestamp'];
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

function end_date_string($level) {
    $timestamps = \Flow\get_timestamps();

    $start_timestamp = $timestamps[$level];

    $date_string = date("l jS G:i", $start_timestamp);

    return $date_string;
}