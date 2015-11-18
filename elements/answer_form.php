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
        position: fixed;
        bottom: 0px;
        left: 0px;
        height: 1.5em;
        text-align: center;
    }

    #countdown-padding {
        height: 1.5em;
        display: none;
    }

</style>
<div id="answer-frame">
    <div id="countdown"></div>
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
</div>
<script>
    answer = new Object();
    //answer.timeout = <?=$answer_timeout?>;
    answer.skip_timeout = <?=$answer_skip_timeout?>;
    polling_interval = 30;

    var insert_skip_flag = function(e) {
        if(e.preventDefault)
            e.preventDefault();

        $skip = $('<input type="hidden" name="skip" value="" />');
        $('form').append($skip);
        $('form').submit();
    };

    if(answer.timeout) {
        setTimeout(insert_skip_flag, answer.timeout*1000);
    }

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

        if(data.expired)
            refreshp();

        if(data.countdown_started && data.time_left < 5)
            refreshp();

        if(data.countdown_started)
            show_countdown(data.time_left);
    }

    function show_countdown(time_left) {
        $('#countdown').text(time_left);
        $('#countdown').show();
    }

    var poll_level_status = function () {
        $.ajax({
            url: 'level_environment.php',
            method: 'post',
            dataType: 'json',
            success: level_status_actions,
            timeout: polling_interval*1000
        })
    }

    var clear_polling = setInterval(poll_level_status, polling_interval*1000);

    function refreshp() {
        window.location.href = window.location.href;
    }

</script>