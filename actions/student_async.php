<?php
/*
Student\enforce_login();

$sname = Student\get_username();
$sid = $_SESSION['student'];
global $fid;

// $levels, $fname, $fdes, $fid, $fid_timestamp
if(!\Pyramid\get_current_flow()) {
    //flow changed
}
*/


\Pyramid\flow_add_student($fid, $sid);

\Util\log(['activity' => 'page_load']);

//TODO:check if it is manteinance time


//avoid race condition
$remaining_pyramids = \Pyramid\remaining_pyramids();

//TODO:the new solution is determine if there are not full pyramids at this moment
$existing_pyramids_full;

//ask the question
if(($pid = \Pyramid\get_student_pyramid($fid, $sid)) === false) {
    \Answer\submit();
    \Util\log_submit();

    //rating has started(even if still is not submitted by anyone)
    $result = mysqli_query($link, "select * from pyramid_groups where pg_fid = {$fid} and pg_level='0' and pg_started=1");
    if(mysqli_num_rows($result) > 0)
        $rating = true;
    else
        $rating = false;

    if ($existing_pyramids_full and !\Answer\is_submitted()) {
        \Answer\request();
        exit;
    } elseif($existing_pyramids_full) {
        //TODO: answer_form_filled
        \Answer\answer_submitted_wait();
        exit;
    } else {
        //wait for the service to update the user
        \Pyramid\wait_pyramid();
        //wait for pyramid allocation
    }
}

//$activity_level
\Pyramid\get_current_activity_level();

//$peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp
\Group\get_members();

if(\Pyramid\is_complete()) {
    \Pyramid\show_final_answer();
    exit;
}

//enter submitted information
\Answer\submit_rate();

//new data entered
if(\Answer\is_new_data()) {
    if(\Answer\submit_error()) {
        \Util\log(['activity' => 'error_on_submit']);
        //TODO: implement retry
        \Answer\retry();
        exit;
    }
}

if(\Pyramid\is_complete()) {
    \Pyramid\show_final_answer();
    exit;
}

if(\Answer\is_available_answers())
    \Answer\request_rate();
else
    \Pyramid\no_questions_available();

exit;
