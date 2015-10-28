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
        float: right;
    }

    #question-header-group {
        float: left;
    }

    #question-textarea > textarea {
        height: auto !important;
    }

</style>
<div id="question-frame">
    <form method="post">
    <div id="question-header-frame">

        <div>
            <div id="question-header-group"><?=$group?></div>
            <div id="question-header-level"><?=$level?></div>
            <div style="clear:both"></div>
        </div>

    </div>
    <div id="question-middle-frame">

        <div>

            <div><?= $question_text?></div>
            <div id="question-textarea">
                <textarea name="qa" rows="10"></textarea>
            </div>

        </div>

    </div>

    <div id="question-footer-frame">

        <div>
            <div id="question-submit-button"><button class="ui-btn"><?=$question_submit_button?></button></div>
            <div id="question-submitted-message"></div>
            <div id="question-rating-ready"></div>
        </div>

    </div>
    </div>
    <?php foreach($hidden_input_array as $hidden_input_name => $hidden_input_value):?>
        <input type="hidden" name="<?=$hidden_input_name?>" value="<?=$hidden_input_value?>">
    <?php endforeach?>
    </form>
</div>
<script>

</script>