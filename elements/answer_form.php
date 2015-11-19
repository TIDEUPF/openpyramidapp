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

    #answer-header-frame > div {
        padding: 0.5em 1em 0.25em 1em;
        background-color: #DFE5EC;
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

    #answer-textarea > textarea {
        height: auto !important;
    }

    #student-error {
        margin-top: 1em;
        text-align: center;
        color: red;
        font-size: 130%;
        font-weight: bold;
    }

    #answer-submit-skip-button {
        display: none;
    }

    #countdown {
        /*display: none;*/
        position: fixed;
        bottom: 0px;
        left: 0px;
        height: 0em;
        text-align: center;
        right: 0px;
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

</style>
<div id="answer-frame">
    <div id="countdown"><span id="countdown-text"></span>s left</div>
    <form method="post" action="student.php" data-ajax="false">
    <div id="answer-header-frame">

        <div>
            <div id="answer-header-user" class="topbar_item"><?=$username?></div>
            <div id="answer-header-level" class="topbar_item"><?=$level?></div>
            <div id="answer-header-logout" class="topbar_item">Logout</div>
            <div style="clear:both"></div>
        </div>

    </div>
    <div id="answer-middle-frame">

        <div>

            <div><h2><?= $answer_text?></h2></div>
            <div id="answer-textarea">
                <textarea name="qa" rows="10"></textarea>
            </div>

            <?php if(isset($error)):?>
                <div>
                    <div id="student-error"><?=$error?></div>
                </div>
            <?php endif;?>

        </div>

    </div>

    <div id="answer-footer-frame">

        <div>
            <div id="answer-submit-button"><button type="submit" name="answer" class="ui-btn"><?=$answer_submit_button?></button></div>
            <div id="answer-submit-skip-button"><button type="submit" name="skip" class="ui-btn"><?=$answer_submit_skip_button?></button></div>
            <div id="answer-submitted-message"></div>
            <div id="answer-rating-ready"></div>
        </div>

    </div>

    <?php foreach($hidden_input_array as $hidden_input_name => $hidden_input_value):?>
    <input type="hidden" name="<?=$hidden_input_name?>" value="<?=$hidden_input_value?>">
    <?php endforeach?>
    </form>
    <div id="countdown-padding"></div>
</div>
<script>
    answer = new Object();
    //answer.timeout = <?=$answer_timeout?>;
    answer.skip_timeout = <?=$answer_skip_timeout?>;
    var polling_interval = 30;
    var time_left = 0;
    var countdown_started = false;
    var countdown_interval = null;
    var polling_interval_d = null;


    /*
    var insert_skip_flag = function(e) {
        if(e.preventDefault)
            e.preventDefault();

        $skip = $('<input type="hidden" name="skip" value="" />');
        $('form').append($skip);
        $('form').submit();
    };

    if(answer.timeout) {
        setTimeout(insert_skip_flag, answer.timeout*1000);
    }*/

    if(answer.skip_timeout) {
        setTimeout(function() {
            $('#answer-submit-skip-button').show();
            //$('button[name=skip_button]').on('touchstart', insert_skip_flag);
            //$('button[name=skip_button]').on('click', insert_skip_flag);
        }, answer.skip_timeout*1000);
    }

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

        //if(data.countdown_started && data.time_left < 0)
        //    refreshp();

        if(data.countdown_started && data.time_left > 0)
            show_countdown(data.time_left);
    }

    function show_countdown(time_left) {
        countdown_started = true;
        this.time_left = time_left - 5;
        if(!countdown_interval)
            countdown_interval = setInterval(update_countdown, 1*1000);
        update_countdown();

        $('#countdown')
            .show()
            .css('height', '1.5em')
            .css('padding-top', '6px');

        $('#countdown-padding')
            .show()
            .css('height', '2em');
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