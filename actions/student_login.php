<?php
$error;



if(empty($_SESSION['user_id'])) {
    $_SESSION['user_id'] = sha1(mt_rand(0,9999999999));
}

if(!isset($_SESSION['student'])) {
    if(isset($_POST['loginBtn'])) {
        if(empty($_POST['usr'])) {

            $error = "UserId can not be empty!";
        }
        //the following is the correct student login that cross-refer with the student excel table
        else{
            $uname = mysqli_real_escape_string($link, stripslashes(strtoupper(trim(strip_tags($_POST['usr'])))));

            //$res1 = mysqli_query($link, "select * from studentexcel where se_sid = '$uname'");
            //if(mysqli_num_rows($res1) > 0) {
            //if(false){
                //$data1 = mysqli_fetch_assoc($res1);
              //  $sname = $data1['se_sname'];

                //$res2 = mysqli_query($link, "select * from students where sid = '$uname'");
               // if(mysqli_num_rows($res2) > 0) {
                    //already in table
                    //$_SESSION['student'] = $uname;
                    //header("location: student.php"); exit(0);
                    //header("location: student_activity.php"); exit(0);
                //}
                //else{
            $sname = strtolower($uname);
            $sname[0] = strtoupper($sname[0]);
            $sname = str_replace(array('*', "'", ',', ' ', '"', '(', ')', '<', '>', '=', ';', '-', '#', '/', '$', '%', '\\', '`'), '', $sname);
            $uname = str_replace(array('*', "'", ',', ' ', '"', '(', ')', '<', '>', '=', ';', '-', '#', '/', '$', '%', '\\', '`'), '', $uname);

            if(filter_var($sname, FILTER_VALIDATE_EMAIL)) {
                $email_split = explode('@', $sname);
                $sname = $email_split[0];
            }

            mysqli_query($link,"insert into students values ('$uname', '$sname', NOW() )");
                    if(mysqli_affected_rows($link) > 0) {
                        $_SESSION['student'] = $uname;
                        $_SESSION['sname'] = $sname;
                        //header("location: student_activity.php"); exit(0);
                    } else{
                        $res2 = mysqli_query($link, "select * from students where sid = '$uname'");
                         if(mysqli_num_rows($res2) <= 0) {
                             $error = 'Database error!';
                         } else {
                             $_SESSION['student'] = $uname;
                             $_SESSION['sname'] = $sname;
                         }
                    }
               // }

            //} else{
            //    $error = 'UserId not in course';
            //}
        }

        //comment this part------- this for testing purposes
        /*else{
        $uname = mysqli_real_escape_string($link, stripslashes(trim(strip_tags($_POST['usr']))));

        $count_login = mysqli_num_rows(mysqli_query($link, "select sid from students where sid = '$uname' limit 1 "));
        if($count_login > 0){
        //$_SESSION['student'] = $token;
        $_SESSION['student'] = $uname;
        header("location: student.php");
        exit(0);
        }
        else{
        $error = 'UserId incorrect';
        }

        }*/

    }

}

if(!isset($_SESSION['student'])) {
    $vars = array();
    if(!empty($error))
        $vars['error'] = $error;

    $vars['hidden_input_array'] = [];
    $hidden_input_array['page'] = "student_login";
    $vars['hidden_input_array'] = array_merge($vars['hidden_input_array'], $hidden_input_array);
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
            $gcal_result_1 = mysqli_query($link, "select * from pyramid_groups where pg_level = 0 and pg_started = 1 and pg_fid='{$fid}'");
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

    if($first_time_user) {
        //show activity explanation
        $activity_explanation_view = View\element("activity_explanation", $vars);

        \View\page(array(
            'title' => 'Activity explanation',
            'body' => $activity_explanation_view,
        ));
    } else {
        header("location: student.php");
    }
    exit;
}