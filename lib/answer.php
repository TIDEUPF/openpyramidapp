<?php

namespace Answer;

function is_submitted($params) {
    global $link, $sid, $fid;

    $res4 = mysqli_query($link, "select * from flow_student where sid = '$sid' and fid = '$fid'");
    //the user already submitted the answer
    if(mysqli_num_rows($res4) > 0)
        return true;

    //in case of timeout allow the user to rate the answers
 //   if(is_timeout())
 //       return true;

    return false;
}

function get_user_answer($sid, $fid) {
    global $link, $ps;

    $cua_result_1 = mysqli_query($link, "select * from flow_student where sid = '$sid' and fid = '$fid'");
    if(mysqli_num_rows($cua_result_1) > 0)
    {
        $cua_data_1 = mysqli_fetch_assoc($cua_result_1);
        $result = $cua_data_1['fs_answer'];
    }

    return $result;
}

function submit($params) {
    global $link, $sid, $fid, $ps, $pid, $input_result, $answer_submit_required_percentage, $peer_array;

    if(!empty($pid)) {
        if (\Answer\is_timeout())
            return false;
    }

    $sql_pid = $pid;
    if($pid === false)
        $sql_pid = -1;

    $answertimestamp = time();

    if(isset($_POST['skip'])) {
        mysqli_query($link, "insert into flow_student values ('', '$fid', '$sql_pid', '$sid', '', 1, $answertimestamp)");
        $input_result['updated'] = 'true';
        \Util\log(['activity' => 'skip_answer_form']);
        return true;
    }

    if(!isset($_POST['answer']))
        return false;

    $input_result['op'] = 'submit';

    $ans_input = mysqli_real_escape_string($link, \Request\param('qa'));

    if ($ans_input != '') {
        //why can't edit the answer once submitted? because should not rate while editing. wrong answer will be rated
        if (mysqli_num_rows(mysqli_query($link, "select * from flow_student where sid = '$sid' and fid = '$fid'")) > 0) {
            return true;
            //edit if already answered
            //mysqli_query($link,"update flow_student set fs_answer = '$ans_input' where sid = '$sid' and fid_ = '$fid'");
            //if(mysqli_affected_rows($link) > 0){ $success = 'Submitted.'; }
        } else {
            //insert new
            mysqli_query($link, "insert into flow_student values (null, '$fid', '$sql_pid', '$sid', '$ans_input', 0, $answertimestamp)");
            /*if (mysqli_affected_rows($link) > 0) {
                $success = 'Submitted.';
            } else {
                $error = 'Database error!';
            }*/
        }
    } else {
        $error = 'Field cannot be empty!';
    }

    if (isset($error)) {
        //ask the answer again
        $input_result['error'] = $error;
        return false;
    } else {
        //follow the next step in the flow
        $input_result['updated'] = true;
        return true;
    }

    return false;
}

function retry() {
    global $input_result;

    $error = $input_result['error'];
    if($input_result['op'] == 'submit_rate') {
        request_rate(array('error' => $error));
    } else {
        request(array('error' => $error));
    }
    exit;
}

function request($params) {
    global $link, $sid, $fid, $pid, $ps, $sname, $levels, $activity_level, $peer_group_id, $peer_array, $flow_data, $peer_toolbar_strlen;

    $timeout = get_answer_timeout();
    $petition = (empty($flow_data['question'])) ? 'Write a question' : $flow_data['question'];
    $level_text = (\Pyramid\get_current_level() >= 0) ? 'Level ' . \Pyramid\get_current_level() .'/' . $levels : '';
    $username_text = (count(\Group\get_status_bar_peers()) > 0) ? $sname . ' + ' . (count(\Group\get_status_bar_peers())-1) : $sname;

    $peers = implode(', ', \group\get_peers_sname());
    if(strlen($peers) > $peer_toolbar_strlen)
        $peers = substr($peers, 0, $peer_toolbar_strlen) . '...';

    $vars = array(
        'username' 				    => $sname . ' + ' . (count(\Group\get_status_bar_peers())-1),
        'username' 				    => $sname . ' + ' . $peers,
        'level' 				    => $level_text,
        'answer_text' 			    => $petition,
        'answer_submit_button' 	    => 'Submit your question',
        'answer_submit_skip_button' => 'Skip the question',
        'answer_timeout'            => $timeout['time_left'],
        'answer_skip_timeout'       => $timeout['time_left_skip'],
        'hidden_input_array' 	    => array(
            'a_lvl' 			=> $activity_level,
            'a_peer_group_id'	=> $peer_group_id,
        ),
    );

    $hidden_input_array['username'] = $sname;
    $hidden_input_array['fid'] = $fid;
    $hidden_input_array['pid'] = $pid;
    $hidden_input_array['level'] = $level_text;
    $hidden_input_array['group_id'] = $peer_group_id;
    $hidden_input_array['page'] = "answer_form";

    $vars['hidden_input_array'] = array_merge($vars['hidden_input_array'], $hidden_input_array);

    if(isset($params['error']))
        $vars['error'] = $params['error'];

    if((int)$flow_data['sync'] == 0) {
        $vars['username'] = $sname;
        $vars['answer_skip_timeout'] = 99999;
    }

    $body = \View\element("answer_form", $vars);

    \View\page(array(
        'title' => 'Question',
        'body' => $body,
    ));
    exit;
}

function request_rate($params) {
    global $link, $fid, $device, $flow_data, $pid, $levels, $sname, $peer_toolbar_strlen, $activity_level, $peer_group_id, $peer_array;

    $answer_text_array = array();
    $hidden_input_array = array();
    $i = 1;

    $answer_ids = get_selected_ids();
    foreach($answer_ids as $answer_id) {
        $res5 = mysqli_query($link, "select * from flow_student where sid = '$answer_id' and fid = '$fid' and skip = 0");// to get peer answer
        if(mysqli_num_rows($res5) > 0) {//the peer already submitted the answer
            $data5 = mysqli_fetch_assoc($res5);
            $peer_answer = $data5['fs_answer'];

            $answer_data = array('answer_text' => $peer_answer);
            $answer_data['selected'] = (int)\Student\get_rating($answer_id);
            $answer_text_array[] = $answer_data;

            $hidden_input_array = array_merge(array(
                'group_id'.$i          => $peer_group_id,
                'to_whom_rated_id'.$i  => $answer_id,
                'lvl'.$i               => $activity_level,
            ), $hidden_input_array);
            $i++;
        }
    }

    $hidden_input_array['numofqustions'] = $i-1;
    $hidden_input_array['username'] = $sname;
    $hidden_input_array['fid'] = $fid;
    $hidden_input_array['pid'] = $pid;
    $hidden_input_array['group_id'] = $peer_group_id;
    $hidden_input_array['level'] = \Pyramid\get_current_level();
    $hidden_input_array['page'] = "answer_rating";

    $peers = implode(', ', \group\get_peers_sname());
    if(strlen($peers) > $peer_toolbar_strlen)
        $peers = substr($peers, 0, $peer_toolbar_strlen) . '...';

    $vars = array(
        'username'              => $sname . ' + ' . (count(\Group\get_status_bar_peers())-1),
        'username'              => $sname . ' + ' . $peers,
        'level'                 => 'Level '. \Pyramid\get_current_level() .'/' . $levels,
        'header_text'           => 'Rate the following answers',
        'answer_text_array'     => $answer_text_array,
        'answer_rate_submit'    => 'Rate',
        'rating_labels'         => array('Not rated', 'Awful', 'Bad', 'Good', 'Great', 'Awesome'),
        'hidden_input_array'    => $hidden_input_array,
    );

    $location_id = hash('crc32b', $_SERVER['SCRIPT_NAME']);
    $room = 'room_' . $fid . '_' . $peer_group_id . '_' . $activity_level . '_' . $pid . '_' . $location_id;
    $messages_result = mysqli_query($link, "select * from chat where fid = '$fid' and room = '{$room}'");// to get peer answer
    $messages = [];
    while($message_row = mysqli_fetch_assoc($messages_result))
        $messages[] = $message_row;

    $vars['messages'] = $messages;

    if (\Student\level_is_rated() and (int)$flow_data['sync'] == 0) {//the peer already submitted the answer
        if($device == 'phone') {
            $vars['async_rated'] = "Submitted rating can be discussed and modified till today midnight. Login tomorrow to see selected questions at the next level!";
        } else {
            $vars['async_rated'] = "You submitted rating in this level successfully. You still can further discuss or modify your rating till today midnight. Make sure you login tomorrow to see which questions have been selected for the next pyramid level to continue!";
        }
    }

    if(isset($params['error']))
        $vars['error'] = $params['error'];

    $body = \View\element("answer_rating", $vars);
    \View\page(array(
        'title' => 'Rating',
        'body' => $body,
    ));
    exit;
}

function submit_rate() {
    global $link, $sid, $fid, $pid, $ps, $timeout, $input_result, $activity_level;

    if(!isset($_POST['rate']))
        return false;

    $input_result['op'] = 'submit_rate';
    $numofqustions = \Request\param('numofqustions');
    $rating_array = array();

    $rating_vars_identifiers = array(
        'optradio',
        'lvl',
        'to_whom_rated_id',
        'group_id',
    );

    //verify the integrity of the whole post
    for($i=1;$i<=$numofqustions;$i++) {
        $question_rating_values = array();
        foreach ($rating_vars_identifiers as $rating_var) {
            $value = \Request\param($rating_var . $i);

            if (empty($value) and $value !== "0")
                $error = 'Please Rate All!';

            $question_rating_values[$rating_var] = mysqli_real_escape_string($link, $value);
        }
        if(empty($question_rating_values['optradio']))
            $error = 'Please Rate All!';

        //timeout
        if($question_rating_values['lvl'] != $activity_level)
            return false;

        $rating_array[] = $question_rating_values;
    }

    if(isset($error)) {
        $input_result['error'] = $error;
        return false;
    }

    $i=0;
    mysqli_query($link, "start transaction");
    foreach($rating_array as $rating) {
        mysqli_query($link, "insert into flow_student_rating values ('', '$fid', '$pid', '$sid', '{$rating['lvl']}', '{$rating['group_id']}', '{$rating['optradio']}', '{$rating['to_whom_rated_id']}', NOW(), 0, {$i}) on duplicate key update fsr_rating='{$rating['optradio']}'");
        $i++;
        if(mysqli_affected_rows($link) <= 0) {
            //TODO: database inconsistency
        }
    }

    //insert dummy rating for each possible skipped group answer
    skip_rating();
    mysqli_query($link, "commit");

    $input_result['updated'] = true;

    return true;
}

function skip_rating() {
    global $link, $sid, $fid, $pid, $ps, $levels, $sname, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    //TODO: in asynchronous mode we select the first two questions

    $n_real_answers = count(get_selected_ids(false));
    $time = time();

    //if there are no real answers just set a dummy selected answer
    if(!$n_real_answers) {
        mysqli_query($link, "insert into selected_answers values ('$fid', '$pid', '$activity_level', '$peer_group_id', '-1', '0', '1', FROM_UNIXTIME({$time}))");
    }

    //number of skip answers supposed to be rated
    $remaining_answers = count(get_selected_ids(true)) - $n_real_answers;

    mysqli_query($link, "start transaction");
    for($i=0;$i<$remaining_answers;$i++) {
        mysqli_query($link, "insert into flow_student_rating values (null, '$fid', '$pid', '$sid', '{$activity_level}', '$peer_group_id', '0', '-1', NOW(), 1, {$i})");
        if (mysqli_affected_rows($link) <= 0) {
            //TODO: database inconsistency
        }
    }
    mysqli_query($link, "commit");
}

function is_available_answers() {
    global $link, $sid, $fid, $ps, $levels, $sname, $activity_level;

    try {
        $result = count(get_selected_ids()) > 0;
    } catch(\Exception $e) {
        //all group skipped answers
        return null;
    }

    return $result;
}

function get_selected_ids($full=false, $current_level = false) {
    global $link, $sid, $fid, $ps, $levels, $sname, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $result = [];

    if($current_level) {
        $activity_level_previous = $activity_level;
    } else {
        $activity_level_previous = $activity_level - 1;
    }

    $skip_answers = ' and skip = \'0\'';
    if($full)
        $skip_answers = '';

    if($activity_level == 0) {
        foreach ($peer_array as $rate_peer_id) {
            $res5 = mysqli_query($link, "select * from flow_student where sid = '$rate_peer_id' and fid = '$fid' {$skip_answers}");// to get peer answer
            if (mysqli_num_rows($res5) > 0) {//the peer already submitted the answer
                $result[] = $rate_peer_id;
            }
        }
        if(empty($result)) {//TODO: the peers did not submit the answer
            return [];
            //throw new \Exception ("peer answer not submitted");
        }
    } else {
        //FIXME: ugly fix for async
        if($current_level) {
            $activity_level_previous = $activity_level - 1;
        }
        foreach ($peer_group_combined_ids_temp as $pgcid_group_id_temp) {
            $sa_result_2 = mysqli_query($link, "select * from selected_answers where {$ps['sa']} and sa_level = '$activity_level_previous' and sa_group_id = '$pgcid_group_id_temp' {$skip_answers}");
            if (mysqli_num_rows($sa_result_2) > 0) {
                while($sa_data_2 = mysqli_fetch_assoc($sa_result_2)) {
                    $result[] = $sa_data_2['sa_selected_id'];
                }
            }
        }
        if(empty($result)) {//no answers from the other peer, user must wait
            return [];
            //throw new \Exception("group answer not rated");
        }
    }

    return $result;
}

function view_final_answer($params) {
    global $link, $sid, $fid, $pid, $ps, $sname, $levels, $activity_level, $peer_group_id;

    if(count($params['final_answer_array'])>1)
        $winning_text = 'Winning questions from this Pyramid';
    else
        $winning_text = 'Winning question from this Pyramid';

    if(count($params['other_answers'])>1)
        $other_header_text = 'Winning questions from other Pyramids';
    else
        $other_header_text = 'Winning question from other Pyramid';

    $vars = array(
        'username' 					=> $sname . ' + ' . (count(\Group\get_status_bar_peers())-1),
        'level' 					=> 'Level ' . \Pyramid\get_current_level() .'/' . $levels,
        'header_text' 			    => $winning_text,
        'other_header_text' 	    => $other_header_text,
        'final_answer_array' 		=> $params['final_answer_array'],
        'other_answer_array' 		=> $params['other_answers'],
        'hidden_input_array' 		=> array(
            'a_lvl' 			=> $activity_level,
            'a_peer_group_id'	=> $peer_group_id,
        ),
    );

    $hidden_input_array['username'] = $sname;
    $hidden_input_array['fid'] = $fid;
    $hidden_input_array['pid'] = $pid;
    $hidden_input_array['level'] = \Pyramid\get_current_level();
    $hidden_input_array['page'] = "winning_answers";
    $hidden_input_array['group_id'] = $peer_group_id;

    $vars['hidden_input_array'] = array_merge($vars['hidden_input_array'], $hidden_input_array);

    if(isset($params['error']))
        $vars['error'] = $params['error'];

    $feedback_result = mysqli_query($link, "select * from feedback where fid='{$fid}' and sid='{$sid}' and feedback > 0");
    if(mysqli_num_rows($feedback_result) > 0)
        $vars['no_feedback'] = true;

    $body = \View\element("final_answer", $vars);

    \View\page(array(
        'title' => 'Question',
        'body' => $body,
        'nosocket' => true,
    ));
    exit;
}

function get_answer_timeout() {
    global $link, $sid, $fid, $ps, $pid, $ftimestamp, $answer_timeout, $answer_skip_timeout, $peer_array;

    $peer_array_sql = implode("','", \Util\sanitize_array($peer_array));
    $r_start = mysqli_query($link, "select * from pyramid_students where timestamp > 0 and {$ps['e']} order by `timestamp` asc limit 1");
    $pyramid = mysqli_fetch_assoc($r_start);

    $r_submitted_answers = mysqli_query($link, "select * from flow_student where fid = '$fid' and sid in ('{$peer_array_sql}') order by `timestamp` asc");
    $n_submitted_answers = mysqli_num_rows($r_submitted_answers);

    $answer_timeout_start = null;

    //all student submitted
    if($n_submitted_answers >= \Group\get_needed_results_to_end_level(true, 'answer'))
        $answer_timeout_start = null;

    $required_peers = \Group\get_needed_results_to_end_level(false, 'answer');

    if($n_submitted_answers < $required_peers)
        $answer_timeout_start = null;

    if($n_submitted_answers >= $required_peers) {
        for ($i = 0; $i < $required_peers; $i++)
            $a_submitted_answers = mysqli_fetch_assoc($r_submitted_answers);

        $answer_timeout_start = $a_submitted_answers['timestamp'];
    }

    $time_left_skip = $answer_skip_timeout;

    return array(
        'time_left' => $answer_timeout_start,
        'time_left_skip' => $time_left_skip,
        'start_timestamp' => $pyramid['timestamp'],
    );
}

function is_timeout() {
    global $link, $sid, $fid, $ps, $ftimestamp, $answer_timeout, $peer_array, $activity_level, $peer_group_id, $flow_data;
    //check for the minimum participants for timeout
    $answertimestamp = 0;

    //if the user is not present on any group in the current level it has been eliminated
    if(empty($peer_array))
        return true;

    if($activity_level > 0)
        return true;

    if(\Student\level_is_rated())
        return true;

    //rating has started(even if still is not submitted by anyone)
    $result = mysqli_query($link, "select * from pyramid_groups where {$ps['pg']} and pg_level='0' and pg_group_id='{$peer_group_id}' and pg_started=1");
    if(mysqli_num_rows($result) > 0)
        return true;

    $peer_array_sql = implode("','", \Util\sanitize_array($peer_array));
    $r_submitted_answers = mysqli_query($link, "select * from flow_student where fid = '$fid' and sid in ('{$peer_array_sql}') order by `timestamp` asc");
    $n_submitted_answers = mysqli_num_rows($r_submitted_answers);

    //all student submitted
    if($n_submitted_answers >= \Group\get_needed_results_to_end_level(true, 'answer'))
        return true;

    $required_peers = \Group\get_needed_results_to_end_level(false, 'answer');

    /*
    if($n_submitted_answers < $required_peers)
        return false;
    */

    $timeout_data = get_answer_timeout();

    if($n_submitted_answers >= $required_peers) {
        for($i=0;$i<$required_peers;$i++)
            $a_submitted_answers = mysqli_fetch_assoc($r_submitted_answers);

        //the answer timestamp cannot be lower than pyramid creation creation
        if($timeout_data['start_timestamp'] > $a_submitted_answers['timestamp'])
            $a_submitted_answers['timestamp'] = $timeout_data['start_timestamp'];

        if(time() > $a_submitted_answers['timestamp'] + $answer_timeout)
            return true;
    }

    //check hardtimer

    if(time() > $flow_data['hardtimer_question'] + $timeout_data['start_timestamp'])
        return true;

    return false;
}

function is_new_data() {
    global $input_result;

    if(!empty($input_result['error']))
        return true;

    return !empty($input_result['updated']);
}

function submit_error() {
    global $input_result;

    return !empty($input_result['error']);
}

function answer_submitted_wait($params) {
    global $link, $sid,  $fid, $ps, $sname, $levels, $activity_level, $peer_group_id, $peer_array, $peer_group_combined_ids;

    $initial_level = $activity_level;

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
    $hidden_input_array['page'] = "question_submitted_message";

    $vars['hidden_input_array'] = array_merge($vars['hidden_input_array'], $hidden_input_array);

    $body = \View\element("answer_submitted", $vars);

    \View\page(array(
        'title' => 'Question',
        'body' => $body,
    ));
    exit;
}
