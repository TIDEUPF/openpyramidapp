<style>
    button.ui-btn {
        width: 100px !important;
        padding: 7px;
        border-radius: 4px;
        display: table-cell;
        vertical-align: middle;
    }

    .ui-input-text {
        width: 338px !important;
    }

    button.ui-btn,
    .ui-page-theme-a {
        color: #696969 !important;
    }

    body > .ui-page, #activity-frame {
        height: 100%;
    }

    #activity-middle-frame,
    #activity-footer-frame > div{
        padding: 0 1em 0 1em;
    }

    #activity-header-frame > div {
        padding: 0.5em 1em 0.25em 1em;
        background-color: #DFE5EC;
    }

    #activity-middle-frame {
        height: 60%;
        overflow: hidden;
    }

    form {
        width: 900px;
        margin-left: auto;
        margin-right: auto;
        padding-top: 30px;
    }

    #activity-header-frame,
    #activity-footer-frame {
        height: 20%;
        overflow: hidden;
    }

    #activity-header-level {
        float: right;
    }

    #activity-header-group {
        float: left;
    }

    #activity-textarea > textarea {
        height: auto !important;
    }

    #activity-next-button {
        display: none;
    }

    #student-login-error {
        margin-top: 1em;
        text-align: center;
        color: red;
        font-size: 130%;
        font-weight: bold;
    }

    #title {
        font-size: 200%;
        text-align: center;
        color: #6F6F6F;
    }

    #post-title {
        margin-top: 2em;
        font-size: 110%;
        text-align: justify;
    }

    #usr-input {
        display: table-cell;
        margin-right: 30px;
        vertical-align: middle;
        padding-right: 30px;
        padding-left: 30px;
    }

    label[for="usr"] {
        display: table-cell;
        font-size: 130%;
        margin-right: 30px;
        vertical-align: middle;
    }

    #login-block {
        display: inline-table;
    }

    #login-block-wrapper {
        margin-top: 30px;
        text-align: center;
    }

    #login-instructions {
        margin-top: 25px;
        margin-right: 40px;
    }

    #login-instructions li {
        font-size: 110%;
        text-align: justify;
        margin-bottom: 10px;
        color: #948484;
    }

    #video {
        margin: 40px auto 0px auto;
        width: 560px;
    }

    .bold {
        font-weight: bold;
        color: #636363;
    }

    #gti-logo img {
        height: 50px;
        background-color: white;
        padding: 1px;
        border-radius: 4px;
    }

    #gti-logo {
        text-align: center;
        margin-top: 35px;
    }

    <?php if (strlen(strstr($_SERVER['HTTP_USER_AGENT'], 'Gecko')) > 0):?>
    <?php endif;?>
</style>
<div id="activity-frame">
    <form action="student_login.php" method="post" data-ajax="false">
<div id="title">Pyramid Interaction App</div>
<div id="post-title"><?php echo htmlspecialchars("This is a scalable pedagogical approach that allows proposing individual questions/options which are added into groups enabling discussion and iteratively rating, following a snowball or pyramid structure to agree upon the most interesting question/opinion promoting fruitful collaborations.")?></div>

        <div id="activity-middle-frame">
            <div id="login-block-wrapper">
                <div id="login-block">
                    <label for="usr"><b>UserId</b></label>
                    <div id="usr-input"><input type="text" data-clear-btn="true" name="usr" value=""></div>
                    <button type="submit" class="ui-btn" name="loginBtn">Log in</button>
                </div>
            </div>

            <?php if(isset($error)):?>
                <div>
                    <div id="student-login-error"><?=$error?></div>
                </div>
            <?php endif;?>

            <ul id="login-instructions">
                <li><?php echo htmlspecialchars("It is recommended to provide an "). '<span class="bold">email address</span>' . htmlspecialchars(" here so that you will be automatically notified about the activity progression to make it much easier. This email address does not necessarily have to be the same as your FutureLearn course email.")?></li>
                <li><?php echo htmlspecialchars("If you do not like to provide an email, you can use a preferred username.")?></li>
                <li><?php echo htmlspecialchars("You can see activity notifications from the step comments also.")?></li>
                <li><?php echo htmlspecialchars("Please do remember the e-mail or username you're using for this app! You will need to use it when accessing to participate in other levels of the pyramid. If you use another username you will be added with a different pyramid and affect your activity progress.")?></li>
                <li><?php echo htmlspecialchars("For more information on how the app works, you can watch the following video. ")?></li>
            </ul>

            <div id="video">
                <iframe width="560" height="315" src="https://www.youtube.com/embed/-l-pIwflqrc?rel=0" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>

        <div id="activity-footer-frame">
            <div id="gti-logo"><a target="_blank" href="http://gti.upf.edu"><img src="elements/resources/logo_gti_2013_big_eng.png" /></a></div>
        </div>
        <?php foreach($hidden_input_array as $hidden_input_name => $hidden_input_value):?>
        <input type="hidden" name="<?=$hidden_input_name?>" value="<?=$hidden_input_value?>">
        <?php endforeach?>
    </form>
</div>