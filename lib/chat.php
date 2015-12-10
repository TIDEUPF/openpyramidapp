<?php

function get_room() {

}

function get_discussion() {

}

function add_message($params) {
    global $link;

    $user_id = $params['user_id'];
    $group_id = $params['group_id'];
    $message = $params['message'];
    $timestamp = time();

    mysqli_query($link, "insert into flow_student values ('', '$fid', '$sid', '$ans_input', 0, $answertimestamp)");
}

function broadcast_message($params) {
    $user_id = $params['user_id'];
    $group_id = $params['group_id'];
    $message = $params['message'];
    $timestamp = time();

}