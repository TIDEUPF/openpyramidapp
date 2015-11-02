<?php

namespace Answer;

function is_submitted($params) {
    global $link, $sid, $fid;

    $res4 = mysqli_query($link, "select * from flow_student where sid = '$sid' and fid = '$fid'");
    //the user already submitted the answer
    if(mysqli_num_rows($res4) > 0) {
        return true;
    }

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
    global $link, $sid, $fid;

    if(isset($_POST['answer'])) {
        $ans_input = mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['qa']))));
        if ($ans_input != '') {
            //why can't edit the answer once submitted? because should not rate while editing. wrong answer will be rated
            if (mysqli_num_rows(mysqli_query($link, "select * from flow_student where sid = '$sid' and fid = '$fid'")) > 0) {
                return true;
                //edit if already answered
                //mysqli_query($link,"update flow_student set fs_answer = '$ans_input' where sid = '$sid' and fid_ = '$fid'");
                //if(mysqli_affected_rows($link) > 0){ $success = 'Submitted.'; }
            } else {
                //insert new
                mysqli_query($link, "insert into flow_student values ('', '$fid', '$sid', '$ans_input')");
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
            request(array('error' => $error));
        } else {
            //follow the next step in the flow
            return true;
        }
    }
    return false;
}

function request($params) {
    global $link, $sid, $fid, $sname, $levels, $activity_level, $peer_group_id;

    $vars = array(
        'username' 					=> $sname,
        'level' 					=> 'Level ' . '0/' . $levels,
        'answer_text' 			=> 'Write a question',
        'answer_submit_button' 	=> 'Submit your question',
        'hidden_input_array' 		=> array(
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
    global $link, $fid, $levels, $sname, $activity_level, $peer_group_id;

    $answer_text_array = array();
    $hidden_input_array = array();
    $i = 1;

    $answer_ids = get_selected_ids();
    foreach($answer_ids as $answer_id){
        $res5 = mysqli_query($link, "select * from flow_student where sid = '$answer_id' and fid = '$fid'");// to get peer answer
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
        'username'              => $sname,
        'level'                 => 'Level '. $activity_level . '/' . $levels,
        'header_text'           => 'Rate the following answers',
        'answer_text_array'   => $answer_text_array,
        'answer_rate_submit'  => 'Rate',
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

    return true;
}

function submit_rate() {
    global $link, $sid, $fid;

    if(isset($_POST['rate'])) { //3 set of post values since there can be 3ratings sometimes
        $rate_input =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['optradio1']))));
        $rate_lvl =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['lvl1']))));
        $to_whom_rated_id =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['to_whom_rated_id1']))));
        $rgroup_id =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['group_id1']))));

        $rate_input2 =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['optradio2']))));
        $rate_lvl2 =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['lvl2']))));
        $to_whom_rated_id2 =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['to_whom_rated_id2']))));
        $rgroup_id2 =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['group_id2']))));

        $rate_input3 =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['optradio3']))));
        $rate_lvl3 =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['lvl3']))));
        $to_whom_rated_id3 =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['to_whom_rated_id3']))));
        $rgroup_id3 =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['group_id3']))));

        if($rate_input != ''){


            if(mysqli_num_rows(mysqli_query($link, "select * from flow_student_rating where fsr_sid = '$sid' and fsr_fid = '$fid' and fsr_level = '$rate_lvl' and fsr_group_id = '$rgroup_id' and fsr_to_whom_rated_id = '$to_whom_rated_id' ")) > 0){
                //to be filled if editing of submitted rating is providing
            }
            else{
                //insert new
                $numofqustions =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['numofqustions']))));

                if($numofqustions == 1)
                {
                    mysqli_query($link,"insert into flow_student_rating values ('', '$fid', '$sid', '$rate_lvl', '$rgroup_id', '$rate_input', '$to_whom_rated_id', NOW() )");
                }
                elseif($numofqustions == 2)
                {
                    if(empty($rate_input2) || empty($rate_input))
                    {
                        $error = 'Please Rate All!';
                    }
                    else
                    {
                        mysqli_query($link,"insert into flow_student_rating values ('', '$fid', '$sid', '$rate_lvl', '$rgroup_id', '$rate_input', '$to_whom_rated_id', NOW() )");
                        mysqli_query($link,"insert into flow_student_rating values ('', '$fid', '$sid', '$rate_lvl2', '$rgroup_id2', '$rate_input2', '$to_whom_rated_id2', NOW() )");
                    }
                }
                elseif($numofqustions == 3)
                {
                    if(empty($rate_input2) || empty($rate_input) || empty($rate_input3))
                    {
                        $error = 'Please Rate All!';
                    }
                    else
                    {
                        mysqli_query($link,"insert into flow_student_rating values ('', '$fid', '$sid', '$rate_lvl', '$rgroup_id', '$rate_input', '$to_whom_rated_id', NOW() )");
                        mysqli_query($link,"insert into flow_student_rating values ('', '$fid', '$sid', '$rate_lvl2', '$rgroup_id2', '$rate_input2', '$to_whom_rated_id2', NOW() )");
                        mysqli_query($link,"insert into flow_student_rating values ('', '$fid', '$sid', '$rate_lvl3', '$rgroup_id3', '$rate_input3', '$to_whom_rated_id3', NOW() )");
                    }
                }

                if(mysqli_affected_rows($link) > 0){ $success = 'Rating Submitted.'; }else{	/*$error = 'Database error!';*/	}
            }
        }
        else{
            $error = 'Rating cannot be empty!';
        }

        if(isset($error)) {
            request_rate(array('error' => $error));
            exit;
        }
        return true;
    }

    return false;
}

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

function get_group_rating($params) {
    return 2;
}

function get_user_group($params) {
    return 1;
}

function get_selected_ids($params) {
    global $link, $sid, $fid, $levels, $sname, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $result = array();
    $activity_level_previous = $activity_level-1;
    if($activity_level == 0) {
        foreach ($peer_array as $rate_peer_id) {
            $res5 = mysqli_query($link, "select * from flow_student where sid = '$rate_peer_id' and fid = '$fid'");// to get peer answer
            if (mysqli_num_rows($res5) > 0) {//the peer already submitted the answer
                $result[] = $rate_peer_id;
            } else {//the peer did not submit the answer
                throw new Exception("peer answer not submitted");
            }
        }
    } else {
        foreach ($peer_group_combined_ids_temp as $pgcid_group_id_temp) {
            $sa_result_2 = mysqli_query($link, "select * from selected_answers where sa_fid = '$fid' and sa_level = '$activity_level_previous' and sa_group_id = '$pgcid_group_id_temp'");
            if (mysqli_num_rows($sa_result_2) > 0) {
                $sa_data_2 = mysqli_fetch_assoc($sa_result_2);
                $result[] = $sa_data_2['sa_selected_id'];
            } else {//no answers from the other peer, user must wait
                throw new Exception("group answer not rated");
            }
        }
    }

    return $result;
}

function view_final_answer($params) {
    global $link, $sid, $fid, $sname, $levels, $activity_level, $peer_group_id;

    $vars = array(
        'username' 					=> $sname,
        'level' 					=> 'Level ' . $activity_level . '/' . $levels,
        'header_text' 			=> 'The winning question is',
        'final_answer_array' 			=> $params['final_answer_array'],
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