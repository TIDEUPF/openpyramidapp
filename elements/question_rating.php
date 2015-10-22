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

    #question-header-frame > #topbar {
        background-color: #DFE5EC;
    }

    #question-header-frame > div {
        padding: 0.5em 1em 0.25em 1em;
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

    #question-next-button {
        display: none;
    }

    #question-header-text {
        font-size: 1.35em;
    }

    .question-text {
        font-size: 1.15em;
    }
</style>
<div id="question-frame">
    <div id="question-header-frame">

        <div id="topbar">
            <div id="question-header-group"><?=$group?></div>
            <div id="question-header-level"><?=$level?></div>
            <div style="clear:both"></div>

        </div>
        <div>
            <div id="question-header-text"><?=$header_text?></div>
        </div>

    </div>
    <div id="question-middle-frame">

        <?php foreach($question_text_array as $k=> $question_text):?>
        <div>
            <div id="question-text-<?=$k?>" class="question-text"><?=$question_text?></div>
        </div>
        <?php endforeach;?>

    </div>

    <div id="question-footer-frame">

        <div>
            <div id="question-waiting-group"><?=$question_waiting_message?></div>
            <div id="question-next-button"><button class="ui-btn"><?= $question_submit_button?></button></div>
        </div>

    </div>
</div>
<script>

</script>