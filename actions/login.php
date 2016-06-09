<?php
session_start();
include('dbvar.php');

if(!isset($_SESSION['user'])){
    if(isset($_POST['loginBtn'])) {
        if(empty($_POST['usr']) || empty($_POST['pwd'])) {
            $error = "Username and Password can not be empty!";
        }
        else{
            $uname = mysqli_real_escape_string($link, stripslashes(trim(strip_tags($_POST['usr']))));
            $pass =  mysqli_real_escape_string($link, stripslashes(strip_tags(trim($_POST['pwd']))));

            $count_login = mysqli_num_rows(mysqli_query($link, "select uname from teacher where uname = '$uname' and pass = '$pass' limit 1 "));
            if($count_login > 0){
                //$_SESSION['token'] = $token;
                $_SESSION['user'] = $uname;
                header("location: teacher.php");
                exit(0);
            }
            else{
                $error = 'Username or password incorrect';
            }
        }
    }
}
else{
    header("location: teacher.php");
    exit(0);
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <title>LoginPage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
    <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>

    <style>
        form {
            width: 500px;
            margin: 0 auto 0 auto
        }
    </style>
</head>
<body>

<div data-role="page">
    <div data-role="header">
        <h1>PyramidApp - Educator View</h1>
    </div>

    <div data-role="main" class="ui-content">
        <div>
            <div class="login-pyramid-block">
                <img src="elements/resources/pyramid-logo.png" alt="pyramid_icon" height="150" width="200">
                <div class="login-pyramid-block-subtitle">
                    <span><?=TS('What is a "pyramid"?')?> </span><a href="#what" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="<?=TS('What is a "pyramid?"?')?>"><?=TS('What is a "pyramid"?')?></a>
                    <div data-role="popup" id="what" class="ui-content" data-theme="a" style="max-width:350px;">
                        <p><?=TS('A pyramid is structured in a way that students start studying given task or problem individually and proposing an initial solution. Then, individuals team up (usually pairs) to compare and discuss their proposals and, finally, proposes a new shared solution. New larger groups are formed growingly (by iteratively joining previous groups) in order to generate new agreed proposals until a global consensus is achieved. A Pyramid scenario fosters individual participation and accountability (all have the opportunity and need to express a contribution) and balanced positive inter-actions (opinions of all members count) in a collaborative knowledge-oriented negotiation process.')?></p>
                    </div>
                </div>

            </div>
            <br>
            <p>Educators can create pyramid collaborative learning activities here and view status of an ongoing pyramid activity.
                When you login, you will be shown a window to design pyramid flows (activities) by specifying your design requirements
                and parameter values. Some parameters are provided with default values to make the design process faster and easier.</p>
            <br> <br><br>
        </div>


        <form method="post" action="" data-ajax="false">
            <div class="ui-field-contain">
                <label for="uid">User ID:</label>
                <input type="text" name="usr" id="usr">
                <label for="password">Password:</label>
                <input type="password" name="pwd" id="pwd">
            </div>
            <input type="submit" data-inline="true" value="Login" name="loginBtn">
        </form>
    </div>
</div>

</body>
</html>