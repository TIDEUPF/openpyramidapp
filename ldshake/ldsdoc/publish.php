<?php
include_once __DIR__ . '/../../libs.php';

$sectoken = $_REQUEST['sectoken'];
$ldshake_guid = $_REQUEST['ldshake_guid'];
$teacher_id = $_REQUEST['ldshake_username'];
$action = $_REQUEST['action'];
$published_url = (isset($_REQUEST['published_url'])) ? $_REQUEST['published_url'] : null;

$document_file = isset($_FILES['document']) ? $_FILES['document'] : null;

if ($document_file['error'] != UPLOAD_ERR_OK) {
    throw new Exception("Document file not present");
}

try {
    if (!($json_string = file_get_contents($document_file ["tmp_name"])))
        throw new Exception("File error");

    //validate
    if (json_decode($json_string) === NULL)
        throw new Exception("Invalid JSON");
} catch (Exception $e) {
    header($_SERVER['protocol'] . ' 500 Invalid document', true, 500);
    exit;
}

try {
    $published_id = null;
    if ($published_url) {
        $published_url_parts = explode('/', $published_url);
        $published_id_string = $published_url_parts[count($published_url_parts) - 1];

        if (!is_numeric($published_id_string)) {
            throw new Exception("Invalid published url");
        }

        $published_id = (int)$published_id_string;
    }
} catch (Exception $e) {
    header($_SERVER['protocol'] . ' 500 Invalid published url', true, 500);
    exit;
}

$params = [
    'ldshake_guid' => $ldshake_guid,
    'published_id' => $published_id,
    'teacher_id' => $teacher_id,
    'flow_data' => $json_string,
    'flow_fields' => \Flow\get_flow_default_fields(),
];

try {
    $flow_id = \ldshake\write_flow($params);
} catch (Exception $e) {
    header($_SERVER['protocol'] . ' 500 Error creating the flow', true, 500);
    exit;
}

global $url;
header($_SERVER['protocol'] . ' 201 Created', true, 201);
$response = $url.'activity/'.$flow_id;
echo $response;
exit;