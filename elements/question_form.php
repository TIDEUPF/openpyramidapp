<style>

    button.ui-btn,
    .ui-page-theme-a {
        color: #696969 !important;
    }

    body > .ui-page, #question-frame {
        height: 100%;
    }

    #question-middle-frame,
    #question-footer-frame > div{
        padding: 0 1em 0 1em;
    }

    #question-header-frame > div {
        padding: 0.5em 1em 0.25em 1em;
        background-color: #DFE5EC;
    }

    #question-middle-frame {
        height: 60%;
        overflow: hidden;
    }

    #question-header-frame,
    #question-footer-frame {
        height: 20%;
        overflow: hidden;
    }

    #question-header-level {
        float: left;
        text-align: center;
    }

    #question-header-user {
        float: left;
    }

    #question-header-logout {
        float: left;
        text-align: right;
    }

    .topbar_item {
        width: 33%;
    }

    #question-textarea > textarea {
        height: auto !important;
    }

    #student-error {
        margin-top: 1em;
        text-align: center;
        color: red;
        font-size: 130%;
        font-weight: bold;
    }

</style>
<div id="question-frame">
    <form method="post">
    <div id="question-header-frame">

        <div>
            <div id="question-header-user" class="topbar_item"><?=$username?></div>
            <div id="question-header-level" class="topbar_item"><?=$level?></div>
            <div id="question-header-logout" class="topbar_item">Logout</div>
            <div style="clear:both"></div>
        </div>

    </div>
    <div id="question-middle-frame">

        <div>

            <div><h2><?= $question_text?></h2></div>
            <div id="question-textarea">
                <textarea name="qa" rows="10"></textarea>
            </div>

            <?php if(isset($error)):?>
                <div>
                    <div id="student-error"><?=$error?></div>
                </div>
            <?php endif;?>

        </div>

    </div>

    <div id="question-footer-frame">

        <div>
            <div id="question-submit-button"><button type="submit" name="answer" class="ui-btn"><?=$question_submit_button?></button></div>
            <div id="question-submitted-message"></div>
            <div id="question-rating-ready"></div>
        </div>

    </div>

    <?php foreach($hidden_input_array as $hidden_input_name => $hidden_input_value):?>
    <input type="hidden" name="<?=$hidden_input_name?>" value="<?=$hidden_input_value?>">
    <?php endforeach?>
    </form>
</div>
<script>
    $('#question-header-logout').on('touchstart', function(e) {
        window.location="logout.php";
    });
</script>