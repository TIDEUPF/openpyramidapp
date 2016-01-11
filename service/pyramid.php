<?php

include_once('../init.php');

global $link, $fid, $pyramid_minsize, $flow_data;

while(true) {
    sleep(1);

    //there is no flow
    if (!\Pyramid\get_current_flow())
        continue;

    //available users
    $result = mysqli_query($link, "select distinct * from flow_available_students where fid='$fid' and sid not in (select sid from pyramid_students where fid = '$fid')");
    $nflow_students = mysqli_num_rows($result);

    //put remaining users in the last pyramid immediatly
    if(!\Pyramid\remaining_pyramids() and $nflow_students > 0) {
        //last pyramid
        $result = mysqli_query($link, "select pg_pid as pid from pyramid_groups where pg_fid='$fid' order by pg_pid desc limit 1");
        $pid_row = mysqli_fetch_assoc($result);
        $pid = $pid_row['pid'];

        \Pyramid\update_pyramid($fid, $pid);
        continue;
    }

    //not enough students to create a new pyramid
    if($nflow_students<$pyramid_minsize)
        continue;

    \Pyramid\create_pyramid($fid, $flow_data['levels'], $flow_data['nostupergrp']);

}
