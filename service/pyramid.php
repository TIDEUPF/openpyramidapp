<?php

include_once('../init.php');

global $link, $fid, $pyramid_minsize, $pyramid_size, $flow_data, $ps;
echo 'init passed';
while(true) {
    sleep(1);

    echo "iteration started\n";
    //there is no flow
    if (!\Pyramid\get_current_flow())
        continue;

    //available users
    $result_avail = mysqli_query($link, "select distinct * from flow_available_students where fid='$fid' and sid not in (select sid from pyramid_students where fid = '$fid')");
    $nflow_students = mysqli_num_rows($result_avail);

    //users assigned to a pyramid
    $result_assign = mysqli_query($link, "select sid from pyramid_students where fid = '$fid'");
    $npy_students = mysqli_num_rows($result_assign);

    //put remaining users in the last pyramid immediatly
    if(!\Pyramid\remaining_pyramids() and $nflow_students > 0) {
        while($user = mysqli_fetch_assoc($result_avail))
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

    if($flow_data['multi_py'] > 0) {
        //users left to be assigned
        $nleft_to_assign = $flow_data['expected_students'] - $npy_students;

        if ($nleft_to_assign >= floor($pyramid_size * 1.20))
            $new_pyramid_size = $pyramid_size;
        else
            $new_pyramid_size = $pyramid_minsize;
    } else {
        $new_pyramid_size = $pyramid_minsize;
    }

    //not enough students to create a new pyramid
    if($nflow_students<$new_pyramid_size)
        continue;

    \Pyramid\create_pyramid($fid, $flow_data['levels'], $flow_data['nostupergrp'], $new_pyramid_size);
    \Util\log(['activity' => 'new_pyramid']);
    echo "created a new pyramid\n";
}
