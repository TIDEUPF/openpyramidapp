<!DOCTYPE html>
<html>
    <head>
        <title><?= $title ?></title>
        <?php if (strlen(strstr($_SERVER['HTTP_USER_AGENT'], 'iPhone')) > 0 or strlen(strstr($_SERVER['HTTP_USER_AGENT'], 'iPad')) > 0 or strlen(strstr($_SERVER['HTTP_USER_AGENT'], 'iPod')) > 0):?>
        <meta name="viewport" content="initial-scale=1.0, user-scalable=0, width=device-width, height=device-height"/>
        <?php else:?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php endif;?>

        <link rel="stylesheet" href="vendors/jquerymobile/jquery.mobile-1.4.5.css"/>
        <script src="vendors/jquery/jquery-2.1.4.min.js"></script>
        <script src="vendors/jquerymobile/jquery.mobile.custom.js"></script>
        <link rel="stylesheet" href="vendors/jquery-bar-rating/themes/font-awesome.min.css">
        <link rel="stylesheet" href="vendors/jquery-bar-rating/themes/fontawesome-stars.css">
        <script src="vendors/jquery-bar-rating/jquery.barrating.min.js"></script>
        <script src="/<?=$node_path?>/socket.io.js"></script>
        <script src="lib/actions.js"></script>
        <script>
            <?php if(!isset($nosocket)):?>
            var socket = io({multiplex : false, 'reconnection': true,'reconnectionDelay': 3000,'maxReconnectionAttempts':Infinity, path: '/<?=$node_path?>/'});
            <?php endif;?>
            var timeoutPeriod = 10000;
            function del_vali(){ if(confirm('Submit Answer?')) { return true; }else{ return false; } }
            function del_vali2(){ if(confirm('Rate Answer?')) { return true; }else{ return false; } }
            function refreshp(){ window.location.href = window.location.href; }
        </script>

    </head>

<body>
<?= $body ?>
<input type="hidden" name="user_id" value="<?=$_SESSION['user_id']?>" />
<?php if(strlen(strstr($_SERVER['HTTP_HOST'], 'sos.gti.upf.edu')) > 0):?>
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-74070171-1', 'auto');
    ga('send', 'pageview');

</script>
<?php endif;?>
</body>

</html>