<style>
    button.ui-btn {
        /*width: 300px !important;*/
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
        width: 45%;
        float: left;
        overflow: scroll;
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
        overflow: hidden;
    }

    #answer-header-group {
        float: left;
    }

    #answer-textarea > textarea {
        height: auto !important;
    }

    #answer-next-button {
        margin-top: 0.5em;
        margin-bottom: 1em;
    }

    #answer-header-text {
        font-size: 1.2em;
        margin-top: 0.5em;
    }

    #answer-header-level {
        float: left;
        text-align: center;
        width: 30%;
    }

    #answer-header-user {
        float: left;
        width: 33%;
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
        display: none;
        position: relative;
        bottom: 0px;
        left: 0px;
        height: 20px;
        width: 100%;
        right: 0px;

        text-align: center;

        /*background-color: #000000;*/
        padding-top: 0px;
        color: white;
        text-shadow: 0 /*{a-page-shadow-x}*/ 1px /*{a-page-shadow-y}*/ 0 /*{a-page-shadow-radius}*/ #000000 /*{a-page-shadow-color}*/;
        font-size: 100%;
        /*transition-property: all;*/
        /*transition-duration: 1s;*/
        /*z-index: 1000;*/
    }

    #countdown-padding {
        height: 0em;
        /*transition-property: all;*/
        /*transition-duration: 1s;*/
        display: none;
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
        width: 10em;
        top: 50%;
        left: 50%;
        z-index: 2000;
        padding: 0.5em;
        border: 2px solid grey;
        border-radius: 4px;
        background-color: #D3D3D3;
        margin-left: -5em;
        margin-top: -9em;
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
    }

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

    li.chat.page div.ui-input-text {
        display: inline-block;
        /*margin: 10px 0 10px 10px !important;*/
        /*margin: 0 !important;*/
    }

    .ui-input-text {
        width: 400px !important;
        /*margin: auto !important;*/
    }

    #answer-header-user {
        max-height: 2em;
        overflow: hidden;
        line-height: 1em;
    }

    #answer-middle-frame {
        width: 100%;
        float: none;
    }

    #pre-submit-warning {
        display: none;
    }

    #async_rated {
        background-color: #ACF97B;
        text-align: center;
    }
</style>
<div id="answer-frame">
    <form method="post" action="student.php" data-ajax="false">
        <div id="submit-confirmation">
            <span id="confirm-text">Are you sure that you want to finish rating and discussion for this level?</span>
            <button id="yes-submit" type="submit" name="rate">Yes</button>
            <button id="no-submit">No</button>
        </div>

    <div id="answer-header-frame">

        <div id="topbar">
            <div id="answer-header-user" class="topbar_item"><?=$username?></div>
            <div id="answer-header-level" class="topbar_item"><?=$level?></div>
            <div id="answer-header-logout" class="topbar_item">Logout</div>
            <div style="clear:both"></div>
        </div>

        <?php if(isset($async_rated)): ?>
            <div id="async_rated"><?=htmlspecialchars($async_rated)?></div>
        <?php endif; ?>

        <!--<div id="countdown-padding"></div>-->
        <div id="countdown"><span id="countdown-text"></span></div>

    </div>

    <div id="answer-middle-frame">
        <div id="answer-header-text">Please rate all options!</div>
        <?php foreach($answer_text_array as $i=> $answer_data):?>
        <div class="answer-rating-widget">
            <fieldset data-role="controlgroup" data-type="horizontal">
                <div>
                    <span class="question-number"><?=($i+1)?></span>
                    <legend><?=htmlspecialchars($answer_data['answer_text'], ENT_COMPAT | ENT_HTML401 | ENT_IGNORE)?></legend>
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

        <div id="answer-footer-frame">

            <div>
                <div id="answer-waiting-group"><?=$answer_waiting_message?></div>
                <div id="pre-submit-warning" style="font-size: 1.35em; margin-top:20px;">Please note that when you submit rating, you will no longer be able to edit rating values. Also you will be removed from the discussion thread for this level.</div>
                <div id="answer-next-button"><button id="answer-next-button-ui" class="ui-btn"><?= $answer_rate_submit?></button></div>
            </div>

        </div>

    </div>

    <?php if($flow_data['ch'] == 1):?>
    <link rel="stylesheet" href="chat/client/embedded.css"/>
    <div id="rating-chat" class="ui-corner-all ui-shadow-inset">
        <div id="rating-chat-instructions" style="margin: .5em 0 0 .3em;">Please use this space to discuss with peers about their options before rating.</div>
        <ul class="pages">
            <li class="chat page">
                <a id="chat-popup-close" href="#" class="ui-icon-arrow-d"></a>
                <div class="chatArea">
                    <ul class="messages">
                        <?php foreach($messages as $message): ?>
                            <li class="message" style="display: list-item;"><span class="username" style="color: rgb(56, 36, 170);"><?=htmlspecialchars($message['sid'], ENT_COMPAT | ENT_HTML401 | ENT_IGNORE)?></span><span class="messageBody"><?=htmlspecialchars($message['message'], ENT_COMPAT | ENT_HTML401 | ENT_IGNORE)?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
        <style>
            #chat-popup-close {
                /*display: none;*/
                display: block;
                position: relative;
                margin-top: 3px;
                left: 47%;
                width 22px;
                height: 22px;
                z-index: 1002;
                border: 0px;
                background-color: rgba(0,0,0,0.0);
            }

            #chat-popup-close::after {
                content: "";
                position: absolute;
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

            #rating-chat {
                display: none;
                position: fixed;
                bottom: 3em;
                left: 0px;
                height: 300px;
                width: 100%;
                z-index: 1002;
            }

            #rating-chat .inputMessage {
                display: none;

            }

            #rating-chat-instructions {
                display: none;
            }
        </style>
    <?php endif;?>

    <div style="clear:both"></div>

    <script src="jslib/chatvars.js"></script>
    <script src="chat/client/chat.js"></script>

    <?php foreach($hidden_input_array as $hidden_input_name => $hidden_input_value):?>
    <input type="hidden" name="<?=$hidden_input_name?>" value="<?=$hidden_input_value?>">
    <?php endforeach?>

    </form>

    <?php if($flow_data['ch'] == 1):?>
    <style>

        #chat-phone-write {
            position:fixed;
            bottom: 2em;
            right: 4.1em;
            height: 0px;
            width: 0px;
            z-index: 1001;
            border: 0px;
            background-color: rgba(0,0,0,0.0);
        }

        #chat-phone-write-padding {
            position:fixed;
            bottom: 0em;
            right: 2.5em;
            height: 3em;
            width: 2.5em;
            z-index: 1001;
            border: 0px;
            background-color: rgba(0,0,0,0.0);
        }

        #chat-phone-write {
            position:fixed;
            bottom: 2em;
            right: 4.1em;
            height: 0px;
            width: 0px;
            z-index: 1001;
            border: 0px;
            background-color: rgba(0,0,0,0.0);
        }

        #chat-popup-show-padding {
            position:fixed;
            bottom: 0em;
            right: 0em;
            height: 3em;
            width: 2.5em;
            z-index: 1001;
            border: 0px;
            background-color: rgba(0,0,0,0.0);
        }

        #chat-popup-show {
            position:fixed;
            bottom: 2em;
            right: 2em;
            height: 0px;
            width: 0px;
            z-index: 1001;
            border: 0px;
            background-color: rgba(0,0,0,0.0);
        }

        #chat-popup-show::after,
        #chat-phone-write::after {
            content: "";
            position: absolute;
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

        #mobile-bottom-chat-input {
            position:fixed;
            bottom: 0px;
            left: 0px;
            /*height: 3em;*/
            width: 100%;
            z-index: 1000;
        }

        #mobile-bottom-chat-message {
            display: none;
            position: fixed;
            bottom: 3em;
            left: 0px;
            height: 2em;
            width: 100%;
            z-index: 1000;
            background-color: #D8FFF4;
            padding: 0.5em 0.5em;
        }

        #mobile-bottom-chat-input input,
        #mobile-bottom-chat-input .ui-corner-all {
            border-radius: 0px !important;
            -webkit-border-radius: 0px !important;
        }

        #mobile-bottom-chat-input .ui-input-text {
            margin: 0px;
        }

        #chat-padding {
            /*height: 2.7em;*/
        }

        ul.pages,
        li.chat.page,
        div.chatArea,
        ul.messages {
            height: inherit;
        }

        li.chat.page {
            display: block;
        }

        div.chatArea {
            padding-bottom: 0px;
        }
        ul.messages {
            box-sizing: border-box;
        }

    </style>
    <div id="chat-padding"></div>
    <div id="mobile-bottom-chat-message">
    </div>
    <div id="mobile-bottom-chat-input">
        <input type="text" class="inputMessage" placeholder="Send a message to your peers..."/>
    </div>
    <a id="chat-phone-write-padding" href="#"></a>
    <a id="chat-phone-write" href="#" class="ui-icon-edit"></a>
    <a id="chat-popup-show-padding" href="#"></a>
    <a id="chat-popup-show" href="#" class="ui-icon-arrow-u"></a>
    <script>
        var chat_last_message_text;
        var last_message_cancel = null;

        $(function() {
            window.resize_middle_frame = function() {
                console.log("resize");
                $('#answer-middle-frame').height(
                    ($(window).height() - ($('#mobile-bottom-chat-input').outerHeight(true) + $('#answer-header-frame').outerHeight(true) ) )
                );

                $('#rating-chat').css(
                    'height',
                    ($(window).height() - ($('#mobile-bottom-chat-input').outerHeight(true) + $('#answer-header-frame').outerHeight(true) ) ) + 'px'
                );

                $('.messages').css(
                    'height',
                    ($('#rating-chat').height() - $('#chat-popup-close').outerHeight(true) ) + 'px'
                );

            }

            resize_middle_frame();

            $(window).resize(function() {
                resize_middle_frame();
            });

            $('#chat-popup-show-padding').click(function(e) {
                e.stopPropagation();
                e.preventDefault();
                $('#rating-chat').toggle();
            });

            $('#chat-phone-write').click(function(e) {
                e.stopPropagation();
                e.preventDefault();
                sendMessage();
            });

            $('#chat-phone-write-padding').click(function(e) {
                e.stopPropagation();
                e.preventDefault();
                sendMessage();
            });

            $('#chat-popup-show, #chat-popup-close').click(function() {
                $('#rating-chat').toggle();
            });

            chat_last_message_text = $('#mobile-bottom-chat-message').text();

            setInterval(function(){
                var lastest_chat_last_message_text = $('#mobile-bottom-chat-message').text();
                if(lastest_chat_last_message_text != chat_last_message_text) {
                    chat_last_message_text = lastest_chat_last_message_text;
                    show_last_message_popup();
                }
            }, 500);

            function show_last_message_popup() {
                $('#mobile-bottom-chat-message').show();
                try {
                    clearTimeout(last_message_cancel);
                } catch(e) {}

                last_message_cancel = setTimeout(function() {
                    $('#mobile-bottom-chat-message').hide();
                }, 5000);
            }
        });
    </script>
    <?php endif;?>

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
        var countdown_height = 20;
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

        //$('#countdown').css('bottom', -countdown_bottom+'px');

        $('#countdown-padding')
         .show()
         .css('height', '2em');

        $('#countdown')
            .show()
            .css('height', countdown_height+'px')
            .css('padding-top', '6px')
            .css('background-color', '#000');

        resize_middle_frame();

        logTimerActivation();

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