<?php
global $ldshake_mode;

//TODO: mandatory ldshake token for students

if($ldshake_mode) {
    \ldshake\check_student_session_flow();
}

Student\enforce_login();

$sname = Student\get_username();
$sid = $_SESSION['student'];
global $fid, $flow_data, $activity_level, $levels, $peer_group_id;

// $levels, $fname, $fdes, $fid, $fid_timestamp
if(\Pyramid\get_current_flow() === -1) {
    //no flow
    \Flow\wait_flow();
}

if((int)$flow_data['sync'] == 0) {
    include __DIR__. '/student_async.php';
    exit;
}

\Pyramid\flow_add_student($fid, $sid);

//avoid race condition
$remaining_pyramids = \Pyramid\remaining_pyramids();
if(($pid = \Pyramid\get_student_pyramid($fid, $sid)) === false) {
    \Util\log(['activity' => 'page_load']);
    \Util\log_submit();
    \Answer\submit();
    if ($remaining_pyramids and !\Answer\is_submitted()) {
        \Answer\request();
        exit;
    } else {
        \Pyramid\wait_pyramid();
        exit;
    }
}

\Util\log(['activity' => 'page_load']);

//$activity_level
\Pyramid\get_current_activity_level();

//$peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp
\Group\get_members();

\Util\log_submit();

global $preupgrade_room, $last_rating_table;

//$preupgrade_room = \Util\get_room_string($fid, $pid, $activity_level, $peer_group_id);
$last_rating_table = \Group\get_group_rating_table();

//check if the group has completed the level and upgrade the level
\Pyramid\upgrade_level();

//forced upgrade if hard timeout is reached
if(\Pyramid\is_complete()) {
    \Pyramid\show_final_answer();
    exit;
} elseif(\Group\is_level_timeout()) {
    //only force upgrade if the pyramid has not reached completion
    \Util\log(['activity' => 'level_timeout']);
    \Pyramid\upgrade_level(true);
} else {
    //enter submitted information only if the level is not timed out
    \Answer\submit();
    \Answer\submit_rate();
}

//new data entered
if(\Answer\is_new_data()) {
    if(\Answer\submit_error()) {
        //TODO: implement retry
        \Answer\retry();
        exit;
    } else {
        //reload values
        \Pyramid\upgrade_level();
    }
}

//we are finished and one or more top level groups are not complete
if(\Group\is_level_timeout() and $activity_level + 1 == $levels) {
    //wait
    \Pyramid\wait();
    exit;
}


//here the current level never should be timed out
if(\Group\is_level_timeout()) {
    \Util\log(['activity' => 'error_flow_student_interaction_timed_out']);
    //wait
    \Pyramid\wait();
    exit;
}

if($activity_level == 0 and !\Group\is_level_zero_rating_started()) {
    if(!\Answer\is_submitted()/* and !\Group\is_level_timeout()*/) {
        \Answer\request();
        exit;
    } else {
        //wait
        \Pyramid\wait();
        exit;
    }
}

//we need the answers for other groups too
//if(!\Student\level_is_rated() and \Group\check_if_previous_groups_completed_task() and !\Group\is_level_timeout()) {
if(!\Student\level_is_rated() and \Group\is_level_started()/* and !\Group\is_level_timeout()*/) {
    if(\Answer\is_available_answers()) {
        \Answer\request_rate();
    } else {
        \Answer\skip_rating();
    }
}

//wait
\Pyramid\wait();
exit;