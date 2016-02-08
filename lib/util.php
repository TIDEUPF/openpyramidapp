<?php
namespace Util;

function get_sql_pyramid($params) {
    global $fid, $pid;

    $table_prefix = '';
    $fid_prefix = '';
    $pid_prefix = '';

    if(isset($params['table'])) {
        $table_prefix = $params['table'];
    }

    if(isset($params['prefix'])) {
        $fid_prefix = $params['prefix'];
        $pid_prefix = $params['prefix'];
    }

    $sql = " {$table_prefix}{$fid_prefix}fid='{$fid}' and {$table_prefix}{$pid_prefix}pid='{$pid}' ";

    return $sql;
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

function log($data) {
    global $link, $fid, $sname, $activity_level, $peer_group_id;

    $data['origin'] = 'php_backend';
    $data['fid'] = $fid;
    if(!empty($sname))
        $data['sname'] = $sname;

    $user_id = !empty($_SESSION['user_id']) ? mysqli_real_escape_string($link, $_SESSION['user_id']) : '';
    $sname = !empty($data['sname']) ? mysqli_real_escape_string($link, $data['sname']) : '';
    $date = time();

    $data_json = mysqli_real_escape_string($link, json_encode($data));
    mysqli_query($link, "insert into activity_log values (null, '$user_id', null, '$sname', '{$data['activity']}', '$activity_level', '$peer_group_id', '{$data_json}', FROM_UNIXTIME('$date'), '{$data['origin']}')");
}