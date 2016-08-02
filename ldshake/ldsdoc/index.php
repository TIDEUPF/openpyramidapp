<?php
error_reporting(0);
//recogemos las variables

$fichero = $_FILES['document'];
$sectoken = $_POST['sectoken'];
$document_id = mt_rand(1,5000000);

//$ruta = __DIR__.'/../../temporalData/'.$document_id;
//$carpeta = mkdir($ruta);


// comprobamos que se hayan recogido correctamente
if (empty ($sectoken));
{
    header($_SERVER['protocol'] . ' 500 Error en sectoken', true, 500);
}

if ($fichero['error'] != UPLOAD_ERR_OK or !isset($_FILES['document']))
{
    $new = true;
} else {

    try {
        if (!($json_string = file_get_contents($fichero ["tmp_name"])))
            throw new Exception("File error");

        if (json_decode($json_string) === NULL)
            throw new Exception("Invalid JSON");

    } catch (Exception $e) {
        header($_SERVER['protocol'] . ' 500 Invalid document', true, 500);
        exit;
    }

    global $link;
    $json_string_sql = mysqli_real_escape_string($link, $json_string);
    $sectoken_sql = mysqli_real_escape_string($link, $sectoken);


    $sql = <<<SQL
insert into flow values (
null, 
'{$document_id}', 
'{$sectoken_sql}',
'$json_string_sql'
)
SQL;

    $insert_result = mysqli_query($link, $sql);

    if (!$fid = mysqli_insert_id($link)) {
        header($_SERVER['protocol'] . ' 500 Invalid document', true, 500);
        exit;
    }

}
global $url;
header($_SERVER['protocol'] . ' 201 Created', true, 201);
$response = $url.'krating/ldshake/ldsdoc/'.$document_id;
echo $response;
exit;