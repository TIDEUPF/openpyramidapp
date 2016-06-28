<?php
session_start();
include('dbvar.php');

if(isset($_SESSION['user'])) {
    $teacher_id = $_SESSION['user'];
}
    
?><!DOCTYPE html>
<html lang="en">
<head>
    <title>Activity</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
    <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <link rel="stylesheet" type="text/css" href="elements/resources/css/teacher/styles.css">
</head>
<body>

<div data-role="page">
    <div data-role="main" class="ui-content">
        <div id="activity-body">
            <div id="activity-left-pane">
                <div id="activity-teacher-id"><?=$teacher_id?></div>
                <button activity="teacher"><?=TS("Create activity")?></button>
                <button activity="activities"><?=TS("View activities")?></button>
                <button activity="logout"><?=TS("Logout")?></button>
            </div>
            <iframe src id="activity-iframe"></iframe>
        </div>
    </div>
</div>
<script>
    $('button').on('click', function() {
        $('button').removeClass('current-activity');
        $(this).addClass('current-activity');
        $('iframe').attr("src" , $(this).attr("activity") + '.php')
    });
</script>


</body>
</html>