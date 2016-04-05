<style>
    button.ui-btn {
        width: 300px !important;
        margin: auto !important;
    }

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
        box-sizing: border-box;
        height: 60%;
        width: 45%;
        float: left;
        overflow: hidden;
    }

    #rating-chat {
        height: 60%;
        width: 55%;
        float: right;
        overflow: hidden;
        background-color: white;
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
        width: 20%;
    }

    #answer-header-user {
        float: left;
        width: 40%;
    }

    #answer-header-logout {
        float: right;
        text-align: right;
        width: 10%;
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

    #student-error {
        margin-top: 1em;
        text-align: center;
        color: red;
        font-size: 130%;
        font-weight: bold;
    }

    a[data-rating-value="0"] {
        display: none !important;
    }

    #countdown {
        /*display: none;*/
        position: relative;
        bottom: 0px;
        left: 0px;
        height: 30px;
        right: 0px;

        text-align: center;

        /*background-color: #000000;*/
        padding-top: 0px;
        color: white;
        text-shadow: 0 /*{a-page-shadow-x}*/ 1px /*{a-page-shadow-y}*/ 0 /*{a-page-shadow-radius}*/ #000000 /*{a-page-shadow-color}*/;
        font-size: 120%;
        /*transition-property: all;*/
        /*transition-duration: 1s;*/
        /*z-index: 1000;*/
    }

    #countdown-padding {
        height: 0em;
        /*transition-property: all;*/
        /*transition-duration: 1s;*/
        /*display: none;*/
    }

    .question-number {
        border: 1px solid black;
        background-color: black;
        padding: 4px;
        border-radius: 4px;
        margin-right: 4px;
        text-shadow: none;
        color: #E4E4E4;
        font-weight: bold;
        float: left;
    }

    #submit-confirmation {
        display: none;
        position: fixed;
        width: 200px;
        top: 50%;
        left: 50%;
        z-index: 1000;
        padding: 30px;
        border: 2px solid grey;
        border-radius: 4px;
        background-color: #D3D3D3;
        margin-left: -132px;
        margin-top: -32px;
    }

    #submit-confirmation button {
        margin-bottom: 20px !important;
        display: block;
        width: 100px !important;
    }

    #confirm-text {
        display: block;
        text-align: center;
        font-size: 110%;
        margin-bottom: 20px;
        margin-top: -10px;
    }

    #chat-write {
        display: block;
        position:relative;
        outline: 0;
        top: 37px;
        left: 383px;
        width: 0px;
        height: 0px;
        z-index: 1001;
        border: 0px;
        background-color: rgba(0,0,0,0.0);
    }

    #chat-write::after {
        content: "";
        position: relative;
        display: block;
        width: 22px;
        height: 22px;

        cursor: pointer;

        background-position: center center;
        background-repeat: no-repeat;

        background-color: rgba(0,0,0,.3) /*{global-icon-disc};;;;;*/;
        background-position: center center;
        background-repeat: no-repeat;
        -webkit-border-radius: 1em;
        border-radius: 1em;
    }

    /*
    #chat-write {
        display: inline-block;
        border: none;
        background: none;
        margin-left: -55px;
        z-index: 1000;
        position: relative;
        margin-bottom: -5px;
        box-shadow: none;
        webkit-box-shadow: none;
    }
    */

    li.chat.page div.ui-input-text {
        display: inline-block;
        /*margin: 10px 0 10px 10px !important;*/
        /*margin: 0 !important;*/
    }

    .ui-input-text {
        width: 400px !important;
        /*margin: auto !important;*/
    }

    #async_rated {
         background-color: #ACF97B;
         text-align: center;
     }

    form {
        min-width: 710px;
    }

    html {
        overflow-x: auto;
    }
</style>

<link rel="stylesheet" href="vendors/perfect-scrollbar/css/perfect-scrollbar.min.css">
<script src="vendors/perfect-scrollbar/js/min/perfect-scrollbar.jquery.min.js"></script>

<div id="answer-frame">
    <form method="post" action="student.php" data-ajax="false">
        <div id="submit-confirmation">
            <span id="confirm-text">><?=TS("Are you sure that you want to finish rating and discussion for this level?")?><</span>
            <button id="yes-submit" type="submit" name="rate"><?=TS("Yes")?></button>
            <button id="no-submit"><?=TS("No")?></button>
        </div>

    <div id="answer-header-frame">

        <div id="topbar">
            <div id="answer-header-user" class="topbar_item"><?=$username?></div>
            <div id="answer-header-level" class="topbar_item"><?=$level?></div>
            <div id="answer-header-logout" class="topbar_item"><?=TS("Logout")?></div>
            <div style="clear:both"></div>
        </div>
        <?php if(isset($async_rated)): ?>
            <div id="async_rated"><?=htmlspecialchars($async_rated)?></div>
        <?php endif; ?>
        <div>
        <!--<div id="answer-header-text"><?=$header_text?></div>-->
        <div id="answer-header-text"><?=TS("Rating is individual. Please rate all options!")?></div>
        </div>

    </div>

    <div id="answer-middle-frame">
        <?php foreach($answer_text_array as $i=> $answer_data):?>
        <div class="answer-rating-widget">
            <fieldset data-role="controlgroup" data-type="horizontal">
                <div>
                    <span class="question-number"><?=($i+1)?></span>
                    <?php
                    global $flow_data;

                    if($flow_data['no_submit'] == 0) {
                        ?>
                        <legend><?= htmlspecialchars($answer_data['answer_text'], ENT_COMPAT | ENT_HTML401 | ENT_IGNORE) ?></legend>
                        <?php
                    } else {
                        $link_data = explode('|', $answer_data['answer_text'], 2);
                        ?>
                        <legend><a target="_blank" href="<?=htmlspecialchars($link_data[1])?>"><?= htmlspecialchars($link_data[0]) ?></a></legend>
                        <?php
                    }
                        ?>
                </div>
                <div style="clear:both"></div>
                <select id="id-answer-rating-<?=($i+1)?>" class="rating-widget" name="optradio<?=($i+1)?>" data-role="none" answer_sid="<?=$hidden_input_array['to_whom_rated_id'.($i+1)]?>" answer_text="<?=htmlspecialchars($answer_data['answer_text'], ENT_COMPAT | ENT_HTML401 | ENT_IGNORE)?>">
                <?php for($i=0;$i<=5;$i++):?>
                <option value="<?=$i?>" <?php if($answer_data['selected'] == $i) echo 'selected="selected"';?>><?=$rating_labels[$i]?></option>
                <?endfor;?>
                </select>
            </fieldset>
        </div>
        <?php endforeach;?>

        <?php if(isset($error)):?>
            <div>
                <div id="student-error"><?=$error?></div>
            </div>
        <?php endif;?>

    </div>

    <?php if($flow_data['ch'] == 1):?>
        <style>

        </style>
    <link rel="stylesheet" href="chat/client/embedded.css"/>
    <div id="rating-chat" class="ui-corner-all ui-shadow-inset">
        <div style="margin: .5em 0 0 .3em;"><?=TS("Please use this space to discuss with peers about their options before rating.")?></div>
        <ul class="pages">
            <li class="chat page">
                <div class="chatArea">
                    <ul class="messages">
                        <?php foreach($messages as $message): ?>
                        <li class="message" style="display: list-item;"><span class="username" style="color: rgb(56, 36, 170);"><?=htmlspecialchars($message['sid'], ENT_COMPAT | ENT_HTML401 | ENT_IGNORE)?></span><span class="messageBody"><?=htmlspecialchars($message['message'], ENT_COMPAT | ENT_HTML401 | ENT_IGNORE)?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <a id="chat-write" href="#" class="ui-icon-edit"></a>
                <input type="text" class="inputMessage" placeholder="<?=TS("Discuss with with your peers!")?>"/>
            </li>
        </ul>
    </div>

    <?php endif;?>

    <div style="clear:both"></div>

    <script src="jslib/chatvars.js"></script>
    <script src="chat/client/chat.js"></script>
        <script>
            $('.messages').perfectScrollbar();
        </script>
    <div id="answer-footer-frame">

        <div>
            <div id="answer-waiting-group"><?=$answer_waiting_message?></div>
            <!--<div style="font-size: 1.35em; margin-top:20px;">Please note that when you submit rating, you will no longer be able to edit rating values. Also you will be removed from the discussion thread for this level.</div>-->
            <div style="font-size: 1.35em; margin-top:20px;"><?=TS("Submit rating here! But you still can continue discussion and modify rating accordingly.")?></div>

            <div id="answer-next-button"><button id="answer-next-button-ui" class="ui-btn"><?=$answer_rate_submit?></button></div>
        </div>

    </div>

    <?php foreach($hidden_input_array as $hidden_input_name => $hidden_input_value):?>
    <input type="hidden" name="<?=$hidden_input_name?>" value="<?=$hidden_input_value?>">
    <?php endforeach?>

    </form>
    <div id="countdown-padding"></div>
    <div id="countdown"><span id="countdown-text"></span></div>

</div>
<script>
    var polling_interval = 30;
    var time_left = 0;
    var countdown_started = false;
    var countdown_interval = null;
    var polling_interval_d = null;

    $("#answer-next-button button").on('click', function(e){
        e.preventDefault();
        $('#submit-confirmation').show();
    });

    $("#no-submit").on('click', function(e){
        e.preventDefault();
        $('#submit-confirmation').hide();
    });

    $('#answer-header-logout').on('touchstart', function(e) {
        window.location="logout.php";
    });

    $('#answer-header-logout').on('click', function(e) {
        window.location="logout.php";
    });

    var level_status_actions = function(data) {
        console.log(data);

        if(data.reset)
            newflow();

        if(data.expired)
            refreshp();

        if(data.countdown_started && data.time_left < 0)
            refreshp();

        if(data.countdown_started)
            show_countdown(data.time_left);
    }

    function show_countdown(time_left) {
        var countdown_height = 30;
        countdown_started = true;
        this.time_left = time_left - 5;
        if(!countdown_interval)
            countdown_interval = setInterval(update_countdown, 1*1000);
        update_countdown();

        var countdown_bottom = $('body').outerHeight()
            - $('#answer-next-button').position().top
            - $('#answer-next-button').outerHeight(true)
            - (countdown_height + 4);

        if(countdown_bottom < 0)
            countdown_bottom = 0;

        $('#countdown').css('bottom', -countdown_bottom+'px');

        $('#countdown')
            .show()
            .css('height', countdown_height+'px')
            .css('padding-top', '6px')
            .css('background-color', '#000');

        logTimerActivation();

        /*
        $('#countdown-padding')
            .show()
            .css('height', '2em');
            */
    }

    function update_countdown() {
        var text_left = '';
        countdown_started = true;
        time_left--;
        var minutes_left = Math.floor(time_left/60);
        if(minutes_left == 1)
            text_left = minutes_left + ' minute ';
        else if(minutes_left > 1)
            text_left = minutes_left + ' minutes ';
        text_left += time_left%60 + ' seconds left';
        $('#countdown-text').text(text_left);
        if(time_left <= 0) {
            clearInterval(countdown_interval);
            countdown_finished();
        }
    }

    var countdown_finished = function() {
        if(polling_interval_d)
            clearInterval(polling_interval_d);

        $('button').prop('disabled', true);
        setTimeout(function () {
            window.location.href = window.location.href;
        }, 10*1000);

        $('#countdown').text('time is up');
    }

    var poll_level_status = function () {
        $.ajax({
            url: 'level_environment.php',
            method: 'post',
            dataType: 'json',
            success: level_status_actions,
            timeout: polling_interval*1000,
            data: {
                level: $('[name="level"]').val()
            }
        });
    }

    poll_level_status();
    polling_interval_d = setInterval(poll_level_status, polling_interval*1000);

    function refreshp() {
        window.location.href = window.location.href;
    }

    function newflow() {
        window.location.href = "student_login.php";
    }
</script>
<script>
    $('.rating-widget').barrating({
        theme: 'fontawesome-stars',
        showSelectedRating: true,
        fastClicks: true
    });
</script>