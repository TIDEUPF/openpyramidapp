<?php

global $ldshake_mode;

//TODO: mandatory ldshake token for students

$error;
global $force_email;

if($ldshake_mode) {
    \ldshake\check_student_session_flow();
    \Pyramid\get_current_flow();
}

if(empty($_SESSION['user_id'])) {
    session_start(/*['read_and_close' => true]*/);
    $_SESSION['user_id'] = sha1(mt_rand(0,9999999999));
    session_write_close();
}

if(!isset($_SESSION['student'])) {
    if(isset($_POST['loginBtn'])) {
        if(empty($_POST['usr'])/* or empty($_REQUEST['code'])*/) {

            $error = "UserId can not be empty!";
        }
        //the following is the correct student login that cross-refer with the student excel table
        else{
            //$code = mysqli_real_escape_string($link,trim($_REQUEST['code']));
            $uname = mysqli_real_escape_string($link, stripslashes(strtoupper(trim(strip_tags($_POST['usr'])))));

            $sname = strtolower($uname);
            $sname[0] = strtoupper($sname[0]);
            $sname = str_replace(array('*', "'", ',', ' ', '"', '(', ')', '<', '>', '=', ';', '#', '/', '$', '%', '\\', '`'), '', $sname);
            $uname = str_replace(array('*', "'", ',', ' ', '"', '(', ')', '<', '>', '=', ';', '#', '/', '$', '%', '\\', '`'), '', $uname);

            $is_email = false;
            if(filter_var($sname, FILTER_VALIDATE_EMAIL)) {
                $email_split = explode('@', $sname);
                $sname = $email_split[0];
                $is_email = true;
            }

            if($force_email and !$is_email) {
                $error = T('You need to introduce a valid email address.');
            } else {
                mysqli_query($link, "insert into students values ('$uname', '$sname', NOW() )");
                if (mysqli_affected_rows($link) > 0) {
                    session_start(/*['read_and_close' => true]*/);
                    $_SESSION['student'] = $uname;
                    $_SESSION['sname'] = $sname;
                    session_write_close();
                } else {
                    $res2 = mysqli_query($link, "select * from students where sid = '$uname'");
                    if (mysqli_num_rows($res2) <= 0) {
                        $error = 'Database error!';
                    } else {
                        session_start(/*['read_and_close' => true]*/);
                        $_SESSION['student'] = $uname;
                        $_SESSION['sname'] = $sname;
                        session_write_close();
                    }
                }
            }
        }
    }
}

if(!\Student\get_username()) {
    session_start(/*['read_and_close' => true]*/);
    unset($_SESSION['student']);
    unset($_SESSION['sname']);
    session_write_close();
}

if(!isset($_SESSION['student'])) {
    $vars = array();
    if(!empty($error))
        $vars['error'] = $error;

    $vars['hidden_input_array'] = [];
    $hidden_input_array['page'] = "student_login";
    $vars['hidden_input_array'] = array_merge($vars['hidden_input_array'], $hidden_input_array);
    $vars['force_email'] = $force_email;
    $login_form = View\element("login", $vars);

    \View\page(array(
        'title' => 'Student Login',
        'body' => $login_form,
    ));
} else {//successfull login
    global $flow_data;

    $late_user = false;
    $first_time_user = false;
    //register the user in the current flow
    if($fid = \Pyramid\get_current_flow()) {

        //late user
        if((int)$flow_data['sync'] == 0) {
            if($flow_data['no_submit'] == 0) {
                $gcal_result_1 = mysqli_query($link, "select * from pyramid_groups where pg_level = 0 and pg_started = 1 and pg_fid='{$fid}'");
            } else {
                $gcal_result_1 = mysqli_query($link, "select * from pyramid_groups where pg_level = 1 and pg_started = 1 and pg_fid='{$fid}'");
            }
            if (mysqli_num_rows($gcal_result_1) > 0) {
                $late_user = true;
            }
        }

        //first time user
        $user_flow_result = mysqli_query($link, "select * from flow_available_students where fid='{$fid}' and sid='{$_SESSION['student']}'");
        if (!(mysqli_num_rows($user_flow_result) > 0)) {
            $first_time_user = true;
        }

        \Pyramid\flow_add_student($fid, $_SESSION['student']);
    }

    $vars = [];
    $vars['hidden_input_array'] = [];
    $vars['late_user'] = $late_user;

    $hidden_input_array['username'] = $_SESSION['sname'];
    $hidden_input_array['fid'] = $fid;
    $hidden_input_array['level'] = 1;
    $hidden_input_array['page'] = "activity_explanation";

    $vars['hidden_input_array'] = array_merge($vars['hidden_input_array'], $hidden_input_array);


    header("location: student.php");

    /*
    if($first_time_user and (int)$flow_data['sync'] == 0) {
        //show activity explanation
        $activity_explanation_view = View\element("activity_explanation", $vars);

        \View\page(array(
            'title' => 'Activity explanation',
            'body' => $activity_explanation_view,
        ));
    } else {
        header("location: student.php");
    }*/
    exit;
}