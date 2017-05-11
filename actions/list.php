<?php
session_start();
include('dbvar.php');

if(isset($_SESSION['user'])) {
    $teacher_id = $_SESSION['user'];
}


//obtain the flows pertaining to the current teacher
$flows = [];
$teacher_id = $_SESSION['user'];
$flow_query = mysqli_query($link, "select * from flow where teacher_id = '$teacher_id'");
if(mysqli_num_rows($flow_query) > 0) {
    while ($flow_query_row = mysqli_fetch_assoc($flow_query)) {
        $flow['id'] = $flow_query_row["fid"];
        $flow['name'] = $flow_query_row["fname"];
        $flows[] = $flow;
    }
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

    <?php foreach($flows as $flow):?>
    <div class="activity-list-item">
        <div class="activity-list-entry-text"><a data-ajax="false" href="teacher.php?edit=<?=$flow['id']?>"><?=TS("Edit")?></a></div>
        <div class="activity-list-entry-text"><a data-ajax="false" href="activities.php?edit=<?=$flow['id']?>"><?=TS("Activity")?></a></div>
        <div class="activity-list-entry-text activity-list-entry-text-name"><?=$flow['name']?></div>
        <div style="clear:both;"></div>
    </div>
    <?php endforeach;?>

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