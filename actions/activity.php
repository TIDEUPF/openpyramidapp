<?php
session_start();
include('dbvar.php');
global $node_path;

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

    <script src="https://cdn.socket.io/socket.io-1.3.7.js"></script>
    <script src="lib/actions.js"></script>
    <script type="text/javascript">
        var socket = io({multiplex : false, 'reconnection': true,'reconnectionDelay': 3000,'maxReconnectionAttempts':Infinity, path: '/<?=$node_path?>/'});
    </script>

</head>
<body style="width:1050px;overflow-x: auto;margin: 0 auto 0 auto;">
<input name="page" type="hidden" value="teacher_main_menu"/>
<input name="username" type="hidden" value="<?=htmlspecialchars($teacher_id)?>"/>
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
            <div style="clear:both"></div>
        </div>
    </div>
</div>
<script>
    $('#activity-left-pane').height($(window).height()-1);
    $('#activity-iframe').attr("height", Math.floor($(window).height()/2) * 2);
    $(window).resize(function() {
        $('#activity-left-pane').height($(window).height()-1);
        $('#activity-iframe').attr("height", Math.floor($(window).height()/2) * 2);
    });

    $('button[activity]').on('click', function() {
        $('button').removeClass('current-activity');
        $(this).addClass('current-activity');
        $('iframe').attr("src" , $(this).attr("activity") + '.php');

        $('#activity-iframe').remove();
        $('#activity-left-pane').after('<iframe id="activity-iframe" src="' + $(this).attr("activity") + '.php' + '" frameborder="0" height="' + (Math.floor($(window).height()/2) * 2) + '"></iframe>');
    });

    $('button[logout]').on('click', function() {
        $('button').removeClass('current-activity');
        $(this).addClass('current-activity');
        window.location = $(this).attr("logout") + '.php';
    });

    /*
    $(window).on('popstate', function (e) {
        return false;
        var state = e.originalEvent.state;
        if (state !== null) {
            //load content with ajax
            return false;
        }
    });
    */
    history.pushState(null, null, document.title);
    window.addEventListener('popstate', function () {
        history.pushState(null, null, document.title);
    });

    $('[activity="teacher"]').click();

</script>


</body>
</html>