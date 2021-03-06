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
//$an = new GCMPushMessage($apiKey);
//$an->setDevices($devices);

global $link, $fid, $pid, $pyramid_minsize, $levels, $flow_data, $activity_level, $peer_group_id, $n_selected_answers, $random_selection;

//email
$email_sent = [
];

date_default_timezone_set("Europe/Berlin");

echo "init passed\n";
while(true) {
    $flows = \Pyramid\get_flows(0);
    sleep(1);
    echo "iteration started\n";

    foreach ($flows as $current_flow) {
        \Pyramid\set_current_flow($current_flow);
        echo $fid . "\n";

        $time = \Util\pyramid_time();

        //TODO: check for not empty pyramids, the there are none just skip
        $unfilled_pyramids = \Flow\get_not_full_pyramids();
        $available_students = \Flow\get_available_students();

        if (count($unfilled_pyramids) and count($available_students)) {

            $nleft_to_assign = count($available_students);
            $i = 0;
            mysqli_query($link, "begin transaction");
            while ($nleft_to_assign > 0 and isset($unfilled_pyramids[$i])) {
                $pid_to_update = $unfilled_pyramids[$i]['pid'];
                $n_users_to_add = min($unfilled_pyramids[$i]['slots'], $nleft_to_assign);
                \Pyramid\update_pyramid($fid, $pid_to_update, $n_users_to_add);
                \Util\log(['activity' => 'added_latecomers_to_pyramid', 'users' => $users]);
                $nleft_to_assign -= $n_users_to_add;
                $i++;
            }
            mysqli_query($link, "commit");

            echo "users added and pyramid updated\n";
            continue;
        }

        /*
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
    */

        //check for new pyramid creation
        $last_pyramid_expired_timestamp = \Flow\get_last_pyramid_expired_timestamp();
        $flow_timestamps = \Flow\get_timestamps();

        $question_submit_expiry_timestamp = $flow_timestamps[0];
        $new_pyramid_size = $flow_data['pyramid_size'];

        if (\Util\pyramid_time() > $question_submit_expiry_timestamp and count($available_students) >= $new_pyramid_size) {
            //create the pyramids, must be executed once
            echo "group formation stage\n";

            $created = false;

            //available users
            $nflow_students = count($available_students);

            if ($nflow_students > 0) {
                $nleft_to_assign = $nflow_students;

                mysqli_query($link, "start transaction");
                mysqli_query($link, "start transaction");

                $new_pid_list = [];
                while ($nleft_to_assign >= $new_pyramid_size) {
                    try {
                        $new_pid_list[] = \Pyramid\create_pyramid($fid, $flow_data['levels'], $flow_data['nostupergrp'], $new_pyramid_size);
                        \Util\log(['activity' => 'new_pyramid']);
                        $created = true;
                        echo "created a new pyramid\n";
                    } catch (Exception $e) {

                    }
                    $nleft_to_assign -= $new_pyramid_size;
                }
                mysqli_query($link, "update pyramid_groups set pg_started = 1, pg_start_timestamp='{$time}' where pg_started = '0' and pg_fid='{$fid}' and pg_level='0'");
                mysqli_query($link, "commit");

                //remaining users add added to the last pyramid
                if ($nleft_to_assign > 0) {
                    $n_users_to_add = min($nleft_to_assign, $new_pyramid_size - 1);
                    $pid_to_update = \Flow\get_last_pyramid_id();

                    mysqli_query($link, "begin transaction");
                    \Pyramid\update_pyramid($fid, $pid_to_update, $n_users_to_add);
                    \Util\log(['activity' => 'added_latecomers_to_pyramid', 'users' => $users]);
                    mysqli_query($link, "commit");
                }

                echo "users added and pyramid updated\n";

                mysqli_query($link, "commit");

                $step = 1;
                try {
                    foreach ($new_pid_list as $created_pid) {
                        \Pyramid\set_pid($created_pid);
                        if ($created and empty($email_sent[$fid][$pid][$step])) {
                            $email_sent[$fid][$pid][$step] = true;
                            $recipients = \Util\get_users_email($fid, $pid);
                            $html = \Util\get_html($step);
                            if (!empty($recipients))
                                \Util\notification_mail($recipients, $html);
                        }

                        //$response = $an->send("Next level is ready! Start discussing. Rating is allowed till 27th, 6pm CET.");
                    }
                } catch (Exception $e) {
                }
            }
        }

        $pyramid_ids = \Flow\get_pyramid_ids();

        foreach ($pyramid_ids as $pyramid_id) {
            \Pyramid\set_pid($pyramid_id);
            $level_timestamps = \Pyramid\get_timestamps();

            //discard expired pyramids
            if (\Util\pyramid_time() > $level_timestamps[($levels + 1)])
                continue;

            //TODO: this must be done per pyramid? calculate level_timestamps per pyramid
            if ($time <= $level_timestamps[0]) {
                //do nothing and wait for users to submit questions
                echo "question stage\n";
                continue;
            } elseif ($time <= $level_timestamps[1]) {
                //create the pyramids, must be executed once
                echo "first rating stage\n";
                continue;

            } elseif ($time <= $level_timestamps[2] and $levels > 1) {
                //select the answers and enable the level
                echo "select answers stage\n";

                $activity_level = 0;
                $next_level = $activity_level + 1;

                //rating started
                $result = mysqli_query($link, "select * from pyramid_groups where pg_fid='$fid' and pg_pid='$pid' and pg_level = {$next_level} and pg_started = 1");
                if (mysqli_num_rows($result) > 0)
                    continue;

                mysqli_query($link, "start transaction");
                $result = mysqli_query($link, "select * from pyramid_groups where pg_fid='$fid' and pg_pid='$pid' and pg_level='{$activity_level}'");
                while ($pg_row = mysqli_fetch_assoc($result)) {
                    \Group\set_group_id((int)$pg_row['pg_group_id']);

                    if (!\Pyramid\set_selected_answers()) {
                        //the conditions to select a question are not met
                        mysqli_query($link, "insert into selected_answers values ('$fid', '$pid', '$activity_level', '$peer_group_id', '-1', '0', '1', FROM_UNIXTIME({$time}))");
                        \Util\log(['activity' => 'level_finished_with_no_rates']);
                    }
                }

                \Pyramid\get_level_activity_rate($activity_level);

                mysqli_query($link, "update pyramid_groups set pg_started = 1, pg_start_timestamp='{$time}' where pg_started = '0' and pg_fid='{$fid}'  and pg_pid='{$pid}' and pg_level={$next_level}");
                mysqli_query($link, "commit");

                $step = $activity_level + 2;
                try {
                    if (empty($email_sent[$fid][$pid][$step])) {
                        $email_sent[$fid][$pid][$step] = true;
                        $recipients = \Util\get_users_email($fid, $pid);
                        $html = \Util\get_html($step);
                        if (!empty($recipients))
                            \Util\notification_mail($recipients, $html);

                        //$response = $an->send("Next level is ready! Start discussing. Rating is allowed till 28th, 12am CET.");
                    }
                } catch (Exception $e) {
                }

                continue;
            } elseif ($time <= $level_timestamps[3] and $levels > 2) {
                //select the answers and enable the level
                echo "selection answers stage\n";

                $activity_level = 1;
                $next_level = $activity_level + 1;

                //rating started
                $result = mysqli_query($link, "select * from pyramid_groups where pg_fid='$fid' and pg_pid='$pid' and pg_level = {$next_level} and pg_started = 1");
                if (mysqli_num_rows($result) > 0)
                    continue;

                mysqli_query($link, "start transaction");
                $result = mysqli_query($link, "select * from pyramid_groups where pg_fid='$fid' and pg_pid='$pid' and pg_level='{$activity_level}'");
                while ($pg_row = mysqli_fetch_assoc($result)) {
                    \Group\set_group_id((int)$pg_row['pg_group_id']);

                    if (!\Pyramid\set_selected_answers()) {
                        //the conditions to select a question are not met
                        mysqli_query($link, "insert into selected_answers values ('$fid', '$pid', '$activity_level', '$peer_group_id', '-1', '0', '1', FROM_UNIXTIME({$time}))");
                        \Util\log(['activity' => 'level_finished_with_no_rates']);
                    }
                }

                \Pyramid\get_level_activity_rate($activity_level);

                mysqli_query($link, "update pyramid_groups set pg_started = 1, pg_start_timestamp='{$time}' where pg_started = '0' and pg_fid='{$fid}'  and pg_pid='{$pid}' and pg_level={$next_level}");
                mysqli_query($link, "commit");

                $step = $activity_level + 2;
                try {
                    if (empty($email_sent[$fid][$pid][$step])) {
                        $email_sent[$fid][$pid][$step] = true;
                        $recipients = \Util\get_users_email($fid, $pid);
                        $html = \Util\get_html($step);
                        if (!empty($recipients))
                            \Util\notification_mail($recipients, $html);

                        //$response = $an->send("Next level is ready! Start discussing. Rating is allowed till 28th, 12am CET.");
                    }
                } catch (Exception $e) {
                }

                continue;
            } elseif ($time <= $level_timestamps[4] and $levels > 3) {
                //select the answers and enable the level
                echo "selection answers stage\n";

                $activity_level = 2;
                $next_level = $activity_level + 1;

                //rating started
                $result = mysqli_query($link, "select * from pyramid_groups where pg_fid='$fid' and pg_pid='$pid' and pg_level = {$next_level} and pg_started = 1");
                if (mysqli_num_rows($result) > 0)
                    continue;

                mysqli_query($link, "start transaction");
                $result = mysqli_query($link, "select * from pyramid_groups where pg_fid='$fid' and pg_pid='$pid' and pg_level='{$activity_level}'");
                while ($pg_row = mysqli_fetch_assoc($result)) {
                    \Group\set_group_id((int)$pg_row['pg_group_id']);

                    if (!\Pyramid\set_selected_answers()) {
                        //the conditions to select a question are not met
                        mysqli_query($link, "insert into selected_answers values ('$fid', '$pid', '$activity_level', '$peer_group_id', '-1', '0', '1', FROM_UNIXTIME({$time}))");
                        \Util\log(['activity' => 'level_finished_with_no_rates']);
                    }
                }

                \Pyramid\get_level_activity_rate($activity_level);

                mysqli_query($link, "update pyramid_groups set pg_started = 1, pg_start_timestamp='{$time}' where pg_started = '0' and pg_fid='{$fid}'  and pg_pid='{$pid}' and pg_level={$next_level}");
                mysqli_query($link, "commit");

                $step = $activity_level + 2;
                try {
                    if (empty($email_sent[$fid][$pid][$step])) {
                        $email_sent[$fid][$pid][$step] = true;
                        $recipients = \Util\get_users_email($fid, $pid);
                        $html = \Util\get_html($step);
                        if (!empty($recipients))
                            \Util\notification_mail($recipients, $html);

                        //$response = $an->send("Next level is ready! Start discussing. Rating is allowed till 28th, 12am CET.");
                    }
                } catch (Exception $e) {
                }

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
            if (!$email_sent[$step]) {
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
                $result = mysqli_query($link, "select * from selected_answers where sa_fid='$fid' and sa_pid='$pid' and sa_level = {$activity_level}");
                if (mysqli_num_rows($result) > 0)
                    continue;

                //prevent race condition with the last flow or incorrect timestamp
                $result = mysqli_query($link, "select * from selected_answers where sa_fid='$fid' and sa_pid='$pid' and sa_level = {$previous_level}");
                if (!(mysqli_num_rows($result) > 0))
                    continue;

                //select the answers and enable the level
                $n_selected_answers = 1;
                $random_selection = false;

                $result = mysqli_query($link, "select * from pyramid_groups where pg_fid='$fid' and pg_pid='$pid' and pg_level='{$activity_level}'");
                while ($pg_row = mysqli_fetch_assoc($result)) {
                    //$pid = (int)$pg_row['pg_pid'];
                    $peer_group_id = (int)$pg_row['pg_group_id'];
                    \Util\sql_gen();
                    \Group\get_members_from_group_id();

                    if (!\Pyramid\set_selected_answers()) {
                        //the conditions to select an question are not meet
                        mysqli_query($link, "insert into selected_answers values ('$fid', '$pid', '$activity_level', '$peer_group_id', '-1', '0', '1', FROM_UNIXTIME({$time}))");
                        \Util\log(['activity' => 'level_finished_with_no_rates']);
                    }
                }

                $step = 99;
                try {
                    if (empty($email_sent[$fid][$pid][$step])) {
                        $email_sent[$fid][$pid][$step] = true;
                        $recipients = \Util\get_users_email($fid, $pid);
                        $html = \Util\get_html($step);
                        if (!empty($recipients))
                            \Util\notification_mail($recipients, $html);

                        // = $an->send("We have the winning questions!");
                    }
                } catch (Exception $e) {
                }

                continue;
            }
        }

    }
}
