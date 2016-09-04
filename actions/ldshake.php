<?php
session_start();
include('dbvar.php');

global $node_path;

$flow_fields = \Flow\get_flow_default_fields();

if(isset($_REQUEST['ldshake_summary'])) {
    $ldshake_sectoken = $_REQUEST['ldshake_sectoken'];
    $ldshake_doc_id = $_REQUEST['ldshake_doc_id'];

    $html_summary = \ldshake\view_non_live_summary($ldshake_doc_id);
    $zip_contents = \ldshake\create_zippped_summary($html_summary);

    \ldshake\send_binary_data($zip_contents);
    exit;
}

if(isset($_REQUEST['ldshake_activity_tracking'])) {
    $ldshake_fid = $_REQUEST['ldshake_fid'];
    $teacher_id = $_REQUEST['ldshake_username'];
    \Flow\set_fid($ldshake_fid);

    include __DIR__ . '/../elements/activity_teacher_view.php';
    exit;
}

if(isset($_REQUEST['ldshake_save']))	{
    $ldshake_sectoken = $_REQUEST['ldshake_sectoken'];
    $ldshake_doc_id = $_REQUEST['ldshake_doc_id'];
    $ldshake_flow_data = $_REQUEST['flow_data'];
    \ldshake\ldshake_update_document($ldshake_doc_id, $ldshake_sectoken, $ldshake_flow_data);
    \ldshake\return_success();
    exit;
} else {
    $ldshake_sectoken = $_REQUEST['sectoken'];
    $ldshake_doc_id = $_REQUEST['document_id'];
    $ldshake_iframe = true;
}
$sql = <<<SQL
select * from `ldshake_editor` where `doc_id` = '{$ldshake_doc_id}' AND `sectoken` = '{$ldshake_sectoken}'
SQL;

$flow_result = mysqli_query($link, $sql);
if(!(mysqli_num_rows($flow_result) > 0)) {
    throw new Exception("Document not found");
}

$row = mysqli_fetch_assoc($flow_result);
$flow_object = json_decode($row['json']);

if(count((array)$flow_object) > 0) {
    $edit = true;
}

$defaults = \Flow\get_default_field_values();
$data_disabled = "";
if(isset($edit)) {
    $data_disabled = "data-disabled=\"true\"";
}

include __DIR__.'/../elements/flow_editor/flow_editor.php';