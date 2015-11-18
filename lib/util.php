<?php
namespace Util;

function sanitize_array($data_array) {
    global $link;

    $sanitized_array = array();
    if(!empty($data_array)) {
        foreach($data_array as $entry) {
            $sanitized_array[] = mysqli_real_escape_string($link, $entry);
        }
    }

    return $data_array;
    return $sanitized_array;
}