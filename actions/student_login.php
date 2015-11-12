<?php
$error;
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
                    $sname = str_replace(array('*', "'", ',', ' ', '"', '(', ')', '<', '>', '=', ';', '-', '#', '/', '@', '$', '%', '\\', '`'), '', $sname);
                    $uname = str_replace(array('*', "'", ',', ' ', '"', '(', ')', '<', '>', '=', ';', '-', '#', '/', '@', '$', '%', '\\', '`'), '', $uname);
                    mysqli_query($link,"insert into students values ('$uname', '$sname', NOW() )");
                    if(mysqli_affected_rows($link) > 0) {
                        $_SESSION['student'] = $uname;
                        //header("location: student_activity.php"); exit(0);
                    } else{
                        $res2 = mysqli_query($link, "select * from students where sid = '$uname'");
                         if(mysqli_num_rows($res2) <= 0) {
                             $error = 'Database error!';
                         } else {
                             $_SESSION['student'] = $uname;
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

    $login_form = View\element("login", $vars);

    \View\page(array(
        'title' => 'Student Login',
        'body' => $login_form,
    ));
} else {
    //show activity explanation
    $activity_explanation_view = View\element("activity_explanation", $vars);

    \View\page(array(
        'title' => 'Activity explanation',
        'body' => $activity_explanation_view,
    ));

    //header("location: student.php");
    exit;
}