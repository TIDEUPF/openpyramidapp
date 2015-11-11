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

function is_rated($params) {
    //check if rated
    global $link, $sid, $fid, $sname, $levels, $activity_level, $peer_group_id;

    $sa_result_4 = mysqli_query($link, "select * from flow_student_rating where fsr_fid = '$fid' and fsr_sid= '$sid' and fsr_level = '$activity_level'");
    if(mysqli_num_rows($sa_result_4) > 0){
        while($sa_data_4 = mysqli_fetch_assoc($sa_result_4)){
            $sa_rated = true;
        }
    }

    if(isset($sa_rated))
        return true;
    else
        return false;
}

function get_user_answer($sid, $fid) {
    global $link;

    $cua_result_1 = mysqli_query($link, "select * from flow_student where sid = '$sid' and fid = '$fid'");
    if(mysqli_num_rows($cua_result_1) > 0)
    {
        $cua_data_1 = mysqli_fetch_assoc($cua_result_1);
        $result = $cua_data_1['fs_answer'];
    }

    return $result;
}

function submit($params) {
    global $link, $sid, $fid, $input_result, $answer_submit_required_percentage, $peer_array;

    //check for the minimum participants for timeout start
    $answertimestamp = 0;
    $required_peers = \Group\get_needed_results_to_end_level();
    $peer_array_sql = implode("','", $peer_array);
    $n_submitted_answers = mysqli_num_rows(mysqli_query($link, "select * from flow_student where fid = '$fid' and sid in ('{$peer_array_sql}')"));

    if($n_submitted_answers + 1 >= $required_peers)
        $answertimestamp = time();

    if(isset($_POST['skip'])) {
        mysqli_query($link, "insert into flow_student values ('', '$fid', '$sid', '', 1, $answertimestamp)");
        $input_result['updated'] = 'true';
        return true;
    }

    if(!isset($_POST['answer']))
        return false;

    $input_result['op'] = 'submit';

    $ans_input = mysqli_real_escape_string($link, \Request\param('qa'));//stripslashes(strip_tags(trim($_POST['qa']))));

    if(is_timeout())
        return false;

    if ($ans_input != '') {
        //why can't edit the answer once submitted? because should not rate while editing. wrong answer will be rated
        if (mysqli_num_rows(mysqli_query($link, "select * from flow_student where sid = '$sid' and fid = '$fid'")) > 0) {
            return true;
            //edit if already answered
            //mysqli_query($link,"update flow_student set fs_answer = '$ans_input' where sid = '$sid' and fid_ = '$fid'");
            //if(mysqli_affected_rows($link) > 0){ $success = 'Submitted.'; }
        } else {
            //insert new
            mysqli_query($link, "insert into flow_student values ('', '$fid', '$sid', '$ans_input', 0, $answertimestamp)");
            if (mysqli_affected_rows($link) > 0) {
                $success = 'Submitted.';
            } else {
                $error = 'Database error!';
            }
        }
    } else {
        $error = 'Field cannot be empty!';
    }

    if (isset($error)) {
        //ask the answer again
        $input_result['error'] = "You are out of time";
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
    global $link, $sid, $fid, $sname, $levels, $activity_level, $peer_group_id, $peer_array;

    $timeout = get_answer_timeout();

    $vars = array(
        'username' 				    => $sname . ' + ' . (count(\Group\get_status_bar_peers())-1),
        'level' 				    => 'Level ' . \Pyramid\get_current_level() .'/' . $levels,
        'answer_text' 			    => 'Write a question',
        'answer_submit_button' 	    => 'Submit your question',
        'answer_submit_skip_button' => 'Skip the question',
        'answer_timeout'            => $timeout['time_left'],
        'answer_skip_timeout'       => $timeout['time_left_skip'],
        'hidden_input_array' 	    => array(
            'a_lvl' 			=> $activity_level,
            'a_peer_group_id'	=> $peer_group_id,
        ),
    );

    if(isset($params['error']))
        $vars['error'] = $params['error'];

    $body = \View\element("answer_form", $vars);

    \View\page(array(
        'title' => 'Question',
        'body' => $body,
    ));
    exit;
}

function request_rate($params) {
    global $link, $fid, $levels, $sname, $activity_level, $peer_group_id, $peer_array;

    $answer_text_array = array();
    $hidden_input_array = array();
    $i = 1;

    $answer_ids = get_selected_ids();
    foreach($answer_ids as $answer_id){
        $res5 = mysqli_query($link, "select * from flow_student where sid = '$answer_id' and fid = '$fid' and skip = 0");// to get peer answer
        if(mysqli_num_rows($res5) > 0) {//the peer already submitted the answer
            $data5 = mysqli_fetch_assoc($res5);
            $peer_answer = $data5['fs_answer'];

            $answer_text_array['optradio'.$i] = $peer_answer;
            $hidden_input_array = array_merge(array(
                'group_id'.$i          => $peer_group_id,
                'to_whom_rated_id'.$i  => $answer_id,
                'lvl'.$i               => $activity_level,
            ), $hidden_input_array);
            $i++;
        }
    }

    $hidden_input_array['numofqustions'] = $i-1;
    $vars = array(
        'username'              => $sname . ' + ' . (count(\Group\get_status_bar_peers())-1),
        'level'                 => 'Level '. \Pyramid\get_current_level() .'/' . $levels,
        'header_text'           => 'Rate the following answers',
        'answer_text_array'     => $answer_text_array,
        'answer_rate_submit'    => 'Rate',
        'rating_labels'         => array('Not rated', 'Awful', 'Bad', 'Good', 'Great', 'Awesome'),
        'hidden_input_array'    => $hidden_input_array,
    );

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
    global $link, $sid, $fid, $timeout, $input_result;

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

        $rating_array[] = $question_rating_values;
    }

    if(isset($error)) {
        $input_result['error'] = $error;
        return false;
    }

    //TODO: Now we could retrieve the values from the current status
    if($timestamp = \Group\get_level_timeout_timestamp($fid, $rating_array[0]['rate_lvl'], $rating_array[0]['rgroup_id'])) {
        if(time() > $timestamp + $timeout) {
            $input_result['error'] = "You are out of time";
            return false;
        }
    }

    foreach($rating_array as $rating) {
        mysqli_query($link,"insert into flow_student_rating values ('', '$fid', '$sid', '{$rating['lvl']}', '{$rating['group_id']}', '{$rating['optradio']}', '{$rating['to_whom_rated_id']}', NOW(), 0 )");

        if(mysqli_affected_rows($link) <= 0) {
            //TODO: database inconsistency
        }
    }

    //insert dummy rating for each possible skipped group answer
    skip_rating();

    $input_result['updated'] = true;

    return true;
}

function skip_rating() {
    global $link, $sid, $fid, $levels, $sname, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    //number of supposed to be rated
    if($activity_level == 0)
        $st_count = count($peer_array);
    else
        $st_count = count($peer_group_combined_ids_temp);

    //sids of students with real answers
    $available_answers = is_available_answers();
    if(empty($available_answers))
        $available_answers = array();

    $remaining_answers = $st_count - count($available_answers);
    for($i=0;$i<$remaining_answers;$i++) {
        mysqli_query($link, "insert into flow_student_rating values ('', '$fid', '$sid', '{$activity_level}', '$peer_group_id', '0', '-1', NOW(), 1 )");
        if (mysqli_affected_rows($link) <= 0) {
            //TODO: database inconsistency
        }
    }
}

function is_available_answers() {
    global $link, $sid, $fid, $levels, $sname, $activity_level;

    try {
        $result = get_selected_ids();
    } catch(\Exception $e) {
        //all group skipped answers
        return null;
    }

    return $result;
}

/*
function get_user_rating($fid, $who_rated, $to_whom_rated, $lvl) {
    global $link;

    $gpr_result_1 = mysqli_query($link, "select * from flow_student_rating where fsr_fid = '$fid' and fsr_sid= '$who_rated' and fsr_to_whom_rated_id = '$to_whom_rated' and fsr_level = '$lvl'");
    //this is to check whether peer has rated for a particular student, i.e. to_whom_rated = sid
    if(mysqli_num_rows($gpr_result_1) > 0)
    {
        $gpr_data_1 = mysqli_fetch_assoc($gpr_result_1);
        $result = $gpr_data_1['fsr_rating'];
    }
    else
    {
        $result = '';
    }

    return $result;
}
*/

function get_selected_ids($params) {
    global $link, $sid, $fid, $levels, $sname, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $result = array();
    $activity_level_previous = $activity_level-1;
    if($activity_level == 0) {
        foreach ($peer_array as $rate_peer_id) {
            $res5 = mysqli_query($link, "select * from flow_student where sid = '$rate_peer_id' and fid = '$fid' and skip = '0'");// to get peer answer
            if (mysqli_num_rows($res5) > 0) {//the peer already submitted the answer
                $result[] = $rate_peer_id;
            }
        }
        if(empty($result)) {//TODO: the peer did not submit the answer
            throw new \Exception ("peer answer not submitted");
        }
    } else {
        foreach ($peer_group_combined_ids_temp as $pgcid_group_id_temp) {
            $sa_result_2 = mysqli_query($link, "select * from selected_answers where sa_fid = '$fid' and sa_level = '$activity_level_previous' and sa_group_id = '$pgcid_group_id_temp' and skip = '0'");
            if (mysqli_num_rows($sa_result_2) > 0) {
                $sa_data_2 = mysqli_fetch_assoc($sa_result_2);
                $result[] = $sa_data_2['sa_selected_id'];
            }
        }
        if(empty($result)) {//no answers from the other peer, user must wait
            throw new \Exception("group answer not rated");
        }
    }

    return $result;
}

function view_final_answer($params) {
    global $link, $sid, $fid, $sname, $levels, $activity_level, $peer_group_id;

    $vars = array(
        'username' 					=> $sname . ' + ' . (count(\Group\get_status_bar_peers())-1),
        'level' 					=> 'Level ' . \Pyramid\get_current_level() .'/' . $levels,
        'header_text' 			    => 'The winning question is',
        'final_answer_array' 		=> $params['final_answer_array'],
        'hidden_input_array' 		=> array(
            'a_lvl' 			=> $activity_level,
            'a_peer_group_id'	=> $peer_group_id,
        ),
    );

    if(isset($params['error']))
        $vars['error'] = $params['error'];

    $body = \View\element("final_answer", $vars);

    \View\page(array(
        'title' => 'Question',
        'body' => $body,
    ));
    exit;
}

function get_answer_timeout() {
    global $link, $sid, $fid, $ftimestamp, $answer_timeout, $answer_skip_timeout;

    //$time_left = $answer_timeout - (time() - $ftimestamp);
    $time_left = 0;
    $time_left_skip = $answer_skip_timeout - (time() - $ftimestamp);
    $time_left_skip = $answer_skip_timeout;

    return array(
        'time_left' => $time_left,
        'time_left_skip' => $time_left_skip,
    );
}

function is_timeout() {
    global $link, $sid, $fid, $ftimestamp, $answer_timeout, $peer_array;
    //check for the minimum participants for timeout
    $answertimestamp = 0;

    //if the user is not present on any group in the current level it has been eliminated
    if(empty($peer_array))
        return true;

    $peer_array_sql = implode("','", $peer_array);

    if(!(mysqli_num_rows(mysqli_query($link, "select * from flow_student where `timestamp` > 0 and fid = '$fid' and sid in ('$peer_array_sql')")) > 0))
        return false;

    $query_result = mysqli_query($link, "select * from flow_student where `timestamp` > 0 and fid = '$fid' and sid in ('$peer_array_sql') order by timestamp asc limit 1");
    $result = mysqli_fetch_assoc($query_result);

    if(time() > $result['timestamp'] + $answer_timeout)
        return true;

    return false;
}

function is_new_data() {
    global $input_result;

    return !empty($input_result['updated']);
}

function submit_error() {
    global $input_result;

    return !empty($input_result['error']);
}
