<?php

//compete logic about rating an answer in eah level during the activity and the output generation
$loop_count_group = 1;

for($activity_level = 0; $activity_level<$levels; $activity_level++)
{
    //find level info
    $pyramid_sql = get_sql_pyramid(['prefix'=>'pg']);
    $sa_result_1 = mysqli_query($link, "select * from pyramid_groups where {$pyramid_sql} and pg_level = '$activity_level'");
    if(mysqli_num_rows($sa_result_1) > 0){
        while($sa_data_1 = mysqli_fetch_assoc($sa_result_1))
        {
            $peer_array_temp = explode(",",$sa_data_1['pg_group']);
            if(in_array($sid,$peer_array_temp)){
                $peer_array = $peer_array_temp;
                $peer_group_id = $sa_data_1['pg_group_id'];
                $peer_group_combined_ids = $sa_data_1['pg_combined_group_ids'];
            }
        }
    }
    //$screen_output[$activity_level] = '<h4><span class="label label-info">Level '.$loop_count_group.'</span></h4>';

    //if first level - here generally 2 by 2 student groups (but there can be groups of 3 also)
    if($activity_level == 0){
        $pyramid_sql = get_sql_pyramid();
        $res4 = mysqli_query($link, "select * from flow_student where sid = '$sid' and {$pyramid_sql}");
        //the user already submitted the question
        if(mysqli_num_rows($res4) > 0){
            /*
            //TODO:wait?
            $data4 = mysqli_fetch_assoc($res4);
            //$flow_student_id = $data4['fs_id'];

            $screen_output[$activity_level] .= '<span>'.$data4['fs_answer'].'</span><br />';
            foreach($peer_array as $rate_peer_id){ //peer ids array

                if($sid != $rate_peer_id){ //print the answer of current student
                    $result_1 = mysqli_query($link, "select * from flow_student_rating where fsr_fid = '$fid' and fsr_sid= '$rate_peer_id' and fsr_to_whom_rated_id = '$sid' and fsr_level = '$activity_level'");
                    //this is to check whether peer has rated for a particular student, i.e. to_whom_rated = sid
                    if(mysqli_num_rows($result_1) > 0){
                        $data_1 = mysqli_fetch_assoc($result_1);
                        $screen_output[$activity_level] .= '<span class="text-primary small">Peer Rating: '.$data_1['fsr_rating'].'</span><br />';
                    }
                    else{
                        $screen_output[$activity_level] .= '<span class="small text-warning">Peer/s have not rated yet.</span><br />';
                    }
                }
            }
            */
        } else {
            //TODO:create form view
            //$screen_output[$activity_level] .= '<script type="text/JavaScript">timeoutPeriod = \'\';</script><form onsubmit="return del_vali();" role="form" action="" method="post"><div class="form-group"><textarea class="form-control" id="qa" name="qa">'.$data4['fs_answer'].'</textarea></div><div class="form-group"><input type="submit" class="btn btn-info" value="Submit" name="answer"></div><input type="hidden" name="a_lvl" value="'.$activity_level.'"><input type="hidden" name="a_peer_group_id" value="'.$peer_group_id.'"></form>';
            $body = View\element("question_form",array(
                'username'                  => $sname,
                'level'                     => 'Level '. ($activity_level) . '/' . $levels,
                'question_text'             => 'Write a question',
                'question_submit_button'    => 'Submit your question',
                'hidden_input_array' => array(
                    'a_lvl'             => $activity_level,
                    'a_peer_group_id'   => $peer_group_id,
                ),
            ));
            View\page(array(
                'title' => 'Rating',
                'body' => $body,
            ));
            exit;
        }
    }

    //Peer section	- get peer answers and let rate
    //if first level
    if($activity_level == 0){
        $hidden_input_array = array();
        $question_text_array = array();
        $i=1;
        foreach($peer_array as $rate_peer_id){

            if($sid != $rate_peer_id){//the other peers
                $screen_output[$activity_level] .= '<h5><span class="label label-default">Group peer: '.$rate_peer_id.'</span></h5>';
                $pyramid_sql = get_sql_pyramid();
                $res5 = mysqli_query($link, "select * from flow_student where sid = '$rate_peer_id' and {$pyramid_sql}");// to get peer answer
                if(mysqli_num_rows($res5) > 0) {//the peer already submitted the question
                    $data5 = mysqli_fetch_assoc($res5);
                    $peer_answer = $data5['fs_answer'];
                    //$peer_answer_id = $data5['fs_id'];

                    //check if rated
                    $pyramid_sql = get_sql_pyramid(['prefix'=>'fsr']);
                    $res6 = mysqli_query($link, "select * from flow_student_rating where {$pyramid_sql} and fsr_sid= '$sid' and fsr_to_whom_rated_id = '$rate_peer_id' and fsr_level = '$activity_level'");
                    if(mysqli_num_rows($res6) > 0){//rated
                        $data6 = mysqli_fetch_assoc($res6);
                        $screen_output[$activity_level] .= '<span>'.$peer_answer.'</span><br /><span class="text-primary small">You rated: '.$data6['fsr_rating'].'</span>';
                    }
                    else{//rate the peer
                        //just query the peers answers and proceed to the rating view
                        $question_text_array['optradio'.$i] = $peer_answer;
                        $hidden_input_array = array_merge(array(
                            'group_id'.$i          => $peer_group_id,
                            'to_whom_rated_id'.$i  => $rate_peer_id,
                            'lvl'.$i               => $activity_level,
                        ), $hidden_input_array);
                        $i++;

                        $screen_output[$activity_level] .= '<span><b>Peer said:</b></span><br />';
                        $screen_output[$activity_level] .= '<span>'.$peer_answer.'</span><br />';
                        //include('a_rating.php');
                        $screen_output[$activity_level] .= '<script type="text/JavaScript">timeoutPeriod = \'\';</script><form onsubmit="return del_vali2();" role="form" action="" method="post"><div class="form-group"><label>&nbsp; 1 <input type="radio" name="optradio1" value="1"></label><label>&nbsp; 2 <input type="radio" name="optradio1" value="2"></label><label>&nbsp; 3 <input type="radio" name="optradio1" value="3"></label><label>&nbsp; 4 <input type="radio" name="optradio1" value="4"> </label><label>&nbsp; 5 <input type="radio" name="optradio1" value="5"></label><input type="submit" class="btn btn-info btn-xs" value="Rate" name="rate"></div><input type="hidden" name="lvl1" value="'.$activity_level.'"><input type="hidden" name="to_whom_rated_id1" value="'.$rate_peer_id.'"><input type="hidden" name="group_id1" value="'.$peer_group_id.'"></form>';

                    }
                }
                else{//the peer did not submit the question
                    //TODO:wait
                    $screen_output[$activity_level] .= '<span class="small text-warning">Not submitted yet.</span><br /><br />';
                }
            }
        }

        //Proceed to rate the answers
        $hidden_input_array['numofqustions'] = $i-1;
        $body = View\element("question_rating",array(
            'username'    => $sname,
            'level'       => 'Level '. ($activity_level+1) . '/' . $levels,
            'header_text' => 'Rate the following questions',
            'question_text_array' => $question_text_array,
            'question_rate_submit' => 'Rate',
            'hidden_input_array' => $hidden_input_array,
        ));
        View\page(array(
            'title' => 'Rating',
            'body' => $body,
        ));
        exit;

    }
    else{ //for other levels, need the previously rated information

        if($activity_level != 0)
        {
            $sa_rated = ""; unset($sa_rated_amount); unset($sa_rated_id);
            unset($pre_task_completed);
            //find previous level selected answers
            $pyramid_sql = get_sql_pyramid(['prefix'=>'pg']);
            $sa_result_1 = mysqli_query($link, "select * from pyramid_groups where {$pyramid_sql} and pg_level = '$activity_level'");
            if(mysqli_num_rows($sa_result_1) > 0){ //get current level pyramid group info
                while($sa_data_1 = mysqli_fetch_assoc($sa_result_1))
                {
                    $peer_array_temp = explode(",",$sa_data_1['pg_group']);
                    if(in_array($sid,$peer_array_temp)){
                        $peer_array = $peer_array_temp;
                        $peer_group_id = $sa_data_1['pg_group_id'];
                        $peer_group_combined_ids = $sa_data_1['pg_combined_group_ids'];
                    }
                }
            }
            $peer_group_combined_ids_temp = explode(",",$peer_group_combined_ids);
            $pgcid_temp_count = 1;
            $activity_level_previous = $activity_level - 1;

            //check if groups completed previous task
            foreach($peer_group_combined_ids_temp as $pgcid_group_id_temp){
                $pyramid_sql = get_sql_pyramid(['prefix'=>'sa']);
                $sa_result_2 = mysqli_query($link, "select * from selected_answers where {$pyramid_sql} and sa_level = '$activity_level_previous' and sa_group_id = '$pgcid_group_id_temp'");
                if(mysqli_num_rows($sa_result_2) > 0){
                    $pre_task_completed[] = 1;
                }
                //TODO:just wait if others not ready
            }

            //check if rated
            $rate_peer_id = $selected_a_id;
            $pyramid_sql = get_sql_pyramid(['prefix'=>'fsr']);
            $sa_result_4 = mysqli_query($link, "select * from flow_student_rating where {$pyramid_sql} and fsr_sid= '$sid' and fsr_level = '$activity_level'");
            if(mysqli_num_rows($sa_result_4) > 0){
                while($sa_data_4 = mysqli_fetch_assoc($sa_result_4)){
                    $sa_rated = "yes";
                    $sa_rated_amount[] = $sa_data_4['fsr_rating'];
                    $sa_rated_id[] = 	$sa_data_4['fsr_to_whom_rated_id'];
                }
            }

            //if rated
            if($sa_rated == "yes"){

                //find for which answer rated
                for($sar = 0; $sar<count($sa_rated_id); $sar++)
                {
                    $sa_rated_id_temp = $sa_rated_id[$sar];
                    $pyramid_sql = get_sql_pyramid();
                    $sa_data_3 = mysqli_fetch_assoc(mysqli_query($link, "select * from flow_student where {$pyramid_sql} and sid = '$sa_rated_id_temp'"));
                    $selected_qa = $sa_data_3['fs_answer'];
                    $screen_output[$activity_level] .= '<span>'.$selected_qa.'</span><br /><span class="text-primary small">You rated: '.$sa_rated_amount[$sar].'</span><br />';
                }

                //check if peers rated
                $pyramid_sql = get_sql_pyramid(['prefix'=>'fsr']);
                $sa_result_5 = mysqli_query($link, "select * from flow_student_rating where {$pyramid_sql} and fsr_level = '$activity_level' and fsr_group_id = '$peer_group_id'");
                if(mysqli_num_rows($sa_result_5) != count($peer_array)){
                    //TODO:wait for others peers to rate
                    $screen_output[$activity_level] .= '<br /><b><span class="small text-warning">All peers have NOT rated yet.</b></span><br />';
                }
            }
            else{ //if this current student has not rated
                //TODO:proceed to rate
                $screen_output[$activity_level] .= '<br /><span class=""><b>Rate all according to your preference!</b></span><br />';
                $screen_output[$activity_level] .= '<form onsubmit="return del_vali2();" role="form" action="" method="post">';
                $hidden_frm_cnt = 1;
                $question_text_array = array();
                foreach($peer_group_combined_ids_temp as $pgcid_group_id_temp){
                    $pyramid_sql = get_sql_pyramid(['prefix'=>'sa']);
                    $sa_result_2 = mysqli_query($link, "select * from selected_answers where {$pyramid_sql} and sa_level = '$activity_level_previous' and sa_group_id = '$pgcid_group_id_temp'");
                    if(mysqli_num_rows($sa_result_2) > 0){
                        $sa_data_2 = mysqli_fetch_assoc($sa_result_2);
                        $selected_a_id = $sa_data_2['sa_selected_id'];
                        //get answer from id
                        $pyramid_sql = get_sql_pyramid();
                        $sa_data_3 = mysqli_fetch_assoc(mysqli_query($link, "select * from flow_student where {$pyramid_sql} and sid = '$selected_a_id'"));
                        $selected_qa = $sa_data_3['fs_answer'];

                        $screen_output[$activity_level] .= '<br /><span class=""><b>'.$pgcid_temp_count.'. '.$selected_qa.'</b></span><br />';
                        //var_dump($pre_task_completed);
                        //var_dump($peer_group_combined_ids_temp);

                        //rating
                        $rate_peer_id = $selected_a_id;
                        if(count($pre_task_completed) == count($peer_group_combined_ids_temp)){

                            //stop timer when rating form load.
                            if($hidden_frm_cnt == 1){ $screen_output[$activity_level] .= '<script type="text/JavaScript">timeoutPeriod = \'\';</script>'; }

                            //include('a_rating.php');
                            $screen_output[$activity_level] .= '<div class="form-group"><label>&nbsp; 1 <input type="radio" name="optradio'.$hidden_frm_cnt.'" value="1"></label><label>&nbsp; 2 <input type="radio" name="optradio'.$hidden_frm_cnt.'" value="2"></label><label>&nbsp; 3 <input type="radio" name="optradio'.$hidden_frm_cnt.'" value="3"></label><label>&nbsp; 4 <input type="radio" name="optradio'.$hidden_frm_cnt.'" value="4"></label><label>&nbsp; 5 <input type="radio" name="optradio'.$hidden_frm_cnt.'" value="5"></label></div><input type="hidden" name="lvl'.$hidden_frm_cnt.'" value="'.$activity_level.'"><input type="hidden" name="to_whom_rated_id'.$hidden_frm_cnt.'" value="'.$rate_peer_id.'"><input type="hidden" name="group_id'.$hidden_frm_cnt.'" value="'.$peer_group_id.'">';
                            $hidden_frm_cnt++;

                            //create the question array
                            $question_text_array['optradio'.$hidden_frm_cnt] = $selected_qa;
                            //
                        }
                    }
                    else{//no questions from the other peer, user must wait
                        $screen_output[$activity_level] .= '<br /><b><span class="small text-warning">'.$pgcid_temp_count.'. Has not completed yet.</b></span><br />';

                    }

                    $pgcid_temp_count++;

                }

                $rating_form = View\element("question_rating",array(
                    'username'    => $sname,
                    'level'       => 'Level '. ($activity_level+1) . '/' . $levels,
                    'header_text' => 'Rate the following questions',
                    'question_text_array' => $question_text_array,
                    'question_rate_submit' => 'Rate',
                    'hidden_input_array' => array(
                        'group_id'.$hidden_frm_cnt          => $peer_group_id,
                        'to_whom_rated_id'.$hidden_frm_cnt  => $rate_peer_id,
                        'lvl'.$hidden_frm_cnt               => $activity_level,
                        'numofqustions'                     => count($peer_group_combined_ids_temp),
                    ),
                ));
                View\page(array(
                    'title' => 'Rating',
                    'body' => $body,
                ));
                exit;

                $screen_output[$activity_level] .= '<input type="hidden" name="numofqustions" value="'.count($peer_group_combined_ids_temp).'"><br /><div class="form-group"><input type="submit" class="btn btn-info btn-xs" value="Rate" name="rate"></div>';	//to know the no of submitted ratings
                $screen_output[$activity_level] .= '</form>';

            }//not rated

            $screen_output[$activity_level] .= '<h5><span class="label label-default">Group peers: '.implode(", ",$peer_array).'</span></h5>';
        }

    }

    $group_size = count($peer_array); //no of peers in the branch
    if($loop_count_group == 1)
    {
        $needed_results = $group_size * ($group_size-1); //in the first level, it's no. of choices * student count
    }
    else{
        $st_count = count($peer_group_combined_ids_temp);
        $needed_results = $group_size * $st_count; //because now every student is rating two answers, need to occupy all answers
    }

    $pyramid_sql = get_sql_pyramid(['prefix'=>'fsr']);
    $result_2= mysqli_query($link, "select * from flow_student_rating where {$pyramid_sql} and fsr_level = '$activity_level' and fsr_group_id = '$peer_group_id'");
    if(mysqli_num_rows($result_2) != $needed_results){
        continue;
    }
    else{ //to sum the ratings
        $pyramid_sql = get_sql_pyramid(['prefix'=>'fsr']);
        $result_3= mysqli_query($link, "SELECT fsr_to_whom_rated_id, SUM(fsr_rating) as sum FROM `flow_student_rating` where {$pyramid_sql} and fsr_level = '$activity_level' and fsr_group_id = '$peer_group_id' group by fsr_to_whom_rated_id order by SUM(fsr_rating) desc limit 1");
        $data_t_2 = mysqli_fetch_assoc($result_3);

        $selected_id = $data_t_2['fsr_to_whom_rated_id'];
        $selected_id_rating_sum = $data_t_2['sum'];
        mysqli_query($link,"insert into selected_answers values ('$fid', '$activity_level', '$peer_group_id', '$selected_id', '$selected_id_rating_sum')");

        $loop_count_group++;

        //last level-- to show selected answers
        if($activity_level == $levels-1){
            //all users answered so proceed to show the final results
            $pyramid_sql['pg'] = get_sql_pyramid(['prefix'=>'pg']);
            $pyramid_sql['sa'] = get_sql_pyramid(['prefix'=>'sa']);
            if( mysqli_num_rows(mysqli_query($link, "select * from pyramid_groups where {$pyramid_sql['pg']}")) == mysqli_num_rows(mysqli_query($link, "select * from selected_answers where {$pyramid_sql['sa']}")) ){

                $result_11= mysqli_query($link, "select * from selected_answers where $pyramid_sql['sa'] and sa_level = '$activity_level'");
                $screen_output[$levels] .= '<br /><h4><span class="label btn-success">Activity is completed!! &nbsp;- &nbsp; Final selections are:</span></h4><br />';
                while($data_t_11 = mysqli_fetch_assoc($result_11)){
                    $qa_last_selected_id = $data_t_11['sa_selected_id'];
                    $pyramid_sql = get_sql_pyramid();
                    $result_12= mysqli_query($link, "select * from flow_student where {$pyramid_sql} and sid = '$qa_last_selected_id'");
                    $data_t_12 = mysqli_fetch_assoc($result_12);
                    $screen_output[$levels] .= '<br /><span class=""><B>'.$data_t_12['fs_answer'].'</B></span><br />';
                }
            }
            else{//user finished but waiting for others
                $screen_output[$levels] .= '<br /><span class="label btn-warning">Activity is completed! &nbsp;- &nbsp; Wait till others finish.</span><br /><br />';
            }
        }
    }
}//main group loop

if(!empty($screen_output[$levels])){
    echo $screen_output[$levels];
}
else{
    echo $screen_output[$activity_level];
}
