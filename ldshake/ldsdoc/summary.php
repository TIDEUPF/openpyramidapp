<?php
include_once __DIR__ . '/../../libs.php';

$sectoken = $_REQUEST['sectoken'];
$document_id = isset($_REQUEST['document_id']) ? (int)$_REQUEST['document_id'] : null;

$html_summary = \ldshake\view_non_live_summary($document_id);
$zip_contents = \ldshake\create_zippped_summary($html_summary);

\ldshake\send_binary_data($zip_contents);
exit;