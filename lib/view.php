<?php

Namespace View;

function element($path, $vars, $filter = true) {
    global $device, $flow_data, $node_path;

    $current_path = __DIR__;

    $element_path = $current_path . '/../elements/' . $path . '.php';

    if(!file_exists($element_path))
        throw Exception("The element does not exist");

    $device_element_path = $current_path . '/../elements/' . $device . '/' . $path . '.php';
    if(file_exists($device_element_path))
        $element_path = $current_path . '/../elements/' . $device . '/' . $path . '.php';

    if(!ob_start())
        throw Exception("Failed to start buffer output");

    if($filter) {
        foreach ($vars as $k => $v) {
            if (is_string($v))
                $vars[$k] = htmlspecialchars($v, ENT_COMPAT, 'UTF-8');
        }
    }
    extract($vars);
    include($element_path);

    $buffer = ob_get_contents();

    if($buffer === FALSE)
        throw Exception("Failed to retrieve the buffer output");

    if(!ob_end_clean())
        throw Exception("Failed to close the buffer output");

    return $buffer;
}

function page($params) {
    $opts = [
        'title' => $params['title'],
        'body' => $params['body'],
    ];

    if(isset($params['nosocket']))
        $opts['nosocket'] = true;

    $html = element("page", $opts, false);

    header('Content-Type: text/html; charset=utf-8');
    echo $html;
}

