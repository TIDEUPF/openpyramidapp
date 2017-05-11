<?php
include('dbvar.php');

$teacher_id = $_SESSION['user'];
\Flow\set_fid($_REQUEST['edit']);

include __DIR__ . '/../elements/activity_teacher_view.php';