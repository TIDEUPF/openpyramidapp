<?php
session_start();
include('dbvar.php');
global $node_path;

$flow_fields = \Flow\get_flow_default_fields();

$sectoken = $_REQUEST['sectoken'];
$id = $_REQUEST['id'];

$sql = <<<SQL
select * from `ldshake_editor` where `doc_id` = '{$id}' AND `sectoken` = '{$sectoken}'
SQL;

$flow_result = mysqli_query($link, $sql);
if(!(mysqli_num_rows($flow_result) > 0)) {
    $error = true;
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