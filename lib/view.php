<?php

Namespace View;

function element($path, $vars) {
    $current_path = __DIR__;

    $element_path = $current_path . '/../elements/' . $path . '.php';

    if(!file_exists($element_path))
        throw Exception("The element does not exist");

    if(!ob_start())
        throw Exception("Failed to start buffer output");

    extract($vars);
    include($element_path);

    $buffer = ob_get_contents();

    if($buffer === FALSE)
        throw Exception("Failed to retrieve the buffer output");

    if(!ob_end_clean())
        throw Exception("Failed to close the buffer output");

    return $buffer;
}