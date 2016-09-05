<?php

if(!isset($non_live_html))
    session_start();

include('dbvar.php');
global $node_path;

//include('inc_pyramid_func.php');


$flow_fields = [
    'activity',
    'task_description',
    'learning_setting',
    'discussion',
    'expected_students',
    'first_group_size',
    'n_levels',
    'multiple_pyramids',
    'min_students_per_pyramid',
    'satisfaction',
    's_question',
    'h_question',
    's_rating',
    'h_rating',
    'sync',
    'random_selection',
    'n_selected_answers'
];

$flow_summary_fields = [
    'activity' => T("Activity name"),
    'task_description' => T("Student task"),
    'n_final_outcomes' => T("Final outcomes"),
    'expected_students' => T("Total number of students"),
    'first_group_size' => T("No. of students per group at initial rating level"),
];

$flow_summary_timer_fields = [
    's_question_summary' => T("Option submission timer"),
    's_rating_summary' => T("Rating timer"),
    'satisfaction' => T("Satisfaction percentage"),
];

if(isset($_SESSION['user'])) {
    $teacher_id = $_SESSION['user'];

    if($_REQUEST['edit']) {
        $fid = (int)$_REQUEST['edit'];

        $sql = <<<SQL
select * from `flow` 
where 
`fid` = {$fid} AND 
`teacher_id` = '{$teacher_id}'
SQL;

        $flow_result = mysqli_query($link, $sql);
        if(!(mysqli_num_rows($flow_result) > 0)) {
            $error = true;
        }

        $row = mysqli_fetch_assoc($flow_result);

        $flow_object = json_decode($row['json']);

        //check fields
        foreach ($flow_fields as $field) {
            if (!isset($flow_object->$field)) {
                $error = true;
                break;
            }
        }

        $edit = true;
    } elseif(isset($_POST['create_flow'])) {
        $action = $_POST['create_flow'];
        $flow_data_decoded = json_decode($_POST['flow_data']);

        //validate
        $error = false;
        $flow_object = new stdClass();
        foreach ($flow_fields as $field) {
            if (!isset($flow_data_decoded->$field)) {
                $error = true;
                break;
            }

            if (empty($flow_data_decoded->$field) and $flow_data_decoded->$field !== "0" and $flow_data_decoded->$field !== 0) {
                $error = true;
                break;
            }

            $flow_object->$field = is_numeric($flow_data_decoded->$field) ? ((int)$flow_data_decoded->$field) : $flow_data_decoded->$field;
        }

        //validate n_levels
        $n_levels_fields = [
            'min_students_per_pyramid',
            'first_group_size',
        ];

        foreach ($n_levels_fields as $field) {
            if (!(is_integer($flow_object->$field) and $flow_object->$field > 0)) {
                $error = true;
            }
        }

        //number of levels
        $n_levels = floor(log(floor($flow_object->min_students_per_pyramid / $flow_object->first_group_size), 2)) + 2;
        if ($flow_object->n_levels > $n_levels)
            $flow_object->n_levels = $n_levels;

        //sanitize sql
        $flow_data = [];
        foreach ($flow_fields as $field) {
            $flow_data[$field] = mysqli_real_escape_string($link, $flow_object->$field);
        }

        $flow_object_json = json_encode($flow_object);
        $flow_object_json_sql = mysqli_real_escape_string($link, $flow_object_json);

        $datestamp = time();

        if($action == "create") {
            $sql = <<<SQL
insert into flow values (
null, 
'{$teacher_id}', 
'{$flow_data['activity']}',
'', /*legacy*/
'', /*legacy*/
'', /*legacy*/
'{$flow_data['first_group_size']}', 
'{$flow_data['n_levels']}', 
'{$flow_data['min_students_per_pyramid']}', /*$pyramid_size*/
'{$flow_data['min_students_per_pyramid']}', /*$min_pyramid*/ 
'{$flow_data['expected_students']}', 
'1', /*legacy*/
'{$datestamp}', 
'{$flow_data['s_question']}', 
'{$flow_data['s_rating']}', 
'{$flow_data['h_question']}', 
'{$flow_data['h_rating']}', 
'{$flow_data['task_description']}',
'{$flow_data['discussion']}',
'{$flow_data['sync']}',
'{$flow_data['multiple_pyramids']}',
'{$flow_data['n_selected_answers']}',
'{$flow_data['random_selection']}',
'{$flow_object_json_sql}'
)
SQL;

            $insert_result = mysqli_query($link, $sql);

            if (!mysqli_insert_id($link))
                $error = true;
        } else {
            $fid = (int)$_REQUEST['fid'];

            $sql = <<<SQL
update flow set
fname = '{$flow_data['activity']}',
nostupergrp = '{$flow_data['first_group_size']}', 
levels = '{$flow_data['n_levels']}', 
pyramid_size = '{$flow_data['min_students_per_pyramid']}', /*$pyramid_size*/
pyramid_minsize = '{$flow_data['min_students_per_pyramid']}', /*$min_pyramid*/ 
expected_students = '{$flow_data['expected_students']}', 
`timestamp` = '{$datestamp}', 
question_timeout = '{$flow_data['s_question']}', 
rating_timeout = '{$flow_data['s_rating']}', 
hardtimer_question = '{$flow_data['h_question']}', 
hardtimer_rating = '{$flow_data['h_rating']}', 
question = '{$flow_data['task_description']}',
ch = '{$flow_data['discussion']}',
sync = '{$flow_data['sync']}',
multi_py = '{$flow_data['multiple_pyramids']}',
n_selected_answers = '{$flow_data['n_selected_answers']}',
random_selection = '{$flow_data['random_selection']}',
`json`= '{$flow_object_json_sql}'
WHERE
fid = '{$fid}' AND 
teacher_id = '{$teacher_id}'
SQL;

            $update_result = mysqli_query($link, $sql);

            if (!(mysqli_affected_rows($link) > 0))
                $error = true;

            $edit = true;
        }
        /*
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
*/
    }
} elseif(isset($non_live_html)) {
    $document_id = (int)$document_id;

    $sql = <<<SQL
select * from `ldshake_editor` 
where 
`doc_id` = {$document_id} 
SQL;

    $flow_result = mysqli_query($link, $sql);
    if (!(mysqli_num_rows($flow_result) > 0)) {
        throw new Exception("Document not found");
    }

    $row = mysqli_fetch_assoc($flow_result);

    $flow_object = json_decode($row['json']);

//check fields
    foreach ($flow_fields as $field) {
        if (!isset($flow_object->$field)) {
            $error = true;
            break;
        }
    }

    $edit = true;
} else {
    header("location: login.php");
    exit(0);
}

global $default_teacher_question;
$tq = $default_teacher_question;

//TODO: restore form http://stackoverflow.com/questions/19109884/serializing-and-deserializing-a-form-with-its-current-state


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
        'discussion' => [
            'sync' => 0,
            's_question' => 24*3600,
            'h_question' => 36*3600,
            's_rating' => 24*3600,
            'h_rating' => 36*3600,
            'satisfaction' => 60,
            'discussion' => 1,
            'n_selected_answers' => 1,
            'random_selection' => 1,
            'n_levels' => 3,
            'first_group_size' => 4,
            'multiple_pyramids' => 1,
        ],
        'no_discussion' => [
            'sync' => 0,
            's_question' => 24*3600,
            'h_question' => 36*3600,
            's_rating' => 24*3600,
            'h_rating' => 2*24*3600,
            'satisfaction' => 60,
            'discussion' => 0,
            'n_selected_answers' => 1,
            'random_selection' => 1,
            'n_levels' => 3,
            'first_group_size' => 4,
            'multiple_pyramids' => 1,
        ],
    ],
    'sync' => [
        'discussion' => [
            'sync' => 1,
            's_question' => 120,
            'h_question' => 240,
            's_rating' => 180,
            'h_rating' => 300,
            'satisfaction' => 60,
            'discussion' => 1,
            'n_selected_answers' => 1,
            'random_selection' => 1,
            'n_levels' => 3,
            'first_group_size' => 4,
            'multiple_pyramids' => 1,
        ],
        'no_discussion' => [
            'sync' => 1,
            's_question' => 120,
            'h_question' => 240,
            's_rating' => 60,
            'h_rating' => 180,
            'satisfaction' => 60,
            'discussion' => 0,
            'n_selected_answers' => 1,
            'random_selection' => 1,
            'n_levels' => 3,
            'first_group_size' => 4,
            'multiple_pyramids' => 1,
        ],
    ],
];

$data_disabled = "";
if(isset($edit)) {
    $data_disabled = "data-disabled=\"true\"";
}

header('Content-Type: text/html; charset=utf-8');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>CreateActivityPage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>

    <?php if(!isset($non_live_html)): ?>
    <link rel="stylesheet" type="text/css" href="elements/resources/css/teacher/styles.css">
    <script src="https://cdn.socket.io/socket.io-1.3.7.js"></script>
    <script src="lib/actions.js"></script>
    <script type="text/javascript">
        var socket = io({multiplex : false, 'reconnection': true,'reconnectionDelay': 3000,'maxReconnectionAttempts':Infinity, path: '/<?=$node_path?>/'});
    </script>
    <?php else:?>
    <style>
    <?php echo file_get_contents(__DIR__ . '/../elements/resources/css/teacher/styles.css');?>
    </style>
    <?php endif;?>
</head>

<body>
<input name="page" type="hidden" value="summary"/>
<input name="username" type="hidden" value="<?=htmlspecialchars($teacher_id)?>"/>
<div data-role="page">
    <div data-role="header">
        <h1>Activity Summary</h1>
    </div>

    <div id="pyramid-summary-levels-block">
        <div id="pyramid-levels-2" class="pyramid-animation" style="position: relative;float: left;margin-left: 15px;">
            <svg viewBox="70 5 280 210">
                <polygon points="210,5 350,210 70,210" style="fill:pink;stroke:purple;stroke-width:2" />
                <circle cx="140" cy="165" r="15" stroke="green" stroke-width="2" fill="yellow" />
                <circle cx="188" cy="165" r="15" stroke="green" stroke-width="2" fill="yellow" />
                <circle cx="237" cy="165" r="15" stroke="green" stroke-width="2" fill="yellow" />
                <circle cx="285" cy="165" r="15" stroke="green" stroke-width="2" fill="yellow" />

                <line x1="130" y1="125" x2="290" y2="125" style="stroke:rgb(255,0,155);stroke-width:4" />

                <text x="120" y="205" fill="red"><?=TS('Level 1 – Individual level')?></text>
                <text x="55" y="140" fill="red" transform="rotate(-52,50,60)"><?=TS('Rating level(s)')?></text>

                <circle cx="210" cy="85" r="23" fill="red" stroke="blue" stroke-width="2"></circle>
            </svg>
        </div>
    
        <div id="pyramid-levels-3" class="pyramid-animation" style="position: relative;float: left;margin-left: 15px;">
            <svg viewBox="70 5 280 210">
                <polygon points="210,5 350,210 70,210" style="fill:pink;stroke:purple;stroke-width:2" />
                <circle cx="120" cy="175" r="12" stroke="green" stroke-width="2" fill="yellow" />
                <circle cx="180" cy="175" r="12" stroke="green" stroke-width="2" fill="yellow" />
                <circle cx="240" cy="175" r="12" stroke="green" stroke-width="2" fill="yellow" />
                <circle cx="300" cy="175" r="12" stroke="green" stroke-width="2" fill="yellow" />
                <line x1="112" y1="150" x2="308" y2="150" style="stroke:rgb(255,0,155);stroke-width:4" />
    
                <text x="120" y="205" fill="red">Level 1 – Individual level</text>
                <text x="55" y="140" fill="red" transform="rotate(-52,50,60)">Rating level(s)</text>
    
                <g class="level1">
                    <circle id="click-circle2" cx="230" cy="120" r="17" fill="green" stroke="black" stroke-width="2"></circle>
                    <circle id="click-circle1" cx="180" cy="120" r="17" fill="green" stroke="black" stroke-width="2"></circle>
                </g>
    
    
                <line x1="149" y1="95" x2="271" y2="95" style="stroke:rgb(255,0,155);stroke-width:4" />
    
                <circle cx="210" cy="62" r="23" fill="red" stroke="blue" stroke-width="2"></circle>
            </svg>
        </div>
    
        <div id="pyramid-levels-4" class="pyramid-animation" style="position: relative;float: left;margin-left: 15px;">
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
    
                    <g class="level1">
                        <circle id="click-circle1" cx="180" cy="-45" r="17" fill="purple" stroke="black" stroke-width="2"></circle>
                        <circle id="click-circle1" cx="230" cy="-45" r="17" fill="green" stroke="black" stroke-width="2"></circle>
                        <circle id="click-circle1" cx="130" cy="-45" r="17" fill="green" stroke="black" stroke-width="2"></circle>
                        <circle id="click-circle1" cx="280" cy="-45" r="17" fill="purple" stroke="black" stroke-width="2"></circle>
                    </g>
    
                    <g>
                        <circle id="click-circle1" cx="180" cy="-105" r="17" fill="purple" stroke="black" stroke-width="2"></circle>
                        <circle id="click-circle1" cx="230" cy="-105" r="17" fill="green" stroke="black" stroke-width="2"></circle>
                    </g>
                </g>
    
                <text x="55" y="140" fill="red" transform="rotate(-52,50,60)">Rating level(s)</text>
    
                <line x1="149" y1="95" x2="271" y2="95" style="stroke:rgb(255,0,155);stroke-width:4" />

                <circle cx="210" cy="62" r="23" fill="red" stroke="blue" stroke-width="2"></circle>
            </svg>
        </div>
    
        <div id="pyramid-levels-5" class="pyramid-animation" style="position: relative;float: left;margin-left: 15px;">
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
    
                    <g class="level1">
                        <circle id="click-circle1" cx="180" cy="-45" r="17" fill="purple" stroke="black" stroke-width="2"></circle>
                        <circle id="click-circle1" cx="230" cy="-45" r="17" fill="green" stroke="black" stroke-width="2"></circle>
                        <circle id="click-circle1" cx="130" cy="-45" r="17" fill="green" stroke="black" stroke-width="2"></circle>
                        <circle id="click-circle1" cx="280" cy="-45" r="17" fill="purple" stroke="black" stroke-width="2"></circle>
                        <circle id="click-circle1" cx="85" cy="-45" r="17" fill="purple" stroke="black" stroke-width="2"></circle>
                        <circle id="click-circle1" cx="330" cy="-45" r="17" fill="green" stroke="black" stroke-width="2"></circle>
                    </g>
    
                    <g>
                        <circle id="click-circle1" cx="180" cy="-100" r="17" fill="purple" stroke="black" stroke-width="2"></circle>
                        <circle id="click-circle1" cx="230" cy="-100" r="17" fill="green" stroke="black" stroke-width="2"></circle>
                        <circle id="click-circle1" cx="280" cy="-100" r="17" fill="purple" stroke="black" stroke-width="2"></circle>
                        <circle id="click-circle1" cx="130" cy="-100" r="17" fill="green" stroke="black" stroke-width="2"></circle>
                    </g>
    
                    <g>
                        <circle id="click-circle1" cx="180" cy="-150" r="17" fill="purple" stroke="black" stroke-width="2"></circle>
                        <circle id="click-circle1" cx="230" cy="-150" r="17" fill="green" stroke="black" stroke-width="2"></circle>
                    </g>
                </g>
    
                <text x="55" y="140" fill="red" transform="rotate(-52,50,60)">Rating level(s)</text>
    
                <!--circle cx="170" cy="120" r="17" stroke="green" stroke-width="2" fill="yellow"-->
    
    
                <line x1="149" y1="95" x2="271" y2="95" style="stroke:rgb(255,0,155);stroke-width:4" />

                <circle cx="210" cy="62" r="23" fill="red" stroke="blue" stroke-width="2"></circle>

            </svg>
        </div>
    </div>

    <?php foreach($flow_summary_fields as $kfield => $field):?>
    <div class="summary-field-block">
        <div class="summary-field-title"><?=$field?></div>
        <div id="<?=$kfield?>" class="summary-field-data"></div>
        <div style="clear:both"></div>
    </div>
    <?php endforeach;?>

    <div id="summary-lower-block">
    <?php foreach($flow_summary_timer_fields as $kfield => $field):?>
        <div class="summary-timer-field-block">
            <div class="summary-timer-field-title"><?=$field?></div>
            <div id="<?=$kfield?>" class="summary-timer-field-data"></div>
            <div style="clear:both"></div>
        </div>
    <?php endforeach;?>
    </div>


    <input type="hidden" name="sync">
    <input type="hidden" name="random_selection">
    <input type="hidden" name="n_selected_answers">
    <div id="n_levels" style="display: none;"></div>

    <input type="hidden" name="s_question">
    <input type="hidden" name="h_question">
    <input type="hidden" name="s_rating">
    <input type="hidden" name="h_rating">

    <input type="hidden" name="flow_data">
    <?php if($edit):?>
        <input type="hidden" name="fid" value="<?=$fid?>">
    <?php endif;?>
</div>
<script>

    //timers in seconds
    var timers = [
        's_question',
        'h_question',
        's_rating',
        'h_rating'
    ];

    var timers_summary = [
        's_question',
        'h_question',
        'satisfaction',
    ];

    var new_flow_fields = [
        'satisfaction',
        'n_selected_answers',
        'n_levels',
        'first_group_size',
        'discussion',
        'random_selection',
        'multiple_pyramids',
        's_question',
        'h_question',
        's_rating',
        'h_rating'
    ];

    var edit_flow_fields = <?=json_encode($flow_fields)?>;
    var flow_summary_fields = <?=json_encode($flow_summary_fields)?>;
    var flow_summary_timer_fields = <?=json_encode($flow_summary_timer_fields)?>;

    var units_in_seconds = {
        'days': 86400,
        'hours': 3600,
        'minutes': 60
    };

    var sync_table = {
        'classroom': 'sync',
        'distance': 'async'
    };

    var discussion_table = {
        0: 'no_discussion',
        1: 'discussion'
    };

    var defaults = <?=json_encode($defaults)?>;
    var sync = 'sync';
    var discussion = 'discussion';

    var edit = <?=(isset($edit)) ? 'true' : 'false'?>;
    <?php if(isset($edit)):?>
    var flow_edit_data = <?=json_encode($flow_object)?>;
    var current_default = flow_edit_data;
    var default_flow_fields = edit_flow_fields;
    var sync = default_flow_fields.sync;
    var discussion = default_flow_fields.discussion;
    <?php else:?>
    var current_default = defaults[sync][discussion];
    var default_flow_fields = new_flow_fields;
    <?php endif;?>

    function time_field_to_seconds(field) {
        var unit_time = get_field_integer(field + '_unit_value');
        var unit = $('[name="' + field + '_unit' + '"]:checked').val();
        var unit_seconds = units_in_seconds[unit];
        var seconds_time = unit_time * unit_seconds;

        return seconds_time;
    }

    function time_seconds_to_units(value) {
        var converted_value = {};
        for(var unit in units_in_seconds) {
            if(value < units_in_seconds[unit])
                continue;

            if(value % units_in_seconds[unit] && unit != 'minutes')
                continue;

            converted_value.value = value / units_in_seconds[unit];
            converted_value.unit = unit;
            break;
        }

        return converted_value;
    }

    function restore_timers() {
        for(var timer in timers) {
            var value = current_default[timers[timer]];
            var converted_value = time_seconds_to_units(value);

            //set_field(timers[timer] + '_unit_value', converted_value.value);
            //set_field(timers[timer] + '_unit', converted_value.unit);
            set_field(timers[timer] + '_summary', converted_value.value + " " + converted_value.unit);
        }
    }

    function timers_to_seconds() {
        for(var timer in timers) {
            var converted_value = time_field_to_seconds(timers[timer]);

            set_field(timers[timer], converted_value);
        }
    }

    function flow_load_fields() {
        for(var field in default_flow_fields) {
            set_field(default_flow_fields[field], current_default[default_flow_fields[field]]);
        }
    }

    function set_n_final_outcomes() {
        var n_groups = get_n_groups();
        var n_levels_rating = get_field_integer("n_levels") - 1;
        var n_pyramids = get_number_of_pyramids();

        var n_outcomes_per_pyramid = Math.floor(n_groups / Math.pow(2, n_levels_rating - 1));
        var n_final_outcomes = n_outcomes_per_pyramid * n_pyramids;

        set_field('n_final_outcomes', n_final_outcomes);
    }

    function n_pyramids_update(event) {
        var number_of_pyramids = get_number_of_pyramids();

        set_field('n_pyramids', number_of_pyramids);
    }

    function get_n_groups() {
        var expected_students_setting = get_field_integer("expected_students");
        var number_of_pyramids = get_number_of_pyramids();
        var expected_students_per_pyramid = Math.floor(expected_students_setting / number_of_pyramids);
        var first_group_size_setting = get_field_integer("first_group_size");

        if(!(expected_students_per_pyramid > 0 && first_group_size_setting > 0))
            return false;

        var n_groups = Math.floor(expected_students_per_pyramid / first_group_size_setting);

        return n_groups;
    }

    function max_possible_levels_update(event) {
        var max_possible_levels = 4;
        var n_groups = get_n_groups();

        if(n_groups < 2)
            return false;
        else if (n_groups < 4)
            max_possible_levels = 3;
        else if (n_groups < 8)
            max_possible_levels = 4;
        else
            max_possible_levels = 5;

        $('[name="n_levels"]').attr("max", max_possible_levels);
        $('[name="n_levels"]').slider("refresh");
    }

    function update_fields() {
        n_pyramids_update();
        //update_first_group_size();
        //max_possible_levels_update();
        set_n_final_outcomes();
    }

    function get_number_of_pyramids() {
        var number_of_pyramids = 1;

        try {
            var multiple_pyramids_setting = get_field_integer("multiple_pyramids");
            var min_students_per_pyramid_setting = get_field_integer("min_students_per_pyramid");
            var expected_students_setting = get_field_integer("expected_students");

            if (multiple_pyramids_setting) {
                number_of_pyramids = Math.floor(expected_students_setting / min_students_per_pyramid_setting);
            }
        } catch (e) {

        }

        return Math.max(1, number_of_pyramids);
    }

    function update_first_group_size() {
        var expected_students_setting = get_field_integer("expected_students");
        var number_of_pyramids = get_number_of_pyramids();
        var expected_students_per_pyramid = Math.floor(expected_students_setting / number_of_pyramids);

        var max_possible_size = Math.min(10, Math.floor(expected_students_per_pyramid/2));

        $('[name="first_group_size"]').attr("max", max_possible_size);
        $('[name="first_group_size"]').slider("refresh");
    }

    //next page
    $('.create-flow-next').click(function(event) {
        event.stopPropagation();
        event.preventDefault();

        var goto = $(this).attr('goto');

        if(!edit) {
            var learning_setting = $('[name="learning_setting"]:checked').val();
            var discussion_setting = $('[name="discussion"]').val();
            sync = sync_table[learning_setting];
            discussion = discussion_table[discussion_setting];
            current_default = defaults[sync][discussion];
            flow_load_fields();
        } else {
            update_fields();
        }

        $('#page-1').fadeOut(400,'swing', function() {
            $('#page-' + goto).fadeIn();
        });
    });

    $(document).on('pagebeforecreate', function() {
        //init
        flow_load_fields();
        update_fields();
        restore_timers();

        var pyramid_number = $('#n_levels').text();

        $('.pyramid-animation').hide();
        $('#pyramid-levels-' + pyramid_number).show();
    });

    $(document).on('pagecreate', function() {
        //restore_timers();
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
    /*
    function set_field(field, value) {
        if($('[name="' + field + '"][type="radio"]').length) {
            $('[name="' + field + '"][value="' + value + '"]').prop("checked", true);
        } else if($('select[name="' + field + '"]').length) {
            $('[name="' + field + '"] [value="' + value + '"]').prop("selected", true);
        } else {
            $('[name="' + field + '"]').val(value);
        }
    }*/

    function set_field(field, value) {
        $('#' + field).text(value);
    }

    function get_field_integer(field) {
        var field_text = $('#' + field).text();
        var field_setting = parseInt(field_text, 10);
        if(!(field_setting > 0 || field_setting < 0 || field_text === "0"))
            throw field_text + " is not an integer";

        return field_setting;
    }

</script>
</body>
</html>