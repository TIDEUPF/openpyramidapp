<style>

    button.ui-btn,
    .ui-page-theme-a {
        color: #696969 !important;
    }

    body > .ui-page, #answer-frame {
        height: 100%;
    }

    #answer-middle-frame,
    #answer-footer-frame > div{
        padding: 0 1em 0 1em;
    }

    #answer-header-frame > #topbar {
        background-color: #DFE5EC;
    }

    #answer-header-frame > div {
        padding: 0.5em 1em 0.25em 1em;
    }

    #answer-middle-frame {
        height: 60%;
        overflow: hidden;
    }

    #answer-header-frame,
    #answer-footer-frame {
        height: 20%;
        overflow: hidden;
    }

    #answer-header-level {
        float: right;
    }

    #answer-header-group {
        float: left;
    }

    #answer-textarea > textarea {
        height: auto !important;
    }

    #answer-next-button {
        margin-top: 1.5em;
    }

    #answer-header-text {
        font-size: 1.35em;
        margin-top: 0.5em;
    }

    #answer-header-level {
        float: left;
        text-align: center;
    }

    #answer-header-user {
        float: left;
    }

    #answer-header-logout {
        float: left;
        text-align: right;
    }

    .topbar_item {
        width: 33%;
    }

    .answer-rating-widget {
        margin-top: 1.0em;
    }

    .answer-text {
        font-size: 1.15em;
    }
</style>
<div id="answer-frame">
    <form method="post" action="student.php">
        <div id="answer-header-frame">

            <div id="topbar">
                <div id="answer-header-user" class="topbar_item"><?=$username?></div>
                <div id="answer-header-level" class="topbar_item"><?=$level?></div>
                <div id="answer-header-logout" class="topbar_item">Logout</div>
                <div style="clear:both"></div>
            </div>
            <div>
                <div id="answer-header-text"><?=$header_text?></div>
            </div>

        </div>
        <div id="answer-middle-frame">
            <?php foreach($final_answer_array as $i=> $answer_text):?>
                <div class="answer-rating-widget">
                    <fieldset data-role="controlgroup" data-type="horizontal">
                        <legend><?=$answer_text?></legend>
                    </fieldset>
                </div>
            <?php endforeach;?>

        </div>

        <div id="answer-footer-frame">

            <div>
                <div id="answer-waiting-group"><?=$answer_waiting_message?></div>
            </div>

        </div>

        <?php foreach($hidden_input_array as $hidden_input_name => $hidden_input_value):?>
            <input type="hidden" name="<?=$hidden_input_name?>" value="<?=$hidden_input_value?>">
        <?php endforeach?>

    </form>
</div>
<script>
    $('#answer-header-logout').on('touchstart', function(e) {
        window.location="logout.php";
    });
</script>