<?php

namespace Flow;

function add_student() {
    global $sid, $fid, $link;

    $result = mysqli_query($link,"insert into flow_students values ('', '$fid', '$sid')");

    return !!mysqli_affected_rows($result);
}

