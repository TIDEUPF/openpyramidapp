<?php
session_start();
include('dbvar.php');

//include('inc_pyramid_func.php');

if(isset($_SESSION['user'])) {
    $teacher_id = $_SESSION['user'];

    $student_count = mysqli_num_rows(mysqli_query($link, "select * from students"));

    if(isset($_POST['cflow'])) {
        $fname = mysqli_real_escape_string($link, stripslashes(trim(strip_tags($_POST['fname']))));
        $fdes =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['fdes']))));
        $fcname =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['fcname']))));
        $qs =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['qs']))));
        $tst = 60 * (int)mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['tst']))));
        $rt = 60 * (int)mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['rt']))));
        $htst = $tst + 120;
        $hrt = $rt + 120;
        $expe = mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['expe']))));
        $sync = (int)mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['sync']))));
        $multi_py = (int)mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['multi_py']))));
        $ch = (int)mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['ch']))));
        $n_selected_answers = (int)mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['n_selected_answers']))));
        $random_selection = (int)mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['random_selection']))));
        $fesname = '';//$fesname =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['fesname']))));
        $fl = (int) $_POST['fl'];
        $fsg = (int) $_POST['fsg'];

        //80 per cent of expected students
        if($multi_py) {
            $multi_pyramid_max_size = 20;
            $multi_pyramid_min_size = 4;
            $pyramid_size = max(min($multi_pyramid_max_size, floor($expe / 2)), $multi_pyramid_min_size);
            if($pyramid_size > 4)
                $min_pyramid = floor($pyramid_size * 0.8);
            else
                $min_pyramid = $pyramid_size;
        } else {
            $pyramid_size = $expe;

            //assume everyone participating for experiments <=8
            if($expe > 8)
                $min_pyramid = floor($expe * 0.8);
            else
                $min_pyramid = $expe;
        }

        //number of levels
        $max_levels = floor(log(floor($min_pyramid/$fsg), 2)) + 1;
        if($fl > $max_levels)
            $fl = $max_levels;

        $rps = 1;//TODO: legacy var

        if($fl < 1 || $rps < 1 || $fsg < 1)	{
            $error = 'Levels and Responses cannot be 0';
        } else {
            $datestamp = time();
            mysqli_query($link,"insert into flow values (null, '$teacher_id', '$fname', '$fdes', '$fcname', '$fesname', '$fsg', '$fl', '$pyramid_size', '$min_pyramid', '$expe', '$rps', '$datestamp', $tst, $rt, $htst, $hrt, '{$qs}', '{$ch}', '{$sync}', '{$multi_py}', '{$n_selected_answers}', '{$random_selection}')");
        }
    }
} else {
    header("location: login.php");
    exit(0);
}

global $default_teacher_question;
$tq = $default_teacher_question;
//TODO: restore form http://stackoverflow.com/questions/19109884/serializing-and-deserializing-a-form-with-its-current-state
?><!DOCTYPE html>
<html lang="en">
<head>
    <title>CreateActivityPage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
    <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <link rel="stylesheet" type="text/css" href="resources/css/teacher/styles.css">
</head>

<body>
<div data-role="page">
    <div data-role="header">
        <h1>Create Pyramid Activity</h1>
    </div>
    <div data-role="main" class="ui-content">
        <form data-ajax="false">
            <div class="ui-field-contain">
                <label for="activity">Activity name:</label>
                <input type="text" name="textinput-s" id="activity" placeholder="Activity name" value="" data-clear-btn="true">
            </div>

            <div class="ui-field-contain">
                <label for="description">Task Description:<a href="#popupInfo1" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                    <div data-role="popup" id="popupInfo1" class="ui-content" data-theme="a" style="max-width:350px;">
                        <p>This is the task description that will appear for students when they access the pyramid activity.</p>
                    </div></label>
                <textarea name="textarea-1" id="description"></textarea>
            </div>

            <div class="ui-field-contain">
                <label for="class_size">Total number of students:<a href="#popupInfo2" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                    <div data-role="popup" id="popupInfo2" class="ui-content" data-theme="a" style="max-width:350px;">
                        <p>This is the total number of expected students in the class available for the activity. This could be an estimated value (specially during a massive open online course case).</p>
                    </div></label>
                <input type="number" name="textinput-s" id="class_size" placeholder="Total class size" value="30" data-clear-btn="true">
            </div>
            <div class="ui-field-contain">
                <fieldset data-role="controlgroup">
                    <legend>Learning setting:<a href="#popupInfo7" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                        <div data-role="popup" id="popupInfo7" class="ui-content" data-theme="a" style="max-width:700px;">
                            <p>This field specifies whether the classroom setting is a face-to-face or virtual learning context.</p>
                        </div></legend>
                    <input type="checkbox" name="checkbox-v-2a" id="checkbox-v-2a">
                    <label for="checkbox-v-2a">Classroom</label>
                    <input type="checkbox" name="checkbox-v-2b" id="checkbox-v-2b">
                    <label for="checkbox-v-2b">Distance</label>
                </fieldset>
            </div>

            <div class="ui-input-btn ui-btn ui-btn-inline ui-corner-all">Save<input type="button" data-enhanced="true" value="Save"></div>

        </form>


    </div>
</div>
</div>
</div>
</body>
</html>