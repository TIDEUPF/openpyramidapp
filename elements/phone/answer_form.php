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
        max-height: 2em;
        overflow: hidden;
        line-height: 1em;
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

    #answer-submit-skip-button button {
        display: none;
    }

    #answer-submit-skip-button {
        height: 45px;
        text-align: center;
        margin-top: 5px;
    }

    #countdown {
        display: none;
        position: relative;
        bottom: 0px;
        left: 0px;
        height: 20px;
        width: 100%
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
        transition-duration: 1s;
        /*display: none;*/
    }

</style>
<div id="answer-frame">
    <form method="post" action="student.php" data-ajax="false">
    <div id="answer-header-frame">

        <div>
            <div id="answer-header-user" class="topbar_item"><?=$username?></div>
            <div id="answer-header-level" class="topbar_item"><?=$level?></div>
            <div id="answer-header-logout" class="topbar_item"><?=TS("Logout")?></div>
            <div style="clear:both"></div>
        </div>
        <!--<div id="countdown-padding"></div>-->
        <div id="countdown"><span id="countdown-text"></span></div>


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
</div>
<script>
    answer = new Object();
    //answer.timeout = <?=$answer_timeout?>;
    answer.skip_timeout = <?=$answer_skip_timeout?>;
    var polling_interval = 20;
    var time_left = 0;
    var countdown_started = false;
    var countdown_interval = null;
    var polling_interval_d = null;
    var a_lvl = <?=(int)$hidden_input_array['a_lvl']?>;


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
            $('#answer-submit-skip-button button').show();
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

        /*
        if(data.expired)
            refreshp();
            */

        /*the flow is in a new level*/
        if(data.a_lvl != a_lvl)
            refreshp();

        if(data.rating)
            refreshp();

        //if(data.countdown_started && data.time_left < 0)
        //    refreshp();

        if(data.countdown_started && data.time_left > 0)
            show_countdown(data.time_left);
    }

    function show_countdown(time_left) {
        var countdown_height = 20;
        countdown_started = true;
        this.time_left = time_left - 10;
        if(!countdown_interval)
            countdown_interval = setInterval(update_countdown, 1*1000);
        update_countdown();

        var countdown_bottom = $('body').outerHeight()
            - $('#answer-submit-skip-button').position().top
            - $('#answer-submit-skip-button').outerHeight(true)
            - (countdown_height + 5);

        if(countdown_bottom < 0)
            countdown_bottom = 0;

        //$('#countdown').css('bottom', (-countdown_bottom)+'px');

        //$('#countdown').css('transition-property', 'all');

        $('#countdown')
            .show()
            //.css('height', countdown_height+'px')
            .css('padding-top', '6px')
            .css('background-color', '#000');

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