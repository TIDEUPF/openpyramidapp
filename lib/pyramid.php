<?php

namespace Pyramid;

function get_current_activity_level() {
    global $link, $sid, $fid, $ps, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids;

    $activity_level = 0;

    //get the highest rated level for the user
    $gcal_result_1 = mysqli_query($link, "select * from pyramid_groups where pg_started = 1 and {$ps['pg']} order by pg_level desc");
    if(mysqli_num_rows($gcal_result_1) > 0) {
        while($gcal_data_1 = mysqli_fetch_assoc($gcal_result_1)) {
            if(in_array($sid, explode(',',$gcal_data_1['pg_group'])) and $gcal_data_1['pg_level'] > $activity_level)
                $activity_level = $gcal_data_1['pg_level'];
        };
    }

    return $activity_level;
}

function get_available_level() {
    global $link, $sid, $fid, $ps, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids;

    //get the lowest active level providing the timeout is not set
    $available_result = mysqli_query($link, "select pg_level, pg_group_id from pyramid_groups where {$ps['pg']} and pg_timestamp = 0 order by pg_level asc limit 1");

    if(mysqli_num_rows($available_result) > 0) {
        $available_data = mysqli_fetch_assoc($available_result);
        $available_activity_level = $available_data['pg_level'];
        $available_group_id = $available_data['pg_group_id'];
    } else {
        return false;
    }

    return array('activity_level' => $available_activity_level, 'peer_group_id' => $available_group_id);
}

/*
function add_latecomer($pid, $activity_level, $peer_group_id, $pid, $sid) {
    global $link, $fid;

    mysqli_query($link, "update pyramid_groups set pg_latecomers = CONCAT(pg_latecomers,',','{$sid}') where pg_fid='{$fid}' and pg_pid='{$pid}' and pg_level='{$activity_level}' and pg_group_id='{$peer_group_id}'");
}
*/

function upgrade_level($forced = false) {
    global $link, $sid, $fid, $pid, $ps, $activity_level, $levels, $peer_array, $peer_group_id, $peer_group_combined_ids, $flow_data;

    //we never upgrade the level on a syncronous situation
    if($flow_data['sync'] == 0)
        return false;

    if(!$forced) {
        //the answer phase has ended
        if ($activity_level == 0 and \Answer\is_timeout() and !\Group\is_level_zero_rating_started()) {
            $time = time();
            mysqli_query($link, "update pyramid_groups set pg_started = 1, pg_start_timestamp='{$time}' where pg_started = '0' and {$ps['pg']} and pg_level='{$activity_level}' and pg_group_id='{$peer_group_id}'");
            return true;
        }

        if (!\Student\level_is_rated() and !\Group\sa_exists())
            return false;

        $upgrade = false;
        //check if the highest rated level has a selected answer
        $gcal_result_2 = mysqli_query($link, "select * from selected_answers where {$ps['sa']} and sa_level = '$activity_level' and sa_group_id = '$peer_group_id'");
        if (mysqli_num_rows($gcal_result_2) > 0) {
            $upgrade = true;
        } else {
            //here decide the criteria to allow to proceed to the next level
            $cgfl_temp = \Group\check_if_group_finished_level();

            if ($cgfl_temp and !($activity_level == 0 and !\Student\level_is_rated())) {
                set_selected_answers();
                $upgrade = true;
            }
        }
    }

    if($forced) {
        //the answer phase has ended
        if ($activity_level == 0 and \Answer\is_timeout() and !\Group\is_level_zero_rating_started()) {
            $time = time();

            mysqli_query($link, "update pyramid_groups set pg_started = 1, pg_start_timestamp='{$time}' where pg_started = '0' and pg_fid='{$fid}' and pg_level='{$activity_level}' and pg_group_id='{$peer_group_id}'");
            return true;
        } else {
            if(!set_selected_answers()) {
                //the conditions to select an question are not meet
                $time = time();
                //TODO: select 2 answers if asychronous
                mysqli_query($link, "insert into selected_answers values ('$fid', '$pid', '$activity_level', '$peer_group_id', '-1', '0', '1', FROM_UNIXTIME({$time}))");
                \Util\log(['activity' => 'level_finished_with_no_rates']);
            }
        }
    }

    if($upgrade or $forced) {
        if($activity_level + 1 == $levels)
            return false;

        $activity_level++;
        \Group\get_members();
        $time = time();
        \Util\log(['activity' => 'group_upgrade']);

        //register only when all sibling groups are completed
        if(\Group\check_if_previous_groups_completed_task()) {
            set_selected_answers_for_previous_groups();
            mysqli_query($link, "update pyramid_groups set pg_started = 1, pg_start_timestamp='{$time}' where {$ps['pg']} and pg_level='{$activity_level}' and pg_group_id='{$peer_group_id}'");
            \Util\log(['activity' => 'siblings_finished_level_upgrade']);
        }
    }
}


//expects true/false
function set_selected_answers() {
    global $link, $sid, $fid, $pid, $random_selection, $n_selected_answers, $ps, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids;

    //to sum the ratings
    $ssa_result_1= mysqli_query($link, "SELECT fsr_to_whom_rated_id, skip, SUM(fsr_rating) as sum FROM `flow_student_rating` where {$ps['fsr']} and fsr_level = '$activity_level' and fsr_group_id = '$peer_group_id' group by fsr_to_whom_rated_id order by SUM(fsr_rating) desc, flow_student_rating_id asc limit {$n_selected_answers}");

    if(mysqli_num_rows($ssa_result_1)> 0) {
        //takes into account skipped answers
        while($ssa_data_1 = mysqli_fetch_assoc($ssa_result_1)) {
            $selected_id = $ssa_data_1['fsr_to_whom_rated_id'];
            $selected_id_rating_sum = $ssa_data_1['sum'];
            $skip = $ssa_data_1['skip'];
            $time = time();
            mysqli_query($link, "insert into selected_answers values ('$fid', '$pid', '$activity_level', '$peer_group_id', '$selected_id', '$selected_id_rating_sum', '$skip', FROM_UNIXTIME({$time}))");
            \Util\log(['activity' => 'selected_answer', 'answer' => $selected_id, 'rating' => $selected_id_rating_sum]);
        }
        return true;
    } elseif($random_selection){
        //TODO: force random selection
        //doesn't take into account skipped answers
        $answers = \Answer\get_selected_ids(false, true);
        $n_selected = 0;
        for($i=0; $i<$n_selected_answers and !empty($answers); $i++) {
            $i_selected = mt_rand(1, 9999) % count($answers);
            $selected_id = $answers[$i_selected];
            unset($answers[$i_selected]);
            $answers = array_values($answers);

            $selected_id_rating_sum = 0;
            $skip = 0;
            $time = time();
            mysqli_query($link, "insert into selected_answers values ('$fid', '$pid', '$activity_level', '$peer_group_id', '$selected_id', '$selected_id_rating_sum', '$skip', FROM_UNIXTIME({$time}))");
            \Util\log(['activity' => 'selected_answer_random', 'answer' => $selected_id, 'rating' => $selected_id_rating_sum]);
            $n_selected++;
        }

        if($n_selected > 0)
            return true;
    }

    return false;
}

function set_selected_answers_for_previous_groups() {
    global $link, $sid, $fid, $pid, $ps, $n_selected_answers, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids;

    $previous_groups_ids = explode(',', $peer_group_combined_ids);
    $previous_level = $activity_level - 1;
    $time = time();

    foreach($previous_groups_ids as $fpgi) {

        //already selected
        if(mysqli_num_rows(mysqli_query($link, "select * from selected_answers where {$ps['sa']} and sa_group_id='{$fpgi}' and sa_level='{$previous_level}'")) > 0)
            continue;

        $ssa_result_1= mysqli_query($link, "SELECT fsr_to_whom_rated_id, skip, SUM(fsr_rating) as sum FROM `flow_student_rating` where {$ps['fsr']} and fsr_level = '$previous_level' and fsr_group_id = '$fpgi' group by fsr_to_whom_rated_id order by SUM(fsr_rating) desc limit {$n_selected_answers}");

        if(mysqli_num_rows($ssa_result_1) > 0) {
            while($ssa_data_1 = mysqli_fetch_assoc($ssa_result_1)) {
                $selected_id = $ssa_data_1['fsr_to_whom_rated_id'];
                $selected_id_rating_sum = $ssa_data_1['sum'];
                $skip = $ssa_data_1['skip'];
                mysqli_query($link, "insert into selected_answers values ('$fid', '$pid', '$previous_level', '$fpgi', '$selected_id', '$selected_id_rating_sum', '$skip', FROM_UNIXTIME({$time}))");
                \Util\log(['activity' => 'selected_answer', 'answer' => $selected_id, 'rating' => $selected_id_rating_sum]);
            }
            //return true;
        } else {
            //TODO: select by random
            mysqli_query($link, "insert into selected_answers values ('$fid', '$pid', '$previous_level', '$fpgi', '-1', '-1', '1', FROM_UNIXTIME({$time}))");
        }
    }
}

function get_groups($params) {

}

function is_level_computed($params) {
    global $link, $sid,  $fid, $ps, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $pre_task_completed = array();
    $activity_level_previous = $activity_level-1;

    //check if groups completed previous task
    foreach($peer_group_combined_ids_temp as $pgcid_group_id_temp){
        $sa_result_2 = mysqli_query($link, "select * from selected_answers where {$ps['sa']} and sa_level = '$activity_level_previous' and sa_group_id = '$pgcid_group_id_temp'");
        if(mysqli_num_rows($sa_result_2) > 0){
            $pre_task_completed[] = 1;
        }
    }

    if(count($peer_group_combined_ids_temp) == count($pre_task_completed))
        return true;

    return false;
}

function compute_level_rating() {
    global $link, $sid, $fid, $pid, $ps, $n_selected_answers, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $result_3= mysqli_query($link, "SELECT fsr_to_whom_rated_id, SUM(fsr_rating) as sum FROM `flow_student_rating` where {$ps['fsr']} and fsr_level = '$activity_level' and fsr_group_id = '$peer_group_id' group by fsr_to_whom_rated_id order by SUM(fsr_rating) desc limit {$n_selected_answers}");

    while($data_t_2 = mysqli_fetch_assoc($result_3)) {
        $selected_id = $data_t_2['fsr_to_whom_rated_id'];
        $selected_id_rating_sum = $data_t_2['sum'];
        $time = time();
        mysqli_query($link, "insert into selected_answers values ('$fid', '$pid', '$activity_level', '$peer_group_id', '$selected_id', '$selected_id_rating_sum', FROM_UNIXTIME({$time}))");
    }
}

function is_complete() {
    global $link, $sid,  $fid, $ps, $levels, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    //last level-- to show selected answers
    if($activity_level + 1 >= $levels) {
        $final_level = $levels - 1;

        //all users answered so proceed to show the final results
        if( mysqli_num_rows(mysqli_query($link, "select * from pyramid_groups where {$ps['pg']} and pg_level = '{$final_level}'")) <= mysqli_num_rows(mysqli_query($link, "select * from selected_answers where {$ps['sa']} and sa_level = '{$final_level}'")) ) {
            return true;
        }
    }
    return false;
}

function get_remainings_pyramids_answers() {
    global $link, $sid,  $fid, $pid, $ps, $levels, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $answers = [];

    $sql = <<<SQL
select max(pg_level) as level, pg_pid as pid,
(
	select count(*) from pyramid_groups as r
	where r.pg_fid = m.pg_fid and r.pg_level =
	(
		select pg_level from pyramid_groups as q
		where q.pg_fid = m.pg_fid
		and q.pg_pid = m.pg_pid
		order by pg_level desc limit 1
	)
	and r.pg_pid = m.pg_pid
) as ngroups
from pyramid_groups as m where pg_fid='{$fid}' and pg_pid <> '{$pid}' group by pg_pid order by m.pg_pid desc, m.pg_group_id desc
SQL;

    $result = mysqli_query($link, $sql);

    while($pg = mysqli_fetch_assoc($result)) {
        $sa_sql = "select skip, (select fs_answer from flow_student where sid = sa_selected_id and fid = sa_fid order by fs_id asc) as answer from selected_answers where sa_fid='{$fid}' and sa_pid = '{$pg['pid']}' and sa_level = '{$pg['level']}' and skip = 0 order by sa_pid asc, sa_group_id asc";
        $sa_result = mysqli_query($link, $sa_sql);
        //if(mysqli_num_rows($sa_result) >= $pg['ngroups']) {
            while($sa_row = mysqli_fetch_assoc($sa_result)) {
                $answers[] = $sa_row['answer'];
            }
        //}
    }

    return $answers;
}

function is_final_level_complete() {
    global $link, $sid,  $fid, $ps, $levels, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $last_level = $activity_level -1;
    $is_final_level_completed = mysqli_num_rows(mysqli_query($link, "select * from selected_answers where {$ps['sa']} and sa_group_id='{$peer_group_id}' and sa_level='{$last_level}'")) > 0;

    return $is_final_level_completed;
}

function get_current_level() {
    global $levels, $activity_level;

    $current_level = $activity_level+1;

    if($current_level > $levels)
        $current_level = $levels;

    return $current_level;
}

function show_final_answer() {
    global $link, $sid,  $fid, $ps, $activity_level, $levels, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $final_level = $levels - 1;
    $result_11 = mysqli_query($link, "select * from selected_answers where {$ps['sa']} and sa_level = '$final_level' and skip = '0'");
    $answers = array();
    while ($data_t_11 = mysqli_fetch_assoc($result_11)) {
        $qa_last_selected_id = $data_t_11['sa_selected_id'];
        $result_12 = mysqli_query($link, "select * from flow_student where fid = '$fid' and sid = '$qa_last_selected_id'");
        $data_t_12 = mysqli_fetch_assoc($result_12);
        $answers[] = $data_t_12['fs_answer'];
    }

    if(empty($answers))
        $answers[] = 'Sorry, there were no questions rated in this pyramid.';

    $other_answers = get_remainings_pyramids_answers();

    \Util\log(['activity' => 'final_answers', 'answers' => $answers]);

    \Answer\view_final_answer(array('final_answer_array' => $answers, 'other_answers' => $other_answers));
}

function get_current_flow() {
    global $levels, $fname, $fdes,  $fid, $ps, $n_selected_answers, $random_selection, $link, $ftimestamp, $flow_data, $timeout, $answer_timeout, $pyramid_size, $pyramid_minsize, $answer_required_percentage, $answer_submit_required_percentage;
    //get information the latest flow
    $res3 = mysqli_query($link, "select * from flow order by fid desc limit 1");
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
        $answer_required_percentage = (int)$data3["rating_required_percentage"];
        $answer_submit_required_percentage = (int)$data3["answer_submit_required_percentage"];

        
        if(isset($_SESSION['fid'])) {
            if($_SESSION['fid'] != $fid) {
                $_SESSION['fid'] = $fid;

                return false;
            }
        } else {
            $_SESSION['fid'] = $fid;
        }
        return $fid;
    }
    else{
        //throw new Exception("There are no flows");
        //show activity explanation
        return  -1;
    }
}

function wait($params) {
    global $link, $sid,  $fid, $ps, $sname, $levels, $activity_level, $peer_group_id, $peer_array, $peer_group_combined_ids;

    $initial_level = $activity_level;
    upgrade_level();

    $peers = implode(', ', \group\get_peers_sname());
    if(strlen($peers) > 15)
        $peers = substr($peers, 0, 15) . '...';

    $vars = array(
        'username' 					=> $sname . ' + ' . (count(\Group\get_status_bar_peers())-1),
        'username' 					=> $sname . ' + ' . $peers,
        'level' 				    => T('Level') . ' ' . (\Pyramid\get_current_level()+1) .'/' . ($levels+1),
        'answer_text' 			=> 'Write a question',
        'answer_submit_button' 	=> 'Submit your question',
        'hidden_input_array' 		=> array(
            'a_lvl' 			=> $activity_level,
            'a_peer_group_id'	=> $peer_group_id,
        ),
    );

    //if we wait another group all peers are inactive
    $n_inactive_peers = count(get_inactive_level_group_peers());
    if(count($peer_array) != $n_inactive_peers and $n_inactive_peers > 0) {
        $vars['inactive_peers_count'] = max(count(get_inactive_level_group_peers()), 1);
    } else {
        $array_size = \Group\get_status_bar_groups_count();
        //TODO: fix last level count
        $rated_count = \Group\get_previous_groups_rated_count();
        $vars['inactive_groups_count'] = max($array_size - $rated_count, 1);
    }

    if(isset($params['error']))
        $vars['error'] = $params['error'];

    $hidden_input_array['username'] = $sname;
    $hidden_input_array['fid'] = $fid;
    $hidden_input_array['level'] = \Pyramid\get_current_level();
    $hidden_input_array['levels'] = $levels;
    $hidden_input_array['page'] = "waiting";
    $hidden_input_array['group_id'] = $peer_group_id;

    $vars['hidden_input_array'] = array_merge($vars['hidden_input_array'], $hidden_input_array);

    $body = \View\element("answer_waiting", $vars);

    \View\page(array(
        'title' => 'Question',
        'body' => $body,
    ));
    exit;
}

function wait_pyramid($params) {
    global $link, $sid,  $fid, $ps, $sname, $levels, $activity_level, $peer_group_id, $peer_array, $peer_group_combined_ids;

    $initial_level = $activity_level;
    upgrade_level();

    $vars = array(
        'username' 					=> $sname,
        'level' 				    => 'Level 1' . '/' . ($levels+1),
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
    $hidden_input_array['levels'] = $levels;
    $hidden_input_array['page'] = "pyramid_creation_waiting";

    $vars['hidden_input_array'] = array_merge($vars['hidden_input_array'], $hidden_input_array);

    $body = \View\element("pyramid_waiting", $vars);

    \View\page(array(
        'title' => 'Question',
        'body' => $body,
    ));
    exit;
}

function no_questions_available($params) {
    global $link, $sid,  $fid, $ps, $sname, $levels, $activity_level, $peer_group_id, $peer_array, $peer_group_combined_ids;

    $initial_level = $activity_level;
    upgrade_level();

    $vars = array(
        'username' 					=> $sname,
        'level' 				    => T('Level') . ' '. \Pyramid\get_current_level() . '/' . $levels,
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
    $hidden_input_array['page'] = "no_available_questions";

    $vars['hidden_input_array'] = array_merge($vars['hidden_input_array'], $hidden_input_array);

    $body = \View\element("noquestions_available", $vars);

    \View\page(array(
        'title' => 'Question',
        'body' => $body,
    ));
    exit;
}

function activity_status($params) {
    global $link, $sid,  $fid, $ps, $sname, $levels, $activity_level, $peer_group_id, $peer_array, $peer_group_combined_ids;

    $initial_level = $activity_level;
    upgrade_level();

    $vars = array(
        'username' 				=> $sname,
        'n_inactive_peers'      => $params['n_inactive_peers'],
        'time_remaining'        => $params['time_remaining'],
        'ui_level'              => $params['level'],
        'levels'                => $params['levels'],
        'question_submitted'    => $params['question_submitted'],
        'level' 				=> T('Level') . ' '. $params['level'] . '/' . ($levels+1),
        'hidden_input_array' 	=> array(
            'a_lvl' 			=> $activity_level,
            'a_peer_group_id'	=> $peer_group_id,
        ),
    );

    $hidden_input_array['username'] = $sname;
    $hidden_input_array['fid'] = $fid;
    $hidden_input_array['level'] = 1;
    $hidden_input_array['page'] = "no_available_questions";

    $vars['hidden_input_array'] = array_merge($vars['hidden_input_array'], $hidden_input_array);

    $body = \View\element("activity_status", $vars);

    \View\page(array(
        'title' => 'Question',
        'body' => $body,
    ));
    exit;
}

//remove users that didn't submit due the timeout in the previous level
function set_previous_level_peer_active_group_ids() {
    global $link, $sid,  $fid, $ps, $sname, $levels, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    if(\Group\is_level_timeout())//redundant
        return false;

    if(!\Group\check_if_previous_groups_completed_task() or !\Answer\is_submitted())
        return false;

    $previous_activity_level = $activity_level-1;
    $peer_group_combined_ids_array = explode(',',$peer_group_combined_ids);
    $peer_group_combined_ids_sql = implode("','",\Util\sanitize_array($peer_group_combined_ids_array));
    $peer_array_sql = implode("','", \Util\sanitize_array($peer_array));

    if($previous_activity_level == -1)
        $submitted_group_answers_query = mysqli_query($link, "select distinct sid as active_sid from flow_student where fid = '$fid' and sid in ('{$peer_array_sql}')");
    else
        $submitted_group_answers_query = mysqli_query($link, "select distinct fsr_sid as active_sid from flow_student_rating where {$ps['fsr']} and fsr_level='{$previous_activity_level}' and fsr_group_id in ('{$peer_group_combined_ids_sql}')");

    $active_ids = array();
    while ($rating = mysqli_fetch_assoc($submitted_group_answers_query)) {
        $active_ids[] = $rating['active_sid'];
    }

    if(count($active_ids)) {
        $active_ids_string = implode(',', $active_ids);
        mysqli_query($link, "update pyramid_groups set pg_group=CONCAT('{$active_ids_string}', pg_latecomers) where {$ps['pg']} and pg_level='{$activity_level}' and pg_group_id='{$peer_group_id}'");
        $peer_array = $active_ids;

    }

    return $active_ids;
}

function available_students($number = 0) {
    global $link, $sid, $fid, $ps, $sname, $levels, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    $students = [];
    $limit = "";
    if($number > 0)
        $limit = "limit {$number}";

    $result = mysqli_query($link, "select distinct * from flow_available_students where fid='$fid' and sid not in (select sid from pyramid_students where fid='$fid') {$limit}");
    while($students_r = mysqli_fetch_assoc($result)) {
        $students[] = $students_r['sid'];
    }

    return $students;
}

//update the pyramid to add the latecomers
function update_pyramid($fid, $pid, $number = 0) {
    global $link;

    $pyramid_result = mysqli_query($link, "select * from pyramid_groups where pg_fid='$fid' and pg_pid='$pid' order by pg_level asc");
    while($pyramid_row = mysqli_fetch_assoc($pyramid_result)) {
        $pyramid_row['pg_group'] = explode(',' , $pyramid_row['pg_group']);
        $pyramid_row['pg_latecomers'] = explode(',' , $pyramid_row['pg_latecomers']);
        $pyramid_row['pg_combined_group_ids'] = explode(',' , $pyramid_row['pg_combined_group_ids']);
        $pyramid[$pyramid_row['pg_level']][$pyramid_row['pg_group_id']] = $pyramid_row;
        $top_level = (int)$pyramid_row['pg_level'];
    }

    $nbase_groups = count($pyramid["0"]);
    $latecomers = available_students($number);
    //if(empty($latecomers))
    //    return false;
    $nlatecomers = count($latecomers);
    //$split_size = ceil($nlatecomers/$nbase_groups);

    foreach($latecomers as $lt) {
        $dest_group = mt_rand(1,9999) % $nbase_groups;
        $pyramid["0"]["$dest_group"]['pg_latecomers'][] = $lt;
    }

    for($i=0; $i<=$top_level; $i++) {
        foreach($pyramid["$i"] as $group_row) {
            if($i != 0) {
                $previous_level = $i-1;
                $group_members = [];
                foreach($group_row['pg_combined_group_ids'] as $previous_group_id) {
                    $group_members = array_merge($group_members,$pyramid["$previous_level"]["$previous_group_id"]['pg_group']);
                }
                $group_members = array_unique(array_filter(array_merge($group_members,$group_row['pg_latecomers'])));
            } else {
                $group_members = array_unique(array_filter(array_merge($group_row['pg_group'], $group_row['pg_latecomers'])));
            }
            $updated_group_members = implode(',', $group_members);
            mysqli_query($link, "update pyramid_groups set pg_group='{$updated_group_members}' where pg_fid='{$group_row['pg_fid']}' and pg_pid='{$group_row['pg_pid']}' and pg_level='{$group_row['pg_level']}' and pg_group_id='{$group_row['pg_group_id']}'");
            $pyramid["$i"][$group_row['pg_group_id']]['pg_group'] = $group_members;
        }
    }

    foreach($latecomers as $pyramidstudent)
        add_student($fid, $pid, $pyramidstudent);
}

function is_rating_started() {
    global $link, $sid,  $fid, $pid, $ps, $sname, $levels, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    //rating has started(even if still is not submitted by anyone)
    $result = mysqli_query($link, "select * from pyramid_groups where pg_fid = {$fid} and pg_pid = '{$pid}' and pg_group_id = '{$peer_group_id}' and pg_level='0' and pg_started=1");
    if(mysqli_num_rows($result) > 0)
        $rating = true;
    else
        $rating = false;

    return $rating;
}

function get_inactive_level_group_peers() {
    global $link, $sid,  $fid, $ps, $sname, $levels, $activity_level, $peer_array, $peer_group_id, $peer_group_combined_ids, $peer_group_combined_ids_temp;

    //$peer_group_combined_ids_array = explode(',',$peer_group_combined_ids);
    $peer_array_sql = implode("','", \Util\sanitize_array($peer_array));

    //if($activity_level == 0 and !\Student\level_is_rated())
    if($activity_level == 0 and !is_rating_started()) //TODO: test this solution
        $inactive_peers_result = mysqli_query($link, "select distinct sid from students where sid in ('{$peer_array_sql}') and sid not in (select distinct sid as active_sid from flow_student where fid = '$fid' and sid in ('{$peer_array_sql}'))");
    else
        $inactive_peers_result = mysqli_query($link, "select distinct sid from students where sid in ('{$peer_array_sql}') and sid not in (select distinct fsr_sid as active_sid from flow_student_rating where {$ps['fsr']} and fsr_level='{$activity_level}' and fsr_group_id = '$peer_group_id')");

    $inactive_peers = array();
    while ($row = mysqli_fetch_assoc($inactive_peers_result)) {
        $inactive_peers[] = $row;
    }

    return $inactive_peers;
}

function remaining_pyramids() {
    global $link, $pyramid_size, $fid, $ps, $flow_data;

    $result = mysqli_query($link, "select sid from pyramid_students where fid = '$fid'");
    $nflow_students = mysqli_num_rows($result);

    if((int)$flow_data['multi_py'] == 0) {
        if($nflow_students > 0)
            return false;

        return true;
    }

    if($flow_data['expected_students'] - $nflow_students >= $pyramid_size)
        return true;

    return false;
}

function add_student($fid, $pid, $sid) {
    global $link;

    $time = time();
    $result = mysqli_query($link,"insert into pyramid_students values (null, '$fid', '$pid', '$sid', '$time')");

    return !!mysqli_affected_rows($result);
}

function flow_add_student($fid, $sid) {
    global $link;

    $result = mysqli_query($link,"insert into flow_available_students values (null, '$fid', '$sid')");

    return !!mysqli_affected_rows($result);
}

function exists_student_pyramid($fid, $pid, $sid) {
    global $link;

    $result = mysqli_query($link,"select * from pyramid_students where fid='$fid' and pid='$pid' and sid='$sid'");

    if(mysqli_num_rows($result))
        return true;

    return false;
}

function get_student_pyramid($fid, $sid) {
    global $link, $pid;

    $result = mysqli_query($link,"select * from pyramid_students where fid='$fid' and sid='$sid' limit 1");

    if(!mysqli_num_rows($result)) {
        $pid = false;
        return $pid;
    }

    $result_row = mysqli_fetch_assoc($result);

    $pid = (int)$result_row['pid'];

    \Util\sql_gen();

    return $pid;
}

function create_pyramid($fid, $fl, $fsg, $new_pyramid_size) {
    global $link,  $fid, $ps, $flow_data;

    //required questions
    $n_required_questions = floor($new_pyramid_size/$fsg) * 2;

    $students = \Flow\get_available_students_with_question($n_required_questions);

    if($new_pyramid_size > count($students)) {
        $needed_students = $new_pyramid_size - count($students);
        //try to get the remaining users
        $no_question_students = \Flow\get_available_students_without_question($needed_students);
        $students = array_merge($students, $no_question_students);
    }

    if($new_pyramid_size > count($students)) {
        $needed_students = $new_pyramid_size - count($students);
        //try to get the remaining users
        $question_students = \Flow\get_available_students_with_question($needed_students, $students);
        $students = array_merge($students, $question_students);
    }

    //find the last pid
    $pid = null;
    $result = mysqli_query($link,"select * from pyramid_students where fid='$fid' order by pid desc limit 1");

    if(!mysqli_num_rows($result))
        $pid = 0;
    else {
        $result_row = mysqli_fetch_assoc($result);
        $pid = (int)$result_row['pid'] + 1;
    }

    //add selected student to the pyramid
    foreach($students as $student) {
        add_student($fid, $pid, $student);
    }

    //create the new pyramid structure
    create_pyramid_structure($fid, $pid, $students, $fl , $fsg);

    return $pid;
}

function distribute_group_members($fsg, $question_students, $no_question_students) {
    $n_groups = floor((count($question_students) + count($no_question_students))/$fsg);

    $groups = [];

    for($s=0;$s<$fsg;$s++) {
        for ($i = 0; $i < $n_groups; $i++) {
            if ($student = array_pop($question_students)) {
                $groups[$i][] = $student;
            } elseif ($student = array_pop($no_question_students)) {
                $groups[$i][] = $student;
            }
        }
    }

    $students = [];
    for ($i = 0; $i < $n_groups; $i++) {
        $students = array_merge($students, $groups[$i]);
    }


    while(count($question_students) + count($no_question_students)) {
        if ($student = array_pop($question_students)) {
            $students[] = $student;
        } elseif ($student = array_pop($no_question_students)) {
            $students[] = $student;
        }
    }

    return $students;
}

function create_pyramid_structure($fid, $pid, $sarry, $fl, $fsg) {
    global $link;

    if($fl < 1 || $fsg < 1)
        return false;

    //select students with a question
    $students_sql = implode('\',\'',$sarry);
    $result = mysqli_query($link, "select * from flow_available_students where fid='$fid' and sid in ('{$students_sql}') and sid in(select sid from flow_student where fid = '$fid')");

    $question_students = [];
    while($result_row = mysqli_fetch_assoc($result)) {
        $question_students[] = $result_row['sid'];
    }

    $no_question_students = [];
    foreach($sarry as $student) {
        if(!in_array($student, $question_students))
            $no_question_students[] = $student;
    }

    $student_array = distribute_group_members($fsg, $question_students, $no_question_students);

    $pyramid_list = noSc_pyramid($fl, $student_array, $fsg);

    for($tl=0; $tl<$fl; $tl++) {
        if($tl == 0) {
            $t_group_items = $pyramid_list[$tl][0];
            for($tin=0; $tin<count($t_group_items); $tin++) {
                $group_comma = implode(",",$t_group_items[$tin]);
                mysqli_query($link,"insert into pyramid_groups values ($fid, $pid, '$group_comma', '$tl', '$tin', '0', 0, 0, '', 0)");
                mysqli_query($link,"insert into pyramid_groups_og values ($fid, $pid, '$group_comma', '$tl', '$tin', '0', 0, 0, '', 0)");
            }
        } else {
            $t_group_items = $pyramid_list[$tl][0];
            $t_group_items_relation = $pyramid_list[$tl][1];
            for($tin=0; $tin<count($t_group_items); $tin++) {
                $group_comma = implode(",", $t_group_items[$tin]);
                $group_comma_relations = implode(",", $t_group_items_relation[$tin]);
                mysqli_query($link,"insert into pyramid_groups values ($fid, $pid, '$group_comma', '$tl', '$tin', '$group_comma_relations', 0, 0, '', 0)");
                mysqli_query($link,"insert into pyramid_groups_og values ($fid, $pid, '$group_comma', '$tl', '$tin', '$group_comma_relations', 0, 0, '', 0)");
            }
        }
    }

    return true;
}

function get_level_activity_rate($activity_level) {
    global $link, $pyramid_size, $fid, $pid, $ps, $flow_data, $peer_array, $pid, $activity_level, $peer_group_id;

    $activity_level = 0;
    mysqli_query($link, "start transaction");
    $result = mysqli_query($link, "select * from pyramid_groups where pg_fid='$fid' and pg_pid='$pid' and pg_level='{$activity_level}'");

    $groups = [];
    while($pg_row = mysqli_fetch_assoc($result)) {
        //$pid = (int)$pg_row['pg_pid'];
        $peer_group_id = (int)$pg_row['pg_group_id'];
        \Util\sql_gen();
        \Group\get_members_from_group_id();

        $total_peers = count($peer_array);
        $n_inactive_peers = count(\Pyramid\get_inactive_level_group_peers());
        $groups_score[$peer_group_id] = ($total_peers - $n_inactive_peers) / $total_peers;
    }

    arsort($groups_score);
    $groups_sorted = array_keys($groups_score);
    for($i=0;$i<floor(count($groups_score)/2);$i++) {
        $selected[0] = array_shift($groups_sorted);
        $selected[1] = array_pop($groups_sorted);
        $next_level[$i] = $selected;
    }

    $i=0;
    while($remaining = array_pop($groups_sorted)) {
        $next_level[$i++][] = $remaining;
    }

    mysqli_query($link, "begin transaction");
    foreach($next_level as $next_level_group_id => $next_level_combined_ids) {
        $next_level_combined_ids_string = implode(',', $next_level_combined_ids);
        $next_level_activity_level = $activity_level + 1;
        mysqli_query($link, "update pyramid_groups set pg_combined_group_ids='{$next_level_combined_ids_string}' where pg_fid='{$fid}' and pg_pid='{$pid}' and pg_level='{$next_level_activity_level}' and pg_group_id='{$next_level_group_id}'");
    }

    \Pyramid\update_pyramid($fid, $pid);
    mysqli_query($link, "commit");
    \Util\log(['activity' => 'group_activity_reorder', 'timestamp' => time(), 'origin' => 'php_backend', 'entry' => ['scores' => $groups_score, 'fid' => $fid, 'pid' => $pid, 'level' => $activity_level, 'next_level' => $next_level]]);
}

function get_pyramid_creation_timestamp() {
    global $link, $sid, $fid, $flow_data, $ps, $pid, $ftimestamp, $answer_timeout, $answer_skip_timeout, $peer_array;

    $r_start = mysqli_query($link, "select * from pyramid_students where timestamp > 0 and {$ps['e']} order by `timestamp` asc limit 1");

    if(!(mysqli_num_rows($r_start) > 0)) {
        return false;
    }

    $pyramid = mysqli_fetch_assoc($r_start);

    return (int)$pyramid['timestamp'];
}

function end_date_string($timestamp_level) {
    $timestamps = get_timestamps();

    $start_timestamp = $timestamps[$timestamp_level];

    $date_string = date("l jS G:i", $start_timestamp);

    return $date_string;
}

function get_timestamps() {
    global $flow_data;

    $init_day = get_pyramid_creation_timestamp();

    if(!$init_day)
        return false;

    $submission_timer = (int)$flow_data['question_timeout'];
    $rating_timer = (int)$flow_data['rating_timeout'];

    $level_timestamps = [
        $init_day,
        $init_day + 1*$rating_timer,
        $init_day + 2*$rating_timer,
        $init_day + 3*$rating_timer,
        $init_day + 4*$rating_timer,
    ];

    return $level_timestamps;
}

function set_pid($new_pid) {
    global $pid;
    
    $pid = $new_pid;
    \Util\sql_gen();
}