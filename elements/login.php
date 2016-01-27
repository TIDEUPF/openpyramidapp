<style>
    button.ui-btn {
        width: 378px !important;
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
        width: 500px;
        margin-left: auto;
        margin-right: auto;
        padding-top: 200px;
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

</style>
<div id="activity-frame">
    <form action="student_login.php" method="post" data-ajax="false">

        <div id="activity-middle-frame">
            <h2>Student Login</h2>
            <div>
                <label for="usr"><b>UserId</b></label>
                <input type="text" data-clear-btn="true" name="usr" value="">
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

        </div>
        <?php foreach($hidden_input_array as $hidden_input_name => $hidden_input_value):?>
        <input type="hidden" name="<?=$hidden_input_name?>" value="<?=$hidden_input_value?>">
        <?php endforeach?>
    </form>
</div>