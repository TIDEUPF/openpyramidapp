<?php
namespace ldshake;

function check_student_session_flow() {
    if(!isset($_SESSION['ldshake_guid'])) {
        $body = \View\element("no_ldshake_flow", []);

        \View\page(array(
            'title' => 'Question',
            'body' => $body,
        ));
        exit;
    }
}

function get_document_data($document_id) {
    global $link;

    $document_id = (int)$document_id;

    $sql = <<<SQL
select json
from ldshake_editor
where doc_id = '{$document_id}'
SQL;

    $result = mysqli_query($link, $sql);

    if(!(mysqli_num_rows($result) > 0)) {
        throw new Exception("Non existing document_id");
    }

    while($row = mysqli_fetch_assoc($result)) {
        $result_array[] = $row;
    }

    return $result_array[0]['json'];
}

function send_binary_data($data) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . strlen($data));
    echo $data;
    exit;
}

function view_non_live_summary($document_id) {
    $non_live_html = true;

    ob_start();
    include __DIR__ . '/../actions/summary.php';
    $html = ob_get_contents();
    ob_end_clean();

    return $html;
}

function check_user_ldshake_flow() {
    $non_live_html = true;

    ob_start();
    include __DIR__ . '/../actions/summary.php';
    $html = ob_get_contents();
    ob_end_clean();

    return $html;
}

function create_zippped_summary($html) {
    $zip_filepath = tempnam(sys_get_temp_dir(), 'pyramid');

    $zipped_summary_object = new \ZipArchive();
    $zipped_summary_object->open($zip_filepath, \ZipArchive::CREATE);
    $zipped_summary_object->addFromString('index.html', $html);
    $zipped_summary_object->close();

    $zip_file_contents = file_get_contents($zip_filepath);
    unlink($zip_filepath);

    return $zip_file_contents;
}

function ldshake_save_document($document_id, $sectoken, $json_string)
{
    global $link;

    $document_id_sql = mysqli_real_escape_string($link, $document_id);
    $json_string_sql = mysqli_real_escape_string($link, $json_string);
    $sectoken_sql = mysqli_real_escape_string($link, $sectoken);

    $sql = <<<SQL
insert into flow values (
null, 
'{$document_id_sql}', 
'{$sectoken_sql}',
'{$json_string_sql}'
)
SQL;

    $insert_result = mysqli_query($link, $sql);

    if (!$fid = mysqli_insert_id($link))
        return null;

    return $insert_result;
}

function ldshake_update_document($document_id, $sectoken, $json_string) {
    global $link;

    $document_id_sql = mysqli_real_escape_string($link, $document_id);
    $json_string_sql = mysqli_real_escape_string($link, $json_string);
    $sectoken_sql = mysqli_real_escape_string($link, $sectoken);

    $sql = <<<SQL
update `ldshake_editor` set
`json` = '{$json_string_sql}'
WHERE 
`doc_id` = '{$document_id_sql}' AND 
`sectoken` = '{$sectoken_sql}'
SQL;

    $result = mysqli_query($link, $sql);

    $affected_rows = mysqli_affected_rows($link);
    if (!($affected_rows > 0)) {
        return null;
    }

    return true;
}

function return_success() {
    http_response_code(200);
    exit;
}

function write_flow($params) {
    global $link, $sname;

    $teacher_id = $params['teacher_id'];
    $ldshake_guid = $params['ldshake_guid'];

    $sname = $teacher_id;
    \Util\log_submit();

    $flow_fields = $params['flow_fields'];
    $flow_data_decoded = json_decode($params['flow_data']);

    //validate
    $error = false;
    $flow_object = new \stdClass();
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

    $sql = <<<SQL
insert into flow values (
'{$ldshake_guid}', 
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
) ON DUPLICATE KEY UPDATE
fname = '{$flow_data['activity']}',
nostupergrp = '{$flow_data['first_group_size']}', 
levels = '{$flow_data['n_levels']}', 
pyramid_size = '{$flow_data['min_students_per_pyramid']}', /*pyramid_size*/
pyramid_minsize = '{$flow_data['min_students_per_pyramid']}', /*min_pyramid*/ 
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
SQL;

    mysqli_query($link, $sql);

    if (mysqli_errno($link) > 0)
        $error = true;

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
    if($error) {
        throw new Exception("Error inserting the flow");
    }

    return true;
}