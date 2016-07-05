<?php

include_once('../init.php');
require_once '../vendors/GCM/GCMPushMessage.php';
require_once 'Mail.php';
include_once 'Mail/mime.php';

$apiKey = "AIzaSyCvCBiO1YpIani9UnKRXN6ZrVCDs_yTaN8";
//$apiKey = "AIzaSyAxjHJf7HPlq0xiQEd3IZFy8QUKDQPsZZs";
//$apiKey = "";
$devices = "/topics/global";
//$message = "Next level is ready! Start discussing.";
$an = new GCMPushMessage($apiKey);
$an->setDevices($devices);

global $link, $fid, $pid, $pyramid_minsize, $levels, $flow_data, $activity_level, $peer_group_id, $n_selected_answers, $random_selection;

//email
$email_sent = [
    true,
    false,
    false,
    false,
];

echo "init passed\n";
while(true) {
    sleep(1);

    echo "iteration started\n";

    //there is no flow
    if (!\Pyramid\get_current_flow())
        continue;


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

    $time = time();

    //add the latecomers if there are pyramids created
    $result = mysqli_query($link, "select pg_pid as pid from pyramid_groups where pg_fid='$fid' order by pg_pid desc limit 1");
    if(mysqli_num_rows($result)>0) {
        $result_avail = mysqli_query($link, "select distinct * from flow_available_students where fid='$fid' and sid not in (select sid from pyramid_students where fid = '$fid')");
        $nflow_students = mysqli_num_rows($result_avail);
        if($nflow_students > 0) {

            while ($user = mysqli_fetch_assoc($result_avail))
                $users[] = $user['sid'];

            $pid_row = mysqli_fetch_assoc($result);
            $n_pid = $pid_row['pid'];

            $n_users_to_add = $nflow_students / ($n_pid + 1);
            $n_users_to_add = floor($n_users_to_add) > 0 ? floor($n_users_to_add) : 1;

            $start_pid = mt_rand(0, 9999);
            $nleft_to_assign = $nflow_students;

            mysqli_query($link, "begin transaction");
            while ($nleft_to_assign > 0) {
                $pid_to_update = $start_pid % ($n_pid + 1);
                $start_pid++;
                \Pyramid\update_pyramid($fid, $pid_to_update, $n_users_to_add);
                \Util\log(['activity' => 'added_latecomers_to_pyramid', 'users' => $users]);

                $nleft_to_assign -= $n_users_to_add;
            }
            mysqli_query($link, "commit");

            echo "users added and pyramid updated\n";
            continue;
        }
    }

    if($time <= $level_timestamps[0]) {
        //do nothing and wait for users to submit questions
        echo "question stage\n";
        continue;
    } elseif($time <= $level_timestamps[1]) {
        //create the pyramids, must be executed once
        echo "group formation stage\n";

        $result = mysqli_query($link, "select * from pyramid_groups where pg_fid='$fid' and pg_level = 0 and pg_started = 1");
        if(mysqli_num_rows($result)>0)
            continue;

        $created=false;
        //available users
        $result_avail = mysqli_query($link, "select distinct * from flow_available_students where fid='$fid' and sid not in (select sid from pyramid_students where fid = '$fid')");
        $nflow_students = mysqli_num_rows($result_avail);

        //users assigned to a pyramid
        $result_assign = mysqli_query($link, "select sid from pyramid_students where fid = '$fid'");
        $npy_students = mysqli_num_rows($result_assign);

        if (!($npy_students > 0)) {
            $nleft_to_assign = $nflow_students;

            $new_pyramid_size = $flow_data['pyramid_size'];
            mysqli_query($link, "start transaction");
            mysqli_query($link, "start transaction");
            while ($nleft_to_assign >= $new_pyramid_size) {
                try {
                    \Pyramid\create_pyramid($fid, $flow_data['levels'], $flow_data['nostupergrp'], $new_pyramid_size);
                    \Util\log(['activity' => 'new_pyramid']);
                    $created=true;
                    echo "created a new pyramid\n";
                } catch (Exception $e) {

                }
                $nleft_to_assign -= $new_pyramid_size;
            }
            mysqli_query($link, "update pyramid_groups set pg_started = 1, pg_start_timestamp='{$time}' where pg_started = '0' and pg_fid='{$fid}' and pg_level='0'");
            mysqli_query($link, "commit");

            //remaining
            $result = mysqli_query($link, "select pg_pid as pid from pyramid_groups where pg_fid='$fid' order by pg_pid desc limit 1");
            if(mysqli_num_rows($result)>0) {
                $result_avail = mysqli_query($link, "select distinct * from flow_available_students where fid='$fid' and sid not in (select sid from pyramid_students where fid = '$fid')");
                $nflow_students = mysqli_num_rows($result_avail);
                if($nflow_students > 0) {

                    while ($user = mysqli_fetch_assoc($result_avail))
                        $users[] = $user['sid'];

                    $pid_row = mysqli_fetch_assoc($result);
                    $n_pid = $pid_row['pid'];

                    $n_users_to_add = $nflow_students / ($n_pid + 1);
                    $n_users_to_add = floor($n_users_to_add) > 0 ? floor($n_users_to_add) : 1;

                    $start_pid = mt_rand(0, 9999);
                    $nleft_to_assign = $nflow_students;

                    mysqli_query($link, "begin transaction");
                    while ($nleft_to_assign > 0) {
                        $pid_to_update = $start_pid % ($n_pid + 1);
                        $start_pid++;
                        \Pyramid\update_pyramid($fid, $pid_to_update, $n_users_to_add);
                        \Util\log(['activity' => 'added_latecomers_to_pyramid', 'users' => $users]);

                        $nleft_to_assign -= $n_users_to_add;
                    }
                    mysqli_query($link, "commit");

                    echo "users added and pyramid updated\n";
                }
            }

            mysqli_query($link, "commit");

            $step = 1;
            try {
                if ($created and !$email_sent[$step]) {
                    $email_sent[$step] = true;
                    $recipients = \Util\get_users_email();
                    $html = \Util\get_html($step);
                    if (!empty($recipients))
                        \Util\notification_mail($recipients, $html);

                    //$response = $an->send("Next level is ready! Start discussing. Rating is allowed till 27th, 6pm CET.");
                }
            } catch(Exception $e) {}
            continue;
        }

    } elseif($time <= $level_timestamps[2]) {
        //select the answers and enable the level
        echo "selection answers 1 stage\n";

        $result = mysqli_query($link, "select * from pyramid_groups where pg_fid='$fid' and pg_level = 1 and pg_started = 1");
        if(mysqli_num_rows($result)>0)
            continue;

        $activity_level = 0;
        mysqli_query($link, "start transaction");
        $result = mysqli_query($link, "select * from pyramid_groups where pg_fid='$fid' and pg_level='{$activity_level}'");
        while($pg_row = mysqli_fetch_assoc($result)) {
            $pid = (int)$pg_row['pg_pid'];
            $peer_group_id = (int)$pg_row['pg_group_id'];
            \Util\sql_gen();
            \Group\get_members_from_group_id();

            if(!\Pyramid\set_selected_answers()) {
                //the conditions to select a question are not met
                mysqli_query($link, "insert into selected_answers values ('$fid', '$pid', '$activity_level', '$peer_group_id', '-1', '0', '1', FROM_UNIXTIME({$time}))");
                \Util\log(['activity' => 'level_finished_with_no_rates']);
            }
        }

        \Pyramid\get_level_activity_rate();

        mysqli_query($link, "update pyramid_groups set pg_started = 1, pg_start_timestamp='{$time}' where pg_started = '0' and pg_fid='{$fid}' and pg_level='1'");
        mysqli_query($link, "commit");

        $step = 2;
        try {
            if ($created and !$email_sent[$step]) {
                $email_sent[$step] = true;
                $recipients = \Util\get_users_email();
                $html = \Util\get_html($step);
                if (!empty($recipients))
                    \Util\notification_mail($recipients, $html);

                $response = $an->send("Next level is ready! Start discussing. Rating is allowed till 28th, 12am CET.");
            }
        } catch(Exception $e) {}

        continue;
    } /*elseif($time <= $level_timestamps[3]) {
        //select the answers and enable the level
        echo "selection answers 2 stage\n";
        $activity_level = 1;

        $result = mysqli_query($link, "select * from pyramid_groups where pg_fid='$fid' and pg_level = 2 and pg_started = 1");
        if(mysqli_num_rows($result)>0)
            continue;

        mysqli_query($link, "start transaction");
        $result = mysqli_query($link, "select * from pyramid_groups where pg_fid='$fid' and pg_level='{$activity_level}'");
        while($pg_row = mysqli_fetch_assoc($result)) {
            $pid = (int)$pg_row['pg_pid'];
            $peer_group_id = (int)$pg_row['pg_group_id'];
            \Util\sql_gen();
            \Group\get_members_from_group_id();

            if(!\Pyramid\set_selected_answers()) {
                //the conditions to select an question are not meet
                mysqli_query($link, "insert into selected_answers values ('$fid', '$pid', '$activity_level', '$peer_group_id', '-1', '0', '1', FROM_UNIXTIME({$time}))");
                \Util\log(['activity' => 'level_finished_with_no_rates']);
            }
        }

        mysqli_query($link, "update pyramid_groups set pg_started = 1, pg_start_timestamp='{$time}' where pg_started = '0' and pg_fid='{$fid}' and pg_level='2'");
        mysqli_query($link, "commit");

        $step = 3;
        try {
            if ($created and !$email_sent[$step]) {
                $email_sent[$step] = true;
                $recipients = \Util\get_users_email();
                $html = \Util\get_html($step);
                if (!empty($recipients))
                    \Util\notification_mail($recipients, $html);
            }
        } catch(Exception $e) {}

        continue;
    }*/ else {
        //final day, select the final result
        echo "final stage\n";
        $activity_level = $levels - 1;
        $previous_level = $activity_level - 1;
        $result = mysqli_query($link, "select * from selected_answers where sa_fid='$fid' and sa_level = {$activity_level}");
        if(mysqli_num_rows($result)>0)
            continue;

        //prevent race condition with the last flow or incorrect timestamp
        $result = mysqli_query($link, "select * from selected_answers where sa_fid='$fid' and sa_level = {$previous_level}");
        if(!(mysqli_num_rows($result)>0))
            continue;

        //select the answers and enable the level
        $n_selected_answers = 1;
        $random_selection = false;

        $result = mysqli_query($link, "select * from pyramid_groups where pg_fid='$fid' and pg_level='{$activity_level}'");
        while($pg_row = mysqli_fetch_assoc($result)) {
            $pid = (int)$pg_row['pg_pid'];
            $peer_group_id = (int)$pg_row['pg_group_id'];
            \Util\sql_gen();
            \Group\get_members_from_group_id();

            if(!\Pyramid\set_selected_answers()) {
                //the conditions to select an question are not meet
                mysqli_query($link, "insert into selected_answers values ('$fid', '$pid', '$activity_level', '$peer_group_id', '-1', '0', '1', FROM_UNIXTIME({$time}))");
                \Util\log(['activity' => 'level_finished_with_no_rates']);
            }
        }

        $step = 4;
        try {
            if ($created and !$email_sent[$step]) {
                $email_sent[$step] = true;
                $recipients = \Util\get_users_email();
                $html = \Util\get_html($step);
                if (!empty($recipients))
                    \Util\notification_mail($recipients, $html);

                $response = $an->send("We have the winning questions!");
            }
        } catch(Exception $e) {}

        continue;
    }


}
