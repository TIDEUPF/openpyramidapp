<?php

include_once('../init.php');

global $link, $fid, $pyramid_minsize;

while(true) {
    sleep(1);

    if (!\Pyramid\get_current_flow())
        continue;

    $result = mysqli_query($link, "select * from flow_available_students where fid='$fid' and sid not in (select sid from pyramid_students where fid = '$fid')");
    $nflow_students = mysqli_num_rows($result);

    //not enough students to create a new pyramid
    if($nflow_students<$pyramid_minsize)
        continue;

    \Pyramid\create_pyramid($fid, 2, 2);

}
