<?php

require_once __DIR__ . '/vendors/mobiledetect/Mobile_Detect.php';

function init_cfg() {
    global $default_teacher_question, $answer_required_percentage, $peer_toolbar_strlen, $answer_submit_required_percentage, $timeout, $answer_skip_timeout ,$answer_timeout, $pyramid_minsize, $device;
    $detect = new Mobile_Detect;

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

    load_translation("es");
    $answer_required_percentage = 78;
    $answer_submit_required_percentage = 80;
    $timeout = 120;
    $answer_skip_timeout = 15;
    $answer_timeout = 120;
    $peer_toolbar_strlen = 50;
    $pyramid_minsize = 8;
}