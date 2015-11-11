<?php

namespace Action;

function execute($action, $params = null) {
    global $link;

    if(strstr($action, '..'))
        throw new \Exception("wrong action");

    $action_file = __DIR__ . '/../actions/' . $action . '.php';

    if(!file_exists($action_file))
        throw new \Exception("wrong action");

    include($action_file);
}
