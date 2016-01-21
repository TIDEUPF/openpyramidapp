<?php

function init_cfg() {
    global $default_teacher_question, $answer_required_percentage, $answer_submit_required_percentage, $timeout, $answer_skip_timeout ,$answer_timeout, $pyramid_minsize, $device;

    $default_teacher_question = 'Please submit the question';
    $device = 'tablet';
    $answer_required_percentage = 78;
    $answer_submit_required_percentage = 80;
    $timeout = 120;
    $answer_skip_timeout = 15;
    $answer_timeout = 120;

    $pyramid_minsize = 8;
}