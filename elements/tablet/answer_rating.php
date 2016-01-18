<style>
    button.ui-btn {
        width: 300px !important;
        margin: auto !important;
    }

    .ui-input-text {
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
        height: 0em;
        right: 0px;

        text-align: center;

        background-color: #000000;
        padding-top: 0px;
        color: white;
        text-shadow: 0 /*{a-page-shadow-x}*/ 1px /*{a-page-shadow-y}*/ 0 /*{a-page-shadow-radius}*/ #000000 /*{a-page-shadow-color}*/;
        font-size: 120%;
        transition-property: all;
        transition-duration: 1s;
        z-index: 1000;
    }

    #countdown-padding {
        height: 0em;
        transition-property: all;
        transition-duration: 1s;
        /*display: none;*/
    }

    .question-number {
        border: 1px solid black;
        background-color: black;
        padding: 4px;
        border-radius: 4px;
        margin-right: 4px;
    }
</style>
<div id="answer-frame">
    <form method="post" action="student.php" data-ajax="false">
    <div id="answer-header-frame">

        <div id="topbar">
            <div id="answer-header-user" class="topbar_item"><?=$username?></div>
            <div id="answer-header-level" class="topbar_item"><?=$level?></div>
            <div id="answer-header-logout" class="topbar_item">Logout</div>
            <div style="clear:both"></div>
        </div>
        <div>
            <!--<div id="answer-header-text"><?=$header_text?></div>-->
            <div id="answer-header-text">Rating is individual. Please rate all options!</div>
        </div>

    </div>

    <div id="answer-middle-frame">
        <?php foreach($answer_text_array as $i=> $answer_data):?>
        <div class="answer-rating-widget">
            <fieldset data-role="controlgroup" data-type="horizontal">
                <legend><span class="question-number"><?=($i+1)?></span><?=htmlspecialchars($answer_data['answer_text'])?></legend>

                <select id="id-answer-rating-<?=$i?>" class="rating-widget" name="optradio<?=($i+1)?>" data-role="none">
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

    <link rel="stylesheet" href="chat/client/embedded.css"/>
    <div id="rating-chat" class="ui-corner-all ui-shadow-inset">
        <div style="margin: .5em 0 0 .3em;">Please use this space to discuss with peers about their options before rating.</div>
        <ul class="pages">
            <li class="chat page">
                <div class="chatArea">
                    <ul class="messages"></ul>
                </div>
                <input class="inputMessage" placeholder="Send a message to your peers..."/>
            </li>
        </ul>
    </div>

    <div style="clear:both"></div>

    <script src="https://cdn.socket.io/socket.io-1.3.7.js"></script>
    <script src="jslib/chatvars.js"></script>
    <script src="chat/client/chat.js"></script>

    <div id="answer-footer-frame">

        <div>
            <div id="answer-waiting-group"><?=$answer_waiting_message?></div>
            <div style="font-size: 1.35em;">Please note that when you submit rating, you will no longer be able to edit rating values. Also you will be removed from the discussion thread for this level.</div>
            <div id="answer-next-button"><button class="ui-btn" name="rate"><?= $answer_rate_submit?></button></div>
        </div>

    </div>

        <?php foreach($hidden_input_array as $hidden_input_name => $hidden_input_value):?>
            <input type="hidden" name="<?=$hidden_input_name?>" value="<?=$hidden_input_value?>">
        <?php endforeach?>

    </form>
    <div id="countdown-padding"></div>
    <div id="countdown"><span id="countdown-text"></span>s left</div>

</div>
<script>
    var polling_interval = 30;
    var time_left = 0;
    var countdown_started = false;
    var countdown_interval = null;
    var polling_interval_d = null;

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
            .css('padding-top', '6px');

        /*
        $('#countdown-padding')
            .show()
            .css('height', '2em');
            */
    }

    function update_countdown() {
        countdown_started = true;
        time_left--;
        $('#countdown-text').text(time_left);
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
            timeout: polling_interval*1000
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