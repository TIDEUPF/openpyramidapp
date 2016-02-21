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

    #answer-header-text,
    #answer-header-text-other {
        font-size: 1.35em;
        margin-top: 1.0em;
        margin-bottom: 0.25em;
        margin-left: 1em;
    }

    #answer-header-level {
        float: left;
        text-align: center;
        width: 20%;
    }

    #answer-header-user {
        float: left;
        width: 40%;
    }

    #answer-header-logout {
        float: right;
        text-align: right;
        width: 20%;
        cursor: pointer;
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

    @font-face{
        font-family: 'Glyphicons Halflings';
        src: url('vendors/fonts/glyphicons-halflings-regular.eot');
        src: url('vendors/fonts/glyphicons-halflings-regular.eot') format('embedded-opentype'),
        url('vendors/fonts/glyphicons-halflings-regular.woff2') format('woff2'),
        url('vendors/fonts/glyphicons-halflings-regular.woff') format('woff'),
        url('vendors/fonts/glyphicons-halflings-regular.ttf') format('truetype'),
        url('vendors/fonts/glyphicons-halflings-regular.svg') format('svg');
    }

    ul {
        font-size: 120%;
        list-style: none;
    }
    li:before {
        font-family: 'Glyphicons Halflings';
        content:"\e084";
        position: relative;
        top: 3px;
        margin-right: 5px;
    }

    #answer-middle-frame {
        width: 80%;
        margin: 0px auto;
    }

    #feedback-form {
        display: table;
        font-size: 160%;
        margin: 0.7em auto;
    }

    .winner-answers {
        margin-top: 0.5em;
        margin-bottom: 0.5em;
    }

    #pre-feedback-form {
        margin-top: 0.5em;
        text-align: center;
        color: red;
        font-size: 120%;
    }

    #feedback-form span {
        text-decoration: underline;
        cursor: pointer;
        color: #547cff;
        font-weight: bolder;
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

        </div>

        <div id="answer-middle-frame">
            <div>
                <div id="answer-header-text"><?=$header_text?></div>
            </div>

            <ul class="winner-answers">
            <?php foreach($final_answer_array as $i=>$answer_text):?>
                <li><?=htmlspecialchars($answer_text, ENT_COMPAT | ENT_HTML401 | ENT_IGNORE)?></li>
            <?php endforeach;?>
            </ul>

            <?php if(!empty($other_answer_array)): ?>
            <div id="answer-header-text-other"><?=$other_header_text?></div>
            <ul class="winner-answers">
            <?php foreach($other_answer_array as $i=>$answer_text):?>
                <li><?=htmlspecialchars($answer_text, ENT_COMPAT | ENT_HTML401 | ENT_IGNORE)?></li>
            <?php endforeach;?>
            </ul>
            <?php endif; ?>

        </div>

        <div id="answer-footer-frame">
            <?php if(empty($no_feedback)): ?>
                <div id="pre-feedback-form">Please make sure to submit your feedback by clicking the below link!</div>
            <div id="feedback-form"><span goto="<?=htmlspecialchars("https://docs.google.com/forms/d/1xNvjBcpp4vsS1J-jxTfbO21gqLDQwZ4Vjc1XDnb_6Dw/viewform")?>">Feedback form</span></div>
            <!--<div id="feedback-form"><a target="_blank" href="https://google.com">Feedback form</a></div>-->
            <?php endif; ?>
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
    var cancel_timeout;
    $('#answer-header-logout').on('touchstart', function(e) {
        window.location="logout.php";
    });

    $('#answer-header-logout').on('click', function(e) {
        window.location="logout.php";
    });

    /*
    $('#feedback-form').mousedown(function(event) {
        if(event.which == 3 || event.which == 1 || event.which == 2) { // right click
            clearTimeout(cancel_timeout);
            window.open($('#feedback-form a').attr('href'),'_newtab');
            feedback_clicked();
            $(this).hide();
            cancel_timeout = setTimeout("refreshp();",timeoutPeriod);
        }
    });

    var feedback_clicked = function () {
        $.ajax({
            url: 'feedback_clicked.php',
            method: 'post',
            dataType: 'json',
            timeout: 20000,
        });
    }
    */

    $('#feedback-form span').on('contextmenu', function() {
        return false;
    });

    $('#feedback-form span').mousedown(function(event) {
        if(event.buttons == 3 || event.buttons == 1 || event.buttons == 2 || event.buttons == 4) {
            event.preventDefault();
            clearTimeout(cancel_timeout);
            cancel_timeout = setTimeout("refreshp();",timeoutPeriod);
            feedback_clicked();
        }
    });

    function gotoform() {
        var form_url = $('#feedback-form span').attr('goto');
        $('#feedback-form, #pre-feedback-form').hide();
        window.location.href = form_url;
    }

    var feedback_clicked = function () {
        $.ajax({
            url: 'feedback_clicked.php',
            method: 'post',
            dataType: 'json',
            success: gotoform,
            timeout: 20000
        });
    }

    timeoutPeriod = 20000;
    cancel_timeout = setTimeout("refreshp();",timeoutPeriod);

    function refreshp() {
        window.location.href = window.location.href;
    }

</script>