<?php
namespace Util;

function get_sql_pyramid($params= null) {
    global $fid, $pid;

    $table_prefix = '';
    $fid_prefix = '';
    $pid_prefix = '';

    if(isset($params['table'])) {
        $table_prefix = $params['table'];
    }

    if(isset($params['prefix'])) {
        $fid_prefix = $params['prefix'] . '_';
        $pid_prefix = $params['prefix'] . '_';
    }

    $sql = " {$table_prefix}{$fid_prefix}fid='{$fid}' and {$table_prefix}{$pid_prefix}pid='{$pid}' ";

    return $sql;
}

function sql_gen() {
    global $ps;
    $prefixes = ['sa', 'pg', 'fsr'];

    $ps['e'] = get_sql_pyramid();
    foreach($prefixes as $p) {
        $ps[$p] = get_sql_pyramid(['prefix'=>$p]);
    }
}

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

function log_submit() {
    if(empty($_REQUEST['log']))
        return false;

    if(($data = json_decode($_REQUEST['log'])) === NULL)
        return false;

    foreach($data as $entry) {
        log(['activity' => $entry->type, 'timestamp' => floor($entry->timestamp/1000), 'origin' => 'browser', 'entry' => $entry]);
    }
}

function log($data) {
    global $link, $fid, $pid, $sname, $activity_level, $peer_group_id;

    if(!isset($data['origin']))
        $data['origin'] = 'php_backend';

    $data['fid'] = $fid;
    $data['pid'] = $pid;
    if(!empty($sname))
        $data['sname'] = $sname;

    $user_id = !empty($_SESSION['user_id']) ? mysqli_real_escape_string($link, $_SESSION['user_id']) : '';
    $sname = !empty($data['sname']) ? mysqli_real_escape_string($link, $data['sname']) : '';

    if(isset($data['timestamp']))
        $date = $data['timestamp'];
    else
        $date = time();

    if(isset($data['entry']))
        $data_json = mysqli_real_escape_string($link, json_encode($data['entry']));
    else
        $data_json = mysqli_real_escape_string($link, json_encode((object)$data));

    mysqli_query($link, "insert into activity_log values (null, '$user_id', null, '$sname', '{$data['activity']}', '$activity_level', '$peer_group_id', '{$data_json}', FROM_UNIXTIME('$date'), '{$data['origin']}')");
}

function filter_email($email_array) {
    if(empty($email_array) or !is_array($email_array))
        return $email_array;

    $filtered = [];
    foreach($email_array as $email) {
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_split = explode('@', $email);
            $filtered[] = $email_split[0];
        } else {
            $filtered[] = $email;
        }
    }
    return $filtered;
}