<?php

function init_cfg() {
    global $answer_required_percentage, $answer_submit_required_percentage, $timeout, $answer_skip_timeout ,$answer_timeout;

    $answer_required_percentage = 80;
    $answer_submit_required_percentage = 30;
    $timeout = 3;
    $answer_skip_timeout = 15;
    $answer_timeout = 300;
}