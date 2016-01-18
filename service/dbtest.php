<?php

include_once('../init.php');

global $link, $fid, $pyramid_minsize, $flow_data;
echo 'init passed';
$fid=355500;
    //echo "iteration started\n";
$result = mysqli_query($link, "select @@autocommit");
$auto = mysqli_fetch_assoc($result);
foreach ($auto as $item) {
    echo $item;
}

$result = mysqli_query($link, "select @@tx_isolation");
$auto = mysqli_fetch_assoc($result);
foreach ($auto as $item) {
    echo $item;
}

while($j<5000) {
    $fv = [0, 1, 2];
    $answertimestamp = time();

    for ($i = 0; $i < 3; $i++) {
        $result = mysqli_query($link, "insert into flow_student values (null, '$fid', '$sid', '', 1, $answertimestamp)");
        $fv[$i] = $fid;
        $fid++;
    }
    //available users
    $fe = implode(',', $fv);
    //$result = mysqli_query($link, "select * from flow_student where fid in ({$fe})");
    $fl = $fid - 1;
    $result = mysqli_query($link, "select * from flow_student where fid={$fl}");
    if (mysqli_num_rows($result) != 1)
        echo "error";
    //echo mysqli_num_rows($result)."\n";
    $j++;
}