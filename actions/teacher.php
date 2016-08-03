<?php
session_start();
include('dbvar.php');
global $node_path;

$flow_fields = \Flow\get_flow_default_fields();

if(isset($_SESSION['user'])) {
    $teacher_id = $_SESSION['user'];

    if($_REQUEST['edit']) {
        $fid = (int)$_REQUEST['edit'];

        $sql = <<<SQL
select * from `flow` where `fid` = '{$fid}' AND `teacher_id` = '{$teacher_id}'
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
        global $sname;

        $sname = $teacher_id;
        \Util\log_submit();
        $action = $_POST['create_flow'];
        $flow_data_decoded = json_decode($_POST['flow_data']);

        //validate
        $error = false;
        $flow_object = new stdClass();
        foreach ($flow_fields as $field) {
            /*
            if (!isset($flow_data_decoded->$field)) {
                $error = true;
                break;
            }

            if (empty($flow_data_decoded->$field) and $flow_data_decoded->$field !== "0" and $flow_data_decoded->$field !== 0) {
                $error = true;
                break;
            }*/

            $flow_object->$field = is_numeric($flow_data_decoded->$field) ? ((int)$flow_data_decoded->$field) : $flow_data_decoded->$field;
        }

        //validate n_levels
        $n_levels_fields = [
            'min_students_per_pyramid',
            'first_group_size',
        ];

        foreach ($n_levels_fields as $field) {
            if (!(is_integer($flow_object->$field) and $flow_object->$field > 0)) {
                //$error = true;
            }
        }

        //encode the json object before making the flow safety checks
        $flow_object_json = json_encode($flow_object);
        $flow_object_json_sql = mysqli_real_escape_string($link, $flow_object_json);

        $min_students_per_pyramid = $flow_object->min_students_per_pyramid;
        if(!$flow_object->multiple_pyramids)
            $min_students_per_pyramid = $flow_object->expected_students;

        if($min_students_per_pyramid > $flow_object->expected_students)
            $min_students_per_pyramid = $flow_object->expected_students;

        //number of levels
        $n_levels = floor(log(floor($min_students_per_pyramid / $flow_object->first_group_size), 2)) + 2;
        if ($flow_object->n_levels -1 > $n_levels)
            $flow_object->n_levels = $n_levels + 1;

        //sanitize sql
        $flow_data = [];
        foreach ($flow_fields as $field) {
            $flow_data[$field] = mysqli_real_escape_string($link, $flow_object->$field);
        }

        //substract the individual level
        $flow_data['n_levels']--;
        $flow_data['min_students_per_pyramid'] = $min_students_per_pyramid;

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
'{$flow_data['satisfaction']}',
'{$flow_data['satisfaction']}',
'{$datestamp}',
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

            if (!$fid = mysqli_insert_id($link))
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
answer_submit_required_percentage = '{$flow_data['satisfaction']}', 
rating_required_percentage = '{$flow_data['satisfaction']}', 
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
        if(!$error) {
            header("location: summary.php?edit=" . $fid);
            exit(0);
        }
    }
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

$defaults = \Flow\get_default_field_values();
$data_disabled = "";
if(isset($edit)) {
    $data_disabled = "data-disabled=\"true\"";
}

include '../elements/flow_editor/flow_editor.php';