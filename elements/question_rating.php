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
        /*display: none;*/
    }

    #question-header-text {
        font-size: 1.35em;
    }

    .question-text {
        font-size: 1.15em;
    }
</style>
<div id="question-frame">
    <form method="post">
    <div id="question-header-frame">

        <div id="topbar">
            <div id="question-header-group"><?=$username?></div>
            <div id="question-header-level"><?=$level?></div>
            <div style="clear:both"></div>

        </div>
        <div>
            <div id="question-header-text"><?=$header_text?></div>
        </div>

    </div>
    <div id="question-middle-frame">
        <?php foreach($question_text_array as $i=> $question_text):?>
        <div>
            <fieldset data-role="controlgroup" data-type="horizontal">
                <legend><?=$question_text?></legend>
                <input type="radio" name="answer-rating-<?=$i?>" id="id-answer-rating-<?=$i?>-1" value="1">
                <label for="id-answer-rating-<?=$i?>-1">1</label>
                <input type="radio" name="answer-rating-<?=$i?>" id="id-answer-rating-<?=$i?>-2" value="2">
                <label for="id-answer-rating-<?=$i?>-2">2</label>
                <input type="radio" name="answer-rating-<?=$i?>" id="id-answer-rating-<?=$i?>-3" value="3">
                <label for="id-answer-rating-<?=$i?>-3">3</label>
                <input type="radio" name="answer-rating-<?=$i?>" id="id-answer-rating-<?=$i?>-4" value="4">
                <label for="id-answer-rating-<?=$i?>-4">4</label>
                <input type="radio" name="answer-rating-<?=$i?>" id="id-answer-rating-<?=$i?>-5" value="5">
                <label for="id-answer-rating-<?=$i?>-5">5</label>
            </fieldset>
        </div>
        <?php endforeach;?>

    </div>

    <div id="question-footer-frame">

        <div>
            <div id="question-waiting-group"><?=$question_waiting_message?></div>
            <div id="question-next-button"><button class="ui-btn"><?= $question_rate_submit?></button></div>
        </div>

    </div>
    </form>
</div>
<script>

</script>