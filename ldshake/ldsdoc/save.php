<?php
$document_id = isset($_REQUEST['document_id']) ? (int)$_REQUEST['document_id'] : null;
$sectoken = $_POST['sectoken'];

$data = \ldshake\get_document_data($document_id);
\ldshake\send_binary_data($data);