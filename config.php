<?php

require_once __DIR__ . '/vendors/mobiledetect/Mobile_Detect.php';

function init_cfg() {
    global $default_teacher_question, $force_email, $peer_toolbar_strlen, $timeout, $answer_skip_timeout ,$answer_timeout, $pyramid_minsize, $device, $node_path;
    $detect = new Mobile_Detect;

    //$force_email = true;
    $force_email = false;
    $default_teacher_question = 'Please submit the question';

    $device = 'tablet';
    $ajax = false;
    if (php_sapi_name() != "cli") {
        $headers = apache_request_headers();
    } else {
        $headers = [];
    }

    if(isset($headers['X-Requested-With'])) {
        if($headers['X-Requested-With'] == 'XMLHttpRequest') {
            $ajax = true;
        }
    }

    //detection not needed for ajax
    if(!$ajax and php_sapi_name() != "cli") {
        if ($detect->isMobile() and !$detect->isTablet())
            $device = 'phone';
    }

    \Util\log(['activity' => 'device', 'device' => $device]);

    if(empty($node_path))
        $node_path = 'socket.io';

    load_translation("en");
    $timeout = 120;
    $answer_skip_timeout = 15;
    $answer_timeout = 120;
    $peer_toolbar_strlen = 50;
    $pyramid_minsize = 8;
}