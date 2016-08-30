<?php
namespace ldshake;

function get_document_data($document_id) {
    global $link;

    $sql = <<<SQL
select json
from ldshake_editor
where doc_id = '{$document_id}'
SQL;

    $result = mysqli_query($link, $sql);

    if(!(mysqli_num_rows($result) > 0)) {
        throw new Exception("Non existing document_id");
    }

    while($row = mysqli_fetch_row($result)) {
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

function view__non_live_summary($document_id) {
    $non_live_html = true;

    ob_start();
    include __DIR__ . '/../actions/summary.php';
    $html = ob_get_contents();
    ob_end_clean();

    return $html;
}