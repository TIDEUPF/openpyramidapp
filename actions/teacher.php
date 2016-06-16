<?php
session_start();
include('dbvar.php');

//include('inc_pyramid_func.php');

if(isset($_SESSION['user'])) {
    $teacher_id = $_SESSION['user'];

    
/*
    $student_count = mysqli_num_rows(mysqli_query($link, "select * from students"));

    if(isset($_POST['cflow'])) {
		$fname = mysqli_real_escape_string($link, stripslashes(trim(strip_tags($_POST['activity']))));
		$fdes =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['description']))));
		//$fcname =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['fcname']))));
		$fcname =  '';
		$qs =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['task_description']))));
		$tst = (int)mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['s_question']))));
		$rt = (int)mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['s_rating']))));
		$htst = (int)mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['h_question']))));
		$hrt = (int)mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['h_rating']))));
		//$htst = $tst + 120;
		//$hrt = $rt + 120;
		$expe = mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['expected_students']))));
		$sync = (int)mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['sync']))));
		$multi_py = (int)mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['multiple_pyramids']))));
		$ch = (int)mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['discussion']))));
		$n_selected_answers = (int)mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['n_selected_answers']))));
		$random_selection = (int)mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['random_selection']))));
		$fesname = '';//$fesname =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['fesname']))));
		$fl = (int) $_POST['n_levels'];
		$fsg = (int) $_POST['first_group_size'];
        $str_id = \Util\rand_str(5);

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
*/
} else {
    header("location: login.php");
    exit(0);
}

global $default_teacher_question;
$tq = $default_teacher_question;

//TODO: restore form http://stackoverflow.com/questions/19109884/serializing-and-deserializing-a-form-with-its-current-state

    /*
    $default_data = [
        "checkbox-v-2b" => false,
        "checkbox-v-2a" => false,
        "textinput-s" => 30,
        "textarea-1" => "",
        "textinput-s" => "",
    ];
*/

    //$data = $default_data;

/*
    //sanitize data
    foreach($data as $key => &$element) {
        if(isset($_REQUEST[$key])) {
            if(is_string($element))
                $element = trim($element);

            if(is_bool($element))
                $element = true;
        } else {
            if(is_bool($element) and $element)
                $element = false;
        }

        if(is_string($element))
            $element = htmlentities($element);

        if(is_bool($element))
            $element = $element ? " checked " : "";
    }
*/
if(!isset($_REQUEST['save']))	{
    //default values
} else {
    $datestamp = time();
    $data_sql = mysqli_real_escape_string($link, json_encode($data));
    mysqli_query($link,"insert into activity values (null, '$teacher_id', '$data_sql')");
    $activity_id = mysqli_insert_id($link);

    header("location: activity.php?activity=" . $activity_id);
}

    $defaults = [
        'async' => [
            'sync' => 0,
            's_question' => 180,
            'h_question' => 240,
            's_rating' => 120,
            'h_rating' => 180,
            'satisfaction' => 60,
            //'expected_students' => 20,
            'discussion' => 1,
            'n_selected_answers' => 1,
            'random_selection' => 1,
            'n_levels' => 3,
            'first_group_size' => 4,
            'multiple_pyramids' => 1,
        ],
        'sync' => [
            'sync' => 0,
            's_question' => 3600,
            'h_question' => 240,
            's_rating' => 120,
            'h_rating' => 180,
            'satisfaction' => 60,
            //'expected_students' => 20,
            'discussion' => 1,
            'n_selected_answers' => 1,
            'random_selection' => 1,
            'n_levels' => 3,
            'first_group_size' => 2,
            'multiple_pyramids' => 0,
        ],
    ];
    header('Content-Type: text/html; charset=utf-8');
?><!DOCTYPE html>
<html lang="en">
<head>
    <title>CreateActivityPage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
    <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <link rel="stylesheet" type="text/css" href="elements/resources/css/teacher/styles.css">
</head>

<body>
<div data-role="page">
    <div data-role="header">
        <h1>Create Pyramid Activity</h1>
    </div>
    <div data-role="main" class="ui-content">
        <div id="center-frame">
            <form data-ajax="false" method="post">
                <div id="page-1" class="page">
                    <div class="ui-field-contain">
                        <label for="activity">Activity name:</label>
                        <input type="text" name="activity" id="activity" placeholder="Activity name" value="" data-clear-btn="true">
                    </div>

                    <div class="ui-field-contain">
                        <label for="task_description">Task Description:<a href="#popupInfo1" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                            <div data-role="popup" id="popupInfo1" class="ui-content" data-theme="a" style="max-width:350px;">
                                <p>This is the task description that will appear for students when they access the pyramid activity.</p>
                            </div></label>
                        <textarea name="task_description" id="task_description"></textarea>
                    </div>

                    <div class="ui-field-contain">
                        <label for="expected_students">Total number of students:<a href="#popupInfo2" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                            <div data-role="popup" id="popupInfo2" class="ui-content" data-theme="a" style="max-width:350px;">
                                <p>This is the total number of expected students in the class available for the activity. This could be an estimated value (specially during a massive open online course case).</p>
                            </div></label>
                        <input type="number" name="expected_students" id="expected_students" placeholder="Total class size" data-clear-btn="true">
                    </div>
                    <div class="ui-field-contain">
                        <fieldset id="learning_setting_fieldset" data-role="controlgroup">
                            <legend>Learning setting:<a href="#popupInfo7" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                                <div data-role="popup" id="popupInfo7" class="ui-content" data-theme="a" style="max-width:700px;">
                                    <p>This field specifies whether the classroom setting is a face-to-face or virtual learning context.</p>
                                </div></legend>
                            <input type="radio" name="learning_setting" id="learning_setting-a" value="classroom" checked>
                            <label for="learning_setting-a">Classroom</label>
                            <input type="radio" name="learning_setting" id="learning_setting-b" value="distance">
                            <label for="learning_setting-b">Distance</label>
                        </fieldset>
                    </div>

                    <a goto="2" class="create-flow-next ui-btn ui-corner-all ui-shadow ui-btn-icon-right ui-icon-arrow-r">Next</a>
                </div>

                <?php /*second screen*/?>
                <div id="page-2" class="page" style="display: none;">
                    <div id="pyramid-levels-2" class="pyramid-animation" style="position: relative;float: left;margin-left: 15px;">
                        <svg viewBox="70 5 280 210">
                            <polygon points="210,5 350,210 70,210" style="fill:pink;stroke:purple;stroke-width:2" />
                            <circle cx="120" cy="175" r="12" stroke="green" stroke-width="2" fill="yellow" />
                            <circle cx="180" cy="175" r="12" stroke="green" stroke-width="2" fill="yellow" />
                            <circle cx="240" cy="175" r="12" stroke="green" stroke-width="2" fill="yellow" />
                            <circle cx="300" cy="175" r="12" stroke="green" stroke-width="2" fill="yellow" />
                            <line x1="112" y1="150" x2="308" y2="150" style="stroke:rgb(255,0,155);stroke-width:4" />

                            <text x="120" y="205" fill="red">Level 1 – Individual level</text>
                            <text x="55" y="140" fill="red" transform="rotate(-52,50,60)">Rating level(s)</text>

                            <!-- first level first 2 groups animation -->
                            <circle cx="120" cy="175" stroke="green" stroke-width="2" fill="yellow">
                                <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="120" to="150" />
                                <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="175" to="160" />
                                <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                            </circle>

                            <circle cx="180" cy="175" stroke="green" stroke-width="2" fill="yellow">
                                <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="180" to="150" />
                                <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="175" to="160" />
                                <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                            </circle>

                            <!--circle cx="170" cy="120" r="17" stroke="green" stroke-width="2" fill="yellow"-->

                            <!-- first level next 2 groups animation -->
                            <circle cx="240" cy="180" r="12" stroke="green" stroke-width="2" fill="yellow">
                                <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="240" to="270" />
                                <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="180" to="160" />
                                <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                            </circle>

                            <circle cx="300" cy="180" r="16" stroke="green" stroke-width="2" fill="yellow">
                                <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="300" to="270" />
                                <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="180" to="160" />
                                <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                            </circle>

                            <g class="level1">
                                <circle id="click-circle2"  r="17" stroke="black" stroke-width="2" visibility="hidden">
                                    <set attributeName="visibility" attributeType="CSS" to="visible" begin="2s" dur="2s" fill="freeze" />
                                    <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="270" to="230" />
                                    <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="160" to="120" />
                                    <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                    <animate attributeName="fill" attributeType="CSS" from="yellow" to="green" begin="2s" dur="4s" fill="freeze" />
                                </circle>

                                <circle id="click-circle1" r="17" stroke="black" stroke-width="2" fill="purple" visibility="hidden">
                                    <set attributeName="visibility" attributeType="CSS" to="visible" begin="2s" dur="2s" fill="freeze" />
                                    <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="150" to="180" />
                                    <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="160" to="120" />
                                    <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                    <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                </circle>
                            </g>


                            <line x1="149" y1="95" x2="271" y2="95" style="stroke:rgb(255,0,155);stroke-width:4" />

                            <circle stroke="blue" stroke-width="2" visibility="hidden">
                                <set attributeName="visibility" attributeType="CSS" to="visible" begin="4.5s" dur="2s" fill="freeze" />
                                <animate attributeName="cx" attributeType="XML" begin="4.5s" dur="3s" fill="freeze" from="180" to="210" />
                                <animate attributeName="cy" attributeType="XML" begin="4.5s" dur="3s" fill="freeze" from="120" to="62" />
                                <animate attributeName="r" attributeType="XML" begin="4.5s" dur="3s" fill="freeze" from="17" to="23" />
                                <animate attributeName="fill" attributeType="CSS" from="purple" to="red" begin="4.5s" dur="2s" fill="freeze" />
                            </circle>

                            <circle stroke="blue" stroke-width="2" visibility="hidden">
                                <set attributeName="visibility" attributeType="CSS" to="visible" begin="4.5s" dur="2s" fill="freeze" />
                                <animate attributeName="cx" attributeType="XML" begin="4.5s" dur="3s" fill="freeze" from="230" to="210" />
                                <animate attributeName="cy" attributeType="XML" begin="4.5s" dur="3s" fill="freeze" from="120" to="62" />
                                <animate attributeName="r" attributeType="XML" begin="4.5s" dur="3s" fill="freeze" from="17" to="23" />
                                <animate attributeName="fill" attributeType="CSS" from="green" to="red" begin="4.5s" dur="2s" fill="freeze" />
                            </circle>
                        </svg>
                    </div>

                    <div id="pyramid-levels-3" class="pyramid-animation" style="position: relative;float: left;margin-left: 15px;">
                        <svg viewBox="40 5 340 257">
                            <polygon points="210,5 380,261 40,261" style="fill:pink;stroke:purple;stroke-width:2" />
                            <line x1="112" y1="150" x2="308" y2="150" style="stroke:rgb(255,0,155);stroke-width:4" />
                            <line x1="78" y1="205" x2="343" y2="205" style="stroke:rgb(255,0,155);stroke-width:4" />

                            <g  transform="translate(0,225)">
                                <circle cx="90" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="120" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="150" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="180" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="210" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="240" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="270" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="300" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="330" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <text x="120" y="30" fill="red">Level 1 – Individual level</text>

                                <!-- first level first 2 groups animation -->
                                <circle cx="120" stroke="green" stroke-width="2" fill="yellow">
                                    <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="120" to="150" />
                                    <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="0" to="-15" />
                                    <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                    <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                    <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                                </circle>

                                <circle cx="180" stroke="green" stroke-width="2" fill="yellow">
                                    <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="180" to="150" />
                                    <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="0" to="-15" />
                                    <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                    <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                    <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                                </circle>

                                <!-- first level next 2 groups animation -->
                                <circle cx="240" r="12" stroke="green" stroke-width="2" fill="yellow">
                                    <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="240" to="270" />
                                    <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="0" to="-15" />
                                    <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                    <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                    <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                                </circle>

                                <circle cx="300" r="16" stroke="green" stroke-width="2" fill="yellow">
                                    <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="300" to="270" />
                                    <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="0" to="-15" />
                                    <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                    <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                    <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                                </circle>

                                <g class="level1">
                                    <circle id="click-circle1" r="17" stroke="black" stroke-width="2" fill="purple" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="2s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="150" to="180" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-45" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle2" r="17" stroke="black" stroke-width="2" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="2s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="270" to="230" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-45" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" from="yellow" to="green" begin="2s" dur="4s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle3" r="17" stroke="black" stroke-width="2" fill="green" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="4s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="150" to="130" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-45" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle4" r="17" stroke="black" stroke-width="2" fill="purple" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="4s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="150" to="280" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-45" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                </g>

                                <g>
                                    <circle id="click-circle5" r="17" stroke="black" stroke-width="2" fill="purple" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="4s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="150" to="180" />
                                        <animate attributeName="cy" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="-55" to="-105" />
                                        <animate attributeName="r" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle6" r="17" stroke="black" stroke-width="2" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="4s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="270" to="230" />
                                        <animate attributeName="cy" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="-55" to="-105" />
                                        <animate attributeName="r" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" from="yellow" to="green" begin="2s" dur="4s" fill="freeze" />
                                    </circle>
                                </g>
                            </g>

                            <text x="55" y="140" fill="red" transform="rotate(-52,50,60)">Rating level(s)</text>

                            <!--circle cx="170" cy="120" r="17" stroke="green" stroke-width="2" fill="yellow"-->


                            <line x1="149" y1="95" x2="271" y2="95" style="stroke:rgb(255,0,155);stroke-width:4" />

                            <circle stroke="blue" stroke-width="2" visibility="hidden">
                                <set attributeName="visibility" attributeType="CSS" to="visible" begin="4.5s" dur="2s" fill="freeze" />
                                <animate attributeName="cx" attributeType="XML" begin="6s" dur="3s" fill="freeze" from="180" to="210" />
                                <animate attributeName="cy" attributeType="XML" begin="6s" dur="3s" fill="freeze" from="120" to="62" />
                                <animate attributeName="r" attributeType="XML" begin="6s" dur="3s" fill="freeze" from="17" to="23" />
                                <animate attributeName="fill" attributeType="CSS" from="purple" to="red" begin="6s" dur="2s" fill="freeze" />
                            </circle>

                            <circle stroke="blue" stroke-width="2" visibility="hidden">
                                <set attributeName="visibility" attributeType="CSS" to="visible" begin="4.5s" dur="2s" fill="freeze" />
                                <animate attributeName="cx" attributeType="XML" begin="6s" dur="3s" fill="freeze" from="230" to="210" />
                                <animate attributeName="cy" attributeType="XML" begin="6s" dur="3s" fill="freeze" from="120" to="62" />
                                <animate attributeName="r" attributeType="XML" begin="6s" dur="3s" fill="freeze" from="17" to="23" />
                                <animate attributeName="fill" attributeType="CSS" from="green" to="red" begin="4.5s" dur="2s" fill="freeze" />
                            </circle>
                        </svg>
                    </div>

                    <div id="pyramid-levels-4" class="pyramid-animation" style="position: relative;float: left;margin-left: 15px;">
                        <svg viewBox="0 5 420 307">
                            <polygon points="210,5 420,311 0,311" style="fill:pink;stroke:purple;stroke-width:2" />
                            <line x1="112" y1="150" x2="308" y2="150" style="stroke:rgb(255,0,155);stroke-width:4" />
                            <line x1="72" y1="205" x2="345" y2="205" style="stroke:rgb(255,0,155);stroke-width:4" />
                            <line x1="40" y1="255" x2="380" y2="255" style="stroke:rgb(255,0,155);stroke-width:4" />

                            <g  transform="translate(0,275)">
                                <circle cx="50" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="90" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="125" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="165" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="207" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="247" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="285" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="325" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="360" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <text x="120" y="30" fill="red">Level 1 – Individual level</text>

                                <!-- first level first 2 groups animation -->
                                <circle cx="120" stroke="green" stroke-width="2" fill="yellow">
                                    <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="120" to="150" />
                                    <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="0" to="-15" />
                                    <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                    <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                    <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                                </circle>

                                <circle cx="180" stroke="green" stroke-width="2" fill="yellow">
                                    <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="180" to="150" />
                                    <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="0" to="-15" />
                                    <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                    <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                    <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                                </circle>

                                <!-- first level next 2 groups animation -->
                                <circle cx="240" r="12" stroke="green" stroke-width="2" fill="yellow">
                                    <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="240" to="270" />
                                    <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="0" to="-15" />
                                    <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                    <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                    <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                                </circle>

                                <circle cx="300" r="16" stroke="green" stroke-width="2" fill="yellow">
                                    <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="300" to="270" />
                                    <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="0" to="-15" />
                                    <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                    <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                    <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                                </circle>

                                <g class="level1">
                                    <circle id="click-circle1" r="17" stroke="black" stroke-width="2" fill="purple" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="2s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="150" to="180" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-45" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle2" r="17" stroke="black" stroke-width="2" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="2s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="270" to="230" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-45" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" from="yellow" to="green" begin="2s" dur="4s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle3" r="17" stroke="black" stroke-width="2" fill="green" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="4s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="150" to="130" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-45" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle4" r="17" stroke="black" stroke-width="2" fill="purple" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="4s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="150" to="280" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-45" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle13" r="17" stroke="black" stroke-width="2" fill="purple" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="4s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="150" to="85" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-45" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle14" r="17" stroke="black" stroke-width="2" fill="green" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="4s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="150" to="330" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-45" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                </g>

                                <g>
                                    <circle id="click-circle5" r="17" stroke="black" stroke-width="2" fill="purple" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="4s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="150" to="180" />
                                        <animate attributeName="cy" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="-55" to="-100" />
                                        <animate attributeName="r" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle6" r="17" stroke="black" stroke-width="2" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="4s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="270" to="230" />
                                        <animate attributeName="cy" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="-55" to="-100" />
                                        <animate attributeName="r" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" from="yellow" to="green" begin="2s" dur="4s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle23" r="17" stroke="black" stroke-width="2" fill="purple" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="6s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="230" to="280" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-100" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle24" r="17" stroke="black" stroke-width="2" fill="green" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="6s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="230" to="130" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-100" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                </g>

                                <g>
                                    <circle id="click-circle7" r="17" stroke="black" stroke-width="2" fill="purple" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="6s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="6s" dur="2s" fill="freeze" from="150" to="180" />
                                        <animate attributeName="cy" attributeType="XML" begin="6s" dur="2s" fill="freeze" from="-100" to="-150" />
                                        <animate attributeName="r" attributeType="XML" begin="6s" dur="2s" fill="freeze" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle8" r="17" stroke="black" stroke-width="2" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="6s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="6s" dur="2s" fill="freeze" from="270" to="230" />
                                        <animate attributeName="cy" attributeType="XML" begin="6s" dur="2s" fill="freeze" from="-100" to="-150" />
                                        <animate attributeName="r" attributeType="XML" begin="6s" dur="2s" fill="freeze" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" from="yellow" to="green" begin="2s" dur="4s" fill="freeze" />
                                    </circle>
                                </g>
                            </g>

                            <text x="55" y="140" fill="red" transform="rotate(-52,50,60)">Rating level(s)</text>

                            <!--circle cx="170" cy="120" r="17" stroke="green" stroke-width="2" fill="yellow"-->


                            <line x1="149" y1="95" x2="271" y2="95" style="stroke:rgb(255,0,155);stroke-width:4" />

                            <circle stroke="blue" stroke-width="2" visibility="hidden">
                                <set attributeName="visibility" attributeType="CSS" to="visible" begin="4.5s" dur="2s" fill="freeze" />
                                <animate attributeName="cx" attributeType="XML" begin="8s" dur="3s" fill="freeze" from="180" to="210" />
                                <animate attributeName="cy" attributeType="XML" begin="8s" dur="3s" fill="freeze" from="120" to="62" />
                                <animate attributeName="r" attributeType="XML" begin="8s" dur="3s" fill="freeze" from="17" to="23" />
                                <animate attributeName="fill" attributeType="CSS" from="purple" to="red" begin="6s" dur="2s" fill="freeze" />
                            </circle>

                            <circle stroke="blue" stroke-width="2" visibility="hidden">
                                <set attributeName="visibility" attributeType="CSS" to="visible" begin="4.5s" dur="2s" fill="freeze" />
                                <animate attributeName="cx" attributeType="XML" begin="8s" dur="3s" fill="freeze" from="230" to="210" />
                                <animate attributeName="cy" attributeType="XML" begin="8s" dur="3s" fill="freeze" from="120" to="62" />
                                <animate attributeName="r" attributeType="XML" begin="8s" dur="3s" fill="freeze" from="17" to="23" />
                                <animate attributeName="fill" attributeType="CSS" from="green" to="red" begin="4.5s" dur="2s" fill="freeze" />
                            </circle>
                        </svg>
                    </div>

                    <div id="popup" style="position: relative;float: left;width:500px;">
                        <h4>Pyramid Configurations</h4>

                            <div class="ui-field-contain ui-mini">
                                <label for="first_group_size">No. of students per group at rating level 1:<a href="#popupInfo" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                                    <div data-role="popup" id="popupInfo" class="ui-content" data-theme="a" style="max-width:700px;">
                                        <p>This specifies the initial group size at level 2 (first rating level) after option submission level. This size will be doubled when groups propagate to upper levels.</p>
                                    </div>
                                </label>
                                <input type="range" name="first_group_size" id="first_group_size" value="3" min="2" max="10" data-highlight="true">
                            </div>

                            <div class="ui-field-contain">
                                <label for="n_levels">No. of levels:<a href="#popupInfo5" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                                    <div data-role="popup" id="popupInfo5" class="ui-content" data-theme="a" style="max-width:700px;">
                                        <p>This includes both option submission level and rating levels. It is recommended to have 3 to 4 levels for active participation.</p>
                                    </div></label>
                                <input type="range" name="n_levels" id="n_levels" value="3" min="2" max="4" data-highlight="true">
                            </div>

                            <div class="ui-field-contain">
                                <label for="multiple_pyramids">Allow multiple pyramids:<a href="#popupInfo4" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                                    <div data-role="popup" id="popupInfo4" class="ui-content" data-theme="a" style="max-width:700px;">
                                        <p>If your class is relatively large class, it would be better to enable this feature, so several pyramids will be created and students will be automatically allocated.</p>
                                    </div></label>
                                <select name="multiple_pyramids" id="multiple_pyramids" data-role="slider">
                                    <option value="0">No</option>
                                    <option value="1" selected="selected">Yes</option>
                                </select>
                            </div>

                            <div class="ui-field-contain">
                                <label for="minInfo">Minimum students per pyramid:<a href="#popupInfo3" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                                    <div data-role="popup" id="popupInfo3" class="ui-content" data-theme="a" style="max-width:700px;">
                                        <p>Number of students allowed to be grouped into a single pyramid. Based on the total number of students and this value, several pyramids may require and it will be automatically suggested by the system.</p>
                                    </div></label>
                                <input type="number" name="minInfo" id="minInfo" value="12" data-clear-btn="true">
                            </div>

                            <div class="ui-field-contain">
                                <label for="discussion">Discussion :<a href="#popupInfo6" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                                    <div data-role="popup" id="popupInfo6" class="ui-content" data-theme="a" style="max-width:700px;">
                                        <p>If discussion is enabled, students can discussion with peers to clarify and negotiate their options during rating phases.</p>
                                    </div></label>
                                <select name="discussion" id="discussion" data-role="slider">
                                    <option value="0">No</option>
                                    <option value="1" selected="selected">Yes</option>
                                </select>
                            </div>

                        <!--<div class="ui-field-contain">-->
                        <a href="#popupAdvanced" data-rel="popup" data-position-to="window" class="ui-btn ui-corner-all ui-shadow ui-btn-inline" data-transition="pop">Advanced Settings</a>
                        <div data-role="popup" id="popupAdvanced" data-theme="a" class="ui-corner-all">

                            <div style="padding:10px 20px;">
                                <h4>Advanced Pyramid Configurations</h4>
                                <h5>It is optional to change these default values.</h5>

                                <div id="pop-background"></div>

                                <label for="s_question">Option submission timer:<a text-data="#cpopup1" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext"
                                                                                title="This timer specifies the time permitted for initial option (artifact) submission for students">More</a>
                                    <div id="cpopup1-text" class="ui-content tooltip-popup" data-theme="a" style="display:none">
                                        <p>If discussion is enabled, students can discussion with peers to clarify and negotiate their options during rating phases.</p>
                                    </div>
                                </label>
                                <div style="position:relative;float:left;">
                                    <input type="number" name="s_question" id="s_question" value="" data-clear-btn="true" data-wrapper-class="numk" />
                                </div>

                                <div style="position:relative;float:left; margin-left:10px; margin-top:2px;">
                                    <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
                                        <input type="radio" name="s_question_unit" id="s_question_unit-a" value="m" checked="checked">
                                        <label for="s_question_unit-a">Minutes</label>
                                        <input type="radio" name="s_question_unit" id="s_question_unit-b" value="h">
                                        <label for="s_question_unit-b">Hours</label>
                                        <input type="radio" name="s_question_unit" id="s_question_unit-c" value="d">
                                        <label for="s_question_unit-c">Days</label>
                                    </fieldset></div>
                                <div style="clear:both;"></div>

                                <label for="h_question">Option submission hard timer:<a href="#popup2" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext"
                                                                                       title="This timer specifies the maximum time permitted for initial option (artifact) submission for students. Once expired, every student will be promoted next level."></a>
                                </label>
                                <div style="position:relative;float:left;">
                                    <input type="number" name="h_question" id="h_question" data-wrapper-class="numk" value="" data-clear-btn="true" />
                                </div>

                                <div style="position:relative;float:left; margin-left:10px; margin-top:2px;">
                                    <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
                                        <input type="radio" name="h_question_unit" id="h_question_unit-a" value="m" checked="checked">
                                        <label for="h_question_unit-a">Minutes</label>
                                        <input type="radio" name="h_question_unit" id="h_question_unit-b" value="h">
                                        <label for="h_question_unit-b">Hours</label>
                                        <input type="radio" name="h_question_unit" id="h_question_unit-c" value="d">
                                        <label for="h_question_unit-c">Days</label>
                                    </fieldset></div>
                                <div style="clear:both;"></div>

                                <label for="s_rating">Rating timer:<a href="#popup3" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext"
                                                                     title="This timer specifies the time permitted for rating at each level including discussion time."></a>
                                </label>
                                <div style="position:relative;float:left;">
                                    <input type="number" name="s_rating" id="s_rating" data-wrapper-class="numk" value="" data-clear-btn="true">
                                </div>

                                <div style="position:relative;float:left; margin-left:10px; margin-top:2px;">
                                    <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
                                        <input type="radio" name="s_rating_unit" id="s_rating_unit-a" value="m" checked="checked">
                                        <label for="s_rating_unit-a">Minutes</label>
                                        <input type="radio" name="s_rating_unit" id="s_rating_unit-b" value="h">
                                        <label for="s_rating_unit-b">Hours</label>
                                        <input type="radio" name="s_rating_unit" id="s_rating_unit-c" value="d">
                                        <label for="s_rating_unit-c">Days</label>
                                    </fieldset></div>
                                <div style="clear:both;"></div>

                                <label for="h_rating">Rating hard timer:<a href="#popup4" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext"
                                                                              title="This is the maximum time allowed for rating and discussion at each level. Once expired everyone is promoted to next level."></a>
                                </label>
                                <div style="position:relative;float:left;">
                                    <input type="number" name="h_rating" id="h_rating" data-wrapper-class="numk" value="" data-clear-btn="true">
                                </div>

                                <div style="position:relative;float:left; margin-left:10px; margin-top:2px;">
                                    <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
                                        <input type="radio" name="h_rating_unit" id="h_rating_unit-a" value="m" checked="checked">
                                        <label for="h_rating_unit-a">Minutes</label>
                                        <input type="radio" name="h_rating_unit" id="h_rating_unit-b" value="h">
                                        <label for="h_rating_unit-b">Hours</label>
                                        <input type="radio" name="h_rating_unit" id="h_rating_unit-c" value="d">
                                        <label for="h_rating_unit-c">Days</label>
                                    </fieldset></div>
                                <div style="clear:both;"></div>

                                <label for="satisfaction">Satisfaction percentage:<a href="#popup5" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext"
                                                                                  title="When this percentage is reached, students will be promoted for the next level. This is important when longer timer values are defined at MOOC scenarios with less participation."></a>
                                </label>
                                <input type="range" name="satisfaction" id="satisfaction" value="60" min="30" max="100" data-highlight="true">

                                <a data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check">Submit</a>
                            </div>
                        </div><!--popup-->
                        <br>
                        <div class="ui-input-btn ui-btn ui-btn-inline ui-shadow ui-corner-all ui-icon-check ui-btn-icon-left ui-btn-a">
                            Create<input name="create" type="submit" data-enhanced="true" value="Create">
                        </div>

                        <div id="popup-clearance"></div>

                        <!--fields not enetred by the user -->
                        <input type="hidden" name="sync">
                        <input type="hidden" name="random_selection">
                        <input type="hidden" name="n_selected_answers">

                        <input type="hidden" name="s_question_seconds">
                        <input type="hidden" name="h_question_seconds">
                        <input type="hidden" name="s_rating_seconds">
                        <input type="hidden" name="h_rating_seconds">


                    </div>

                    <div style="clear: both;"></div>



                </div>
            </form>
        </div>
    </div>
</div>


<script>

    //timers in seconds
    var timers = [
        's_question',
        'h_question',
        's_rating',
        'h_rating'
    ];

    var units_in_seconds = {
        'd': 86400,
        'h': 3600,
        'm': 60
    };

    var sync_table = {
        'classroom': 'sync',
        'distance': 'async'
    };

    var defaults = <?=json_encode($defaults)?>;
    var sync = 'sync';

    function timer_to_seconds(field, value) {
        var converted_value = {};
        for(var unit in units_in_seconds) {
            if(value < units_in_seconds[unit])
                continue;

            converted_value.value = value / units_in_seconds[unit];
            converted_value.unit = unit;
            break;
        }

        return converted_value;
    }

    function restore_timers() {
        for(var timer in timers) {
            var value = defaults[sync][timers[timer]];
            var converted_value = timer_to_seconds(timers[timer], value);

            set_field(timers[timer], converted_value.value);
            set_field(timers[timer] + '_unit', converted_value.unit);
        }
    }

    function flow_apply_defaults() {
        var fields = [
            'satisfaction',
            'n_selected_answers',
            'n_levels',
            'first_group_size',
            'discussion',
            'random_selection',
            'multiple_pyramids'
        ];

        for(var field in fields) {
            set_field(fields[field], defaults[sync][fields[field]]);
        }

        restore_timers();

        //update sliders
        $('[data-role="slider"], [data-type="range"]').slider('refresh');

        //update control groups
        $('[data-role="controlgroup"]').controlgroup('refresh')
    }

    //calculate the remaining variables before submitting
    $('form').submit(function() {
        $flow = $(this);

        for(var timer in timers) {
            var unit = $('[name="' + timers[timer] + '_unit"]').val();
            var timer_value = parseInt($('[name="' + timers[timer] + '"]').val(), 10);
            var timer_value_seconds = units_in_seconds[unit] * timer_value;
            $('[name="' + timers[timer] + '_seconds"]').val(timer_value_seconds);
        }

        //sync
        var learning_setting = $('[name="learning_setting"]:checked').val();
        var sync = (learning_setting === "on") ? 1 : 0;
        $('[name="sync"]').val(sync);

        //random_selection
        var random_selection = defaults[sync]['random_selection'];
        $('[name="random_selection"]').val(random_selection);

        //n_selected_answers
        var n_selected_answers = defaults[sync]['n_selected_answers'];
        $('[name="n_selected_answers"]').val(n_selected_answers);

    });


    var e = document.querySelector('#click-circle1');
    e.addEventListener('click', function(event){
        var e = document.querySelector('#popup');
        //e.setAttribute('style', 'display:block;');
        e.style.display = 'block';
        // alert('OK');
    });

    var e = document.querySelector('#click-circle2');
    e.addEventListener('click', function(event){
        var e = document.querySelector('#popup');
        e.style.display = 'block';
        // e.setAttribute('style', 'display:block;');
    });

    //next page
    $('.create-flow-next').click(function(event) {
        event.stopPropagation();
        event.preventDefault();
        var learning_setting = $('[name="learning_setting"]:checked').val();
        var goto = $(this).attr('goto');
        sync = sync_table[learning_setting];
        flow_apply_defaults();
        $('#page-1').fadeOut(400,'swing', function() {
            //$('.page').hide();
            $('#page-' + goto).fadeIn();
        });
    });

    $(document).on('pagecreate', function() {
        //init
        flow_apply_defaults();

        $('#first_group_size').on('slidestop', function(event) {
            var n_students = parseInt($('#first_group_size').val(), 10);

            var radius = Math.pow(n_students,1/10) / Math.pow(3,1/10) * 17;
            $('.level1 circle').attr('r', radius);
        });

        $('#n_levels').on('slidestop', function(event) {
            var pyramid_number = $('#n_levels').val();

            $('.pyramid-animation').hide();
            $('#pyramid-levels-' + pyramid_number).show();
        });

        $('[data-role="popup"]').popup( "option", "history", false );
    });

    //tooltip popups
    $('[text-data]').each(function() {
        $this = $(this);
        $tooltip = $($this.attr('text-data') + '-text');
        $tooltip.on('click', function(event) {
            event.stopPropagation();
            event.preventDefault();
        });
    });

    $('[text-data]').on('click', function(event) {
        event.stopPropagation();
        event.preventDefault();
        $this = $(this);
        $tooltip = $($this.attr('text-data') + '-text');
        $tooltip.removeClass('out');
        $tooltip.addClass('in');
        $tooltip.addClass('pop');
        $tooltip.addClass('ui-overlay-shadow');
        $tooltip.addClass('ui-corner-all');
        $('#pop-background').show();
        $tooltip.show();
    });

    $('#pop-background').on('click', function(event) {
        event.stopPropagation();
        $('[text-data]').each(function() {
            $tooltip = $($this.attr('text-data') + '-text');
            $tooltip.removeClass('in');
            $tooltip.addClass('out');
            $tooltip.hide();
        });
        $(this).hide();
    });


    //util
    function set_field(field, value) {
        if($('[name="' + field + '"][type="radio"]').length) {
            $('[name="' + field + '"][value="' + value + '"]').prop("checked", true);
        } else if($('select[name="' + field + '"]').length) {
            $('[name="' + field + '"] [value="' + value + '"]').prop("selected", true);
        } else {
            $('[name="' + field + '"]').val(value);
        }
    }


</script>
</body>
</html>