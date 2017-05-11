<?php
error_reporting(0);
include __DIR__ .'/../../actions/dbvar.php';
//recogemos las variables

$document_file = isset($_FILES['document']) ? $_FILES['document'] : null;
$sectoken = $_REQUEST['sectoken'];
$document_id = mt_rand(1,5000000);

//$ruta = __DIR__.'/../../temporalData/'.$document_id;
//$carpeta = mkdir($ruta);


// comprobamos que se hayan recogido correctamente
if (empty ($sectoken)) {
    header($_SERVER['protocol'] . ' 500 sectoken error', true, 500);
}

if ($document_file['error'] != UPLOAD_ERR_OK or !isset($_FILES['document'])) {
    $new = true;
    $json_string = json_encode((object)[]);
} else {
    try {
        if (!($json_string = file_get_contents($document_file ["tmp_name"])))
            throw new Exception("File error");

        if (json_decode($json_string) === NULL)
            throw new Exception("Invalid JSON");

    } catch (Exception $e) {
        header($_SERVER['protocol'] . ' 500 Invalid document', true, 500);
        exit;
    }
}

global $link;
$json_string_sql = mysqli_real_escape_string($link, $json_string);
$sectoken_sql = mysqli_real_escape_string($link, $sectoken);

$sql = <<<SQL
insert into ldshake_editor values (
null, 
'{$document_id}', 
'{$sectoken_sql}',
'{$json_string_sql}'
)
SQL;

$insert_result = mysqli_query($link, $sql);

if (!$fid = mysqli_insert_id($link)) {
    header($_SERVER['protocol'] . ' 500 Invalid document', true, 500);
    exit;
}

global $url;
header($_SERVER['protocol'] . ' 201 Created', true, 201);
$response = $url.'ldshake/ldsdoc/'.$document_id;
echo $response;
exit;