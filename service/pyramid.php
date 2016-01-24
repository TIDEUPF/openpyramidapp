<?php

include_once('../init.php');

global $link, $fid, $pyramid_minsize, $flow_data;
echo 'init passed';
while(true) {
    sleep(1);

    echo "iteration started\n";
    //there is no flow
    if (!\Pyramid\get_current_flow())
        continue;

    //available users
    $result = mysqli_query($link, "select distinct * from flow_available_students where fid='$fid' and sid not in (select sid from pyramid_students where fid = '$fid')");
    $nflow_students = mysqli_num_rows($result);

    //put remaining users in the last pyramid immediatly
    if(!\Pyramid\remaining_pyramids() and $nflow_students > 0) {
        while($user = mysqli_fetch_assoc($result))
            $users[] = $user['sid'];
        //last pyramid
        $result = mysqli_query($link, "select pg_pid as pid from pyramid_groups where pg_fid='$fid' order by pg_pid desc limit 1");
        $pid_row = mysqli_fetch_assoc($result);
        $pid = $pid_row['pid'];

        \Pyramid\update_pyramid($fid, $pid);
        \Util\log(['activity' => 'added_latecomers_to_pyramid', 'users' => $users]);
        echo "users added and pyramid updated\n";
        continue;
    }

    //not enough students to create a new pyramid
    if($nflow_students<$pyramid_minsize)
        continue;

    \Pyramid\create_pyramid($fid, $flow_data['levels'], $flow_data['nostupergrp']);
    \Util\log(['activity' => 'new_pyramid']);
    echo "created a new pyramid\n";
}
