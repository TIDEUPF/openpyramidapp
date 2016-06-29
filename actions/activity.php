<?php
session_start();
include('dbvar.php');

if(isset($_SESSION['user'])) {
    $teacher_id = $_SESSION['user'];
} else {
    header("location: login.php");
    exit(0);
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
<body style="width:1050px;overflow-x: auto;margin: 0 auto 0 auto;">
<style>
    [data-role="main"] {
        width: 1050px !important;
        margin: 0 auto 0 auto;
        padding: 0;
        box-shadow: 3px 0px 5px -1px rgba(179,179,179,1), -3px 0px 5px -1px rgba(179,179,179,1);
    }

</style>
<div data-role="page">
    <div data-role="main" class="ui-content">
        <div id="activity-body">
            <div id="activity-left-pane">
                <div id="activity-teacher-id"><?=$teacher_id?></div>
                <button activity="teacher"><?=TS("Create activity")?></button>
                <button activity="list"><?=TS("Activities")?></button>
                <button logout="logout"><?=TS("Logout")?></button>
            </div>
            <iframe src id="activity-iframe" frameborder="0" height="700px"></iframe>
        </div>
    </div>
</div>
<script>
    $('#activity-iframe, #activity-left-pane').height($(window).height());
    $(window).resize(function() {
        $('#activity-iframe, #activity-left-pane').height($(window).height());
    });

    $('button[activity]').on('click', function() {
        $('button').removeClass('current-activity');
        $(this).addClass('current-activity');
        $('iframe').attr("src" , $(this).attr("activity") + '.php')
    });

    $('button[logout]').on('click', function() {
        $('button').removeClass('current-activity');
        $(this).addClass('current-activity');
        window.location = $(this).attr("logout") + '.php';
    });

</script>


</body>
</html>