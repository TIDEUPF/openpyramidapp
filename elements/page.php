<!DOCTYPE html>
<html>
    <head>
        <title><?= $title ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="vendors/jquerymobile/jquery.mobile-1.4.5.css"/>
        <script src="vendors/jquery/jquery-2.1.4.min.js"></script>
        <script src="vendors/jquerymobile/jquery.mobile.custom.js"></script>
        <link rel="stylesheet" href="vendors/jquery-bar-rating/themes/font-awesome.min.css">
        <link rel="stylesheet" href="vendors/jquery-bar-rating/themes/fontawesome-stars.css">
        <script src="vendors/jquery-bar-rating/jquery.barrating.min.js"></script>
        <script src="https://cdn.socket.io/socket.io-1.4.4.js"></script>
        <script src="lib/actions.js"></script>
        <script>
            <?php if(!isset($nosocket)):?>
            var socket = io({multiplex : false, 'reconnection': true,'reconnectionDelay': 9000,'maxReconnectionAttempts':Infinity});
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
</body>

</html>