<?php
session_start();
include('dbvar.php');
global $node_path;

$flow_fields = \Flow\get_flow_default_fields();

if(isset($_REQUEST['ldshake_save']))	{
    $ldshake_sectoken = $_REQUEST['ldshake_sectoken'];
    $ldshake_doc_id = $_REQUEST['ldshake_doc_id'];
    $ldshake_flow_data = $_REQUEST['flow_data'];
    \Util\ldshake_save_document($ldshake_doc_id, $ldshake_sectoken, $ldshake_flow_data);
    exit;
} else {
    $ldshake_sectoken = $_REQUEST['sectoken'];
    $ldshake_doc_id = $_REQUEST['id'];
}
$sql = <<<SQL
select * from `ldshake_editor` where `doc_id` = '{$ldshake_doc_id}' AND `sectoken` = '{$ldshake_sectoken}'
SQL;

$flow_result = mysqli_query($link, $sql);
if(!(mysqli_num_rows($flow_result) > 0)) {
    throw new Exception("Document not found");
}

$row = mysqli_fetch_assoc($flow_result);

if($flow_object = json_decode($row['json'])) {
    $edit = true;
}

$defaults = \Flow\get_default_field_values();
$data_disabled = "";
if(isset($edit)) {
    $data_disabled = "data-disabled=\"true\"";
}

include __DIR__.'/../elements/flow_editor/flow_editor.php';