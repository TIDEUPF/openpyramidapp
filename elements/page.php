<!DOCTYPE html>
<html>
    <head>
        <title><?= $title ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="vendors/jquerymobile/jquery.mobile-1.4.5.css"/>
        <script src="vendors/jquery/jquery-2.1.4.min.js"></script>
        <script src="vendors/jquerymobile/jquery.mobile.custom.js"></script>

        <script>
            var timeoutPeriod = 10000;
            function del_vali(){ if(confirm('Submit Answer?')) { return true; }else{ return false; } }
            function del_vali2(){ if(confirm('Rate Answer?')) { return true; }else{ return false; } }
            function refreshp(){ window.location.href = window.location.href; }
        </script>

    </head>

<body>
<?= $body ?>
</body>

</html>