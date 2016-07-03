<style>
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
        margin-left: auto;
        margin-right: auto;
        padding-top: 0.5em;
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

    .bold {
        font-weight: bold;
    }

    iframe {
        display: block;
        margin: 0px auto;
    }

    #email-petition {
        font-size: 90%;
        text-align: justify;
        margin-bottom: 0.5em;
    }

    #gti-logo img {
        height: 60px;
    }

    #gti-logo {
        display: table;
        margin: 10px auto;
        padding: 0px !important;
    }
</style>
<div id="activity-frame">
    <form action="student_login.php" method="post" data-ajax="false">

        <div id="activity-middle-frame">
            <h2>Pyramid Interaction App</h2>
            <div>
                <!--<label for="usr"><b>UserId</b></label>-->
                <input type="text" data-clear-btn="true" name="usr" value="" placeholder="email">
            </div>

        </div>

        <div id="activity-footer-frame">

            <div>
                <div>
                    <button type="submit" class="ui-btn" name="loginBtn">Log in</button>
                </div>
            </div>

            <?php if(isset($error)):?>
            <div>
                <div id="student-login-error"><?=$error?></div>
            </div>
            <?php endif;?>

            <div id="email-petition"><?php echo htmlspecialchars("It is recommended to provide an "). '<span class="bold">email address</span>' . htmlspecialchars(" here so that you will be automatically notified about the activity progression to make it much easier. This email address does not necessarily have to be the same as your FutureLearn course email.")?> <a href="mailto:ssp.clfp@upf.edu?Subject=Pyramid%20App">Contact address: ssp.clfp@upf.edu</a></div>
            <div id="email-petition"><a href="mailto:ssp.clfp@upf.edu?Subject=Pyramid%20App"<?=TS("Contact address")?>: ssp.clfp@upf.edu</a></div>
            <iframe width="280" height="158" src="https://www.youtube.com/embed/-l-pIwflqrc?rel=0" frameborder="0" allowfullscreen></iframe>

            <div id="gti-logo"><a target="_blank" href="http://gti.upf.edu"><img src="elements/resources/logo_gti_2013_big_eng.png" /></a></div>
        </div>
        <?php foreach($hidden_input_array as $hidden_input_name => $hidden_input_value):?>
        <input type="hidden" name="<?=$hidden_input_name?>" value="<?=$hidden_input_value?>">
        <?php endforeach?>
    </form>
</div>