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

    #answer-header-text {
        text-align: center;
        font-size: 1.35em;
        margin-top: 0.5em;
    }

    #answer_submitted {
        padding: 10px;
        text-align: center;
        font-size: 120%;
        position: relative;
        top: 30%;
        margin-top: 50px;
    }
</style>
<div id="answer-frame">
    <form method="post">
        <div id="answer-header-frame">

            <div id="topbar">
                <div id="answer-header-user" class="topbar_item"><?=$username?></div>
                <div id="answer-header-level" class="topbar_item"><?=$level?></div>
                <div id="answer-header-logout" class="topbar_item">Logout</div>
                <div style="clear:both"></div>
            </div>

            <div id="answer_submitted">
                Your question was submitted successfully! Please login (using the same userID) from 9th 00:00h CET to see rating and discussion level.
            </div>

        </div>
        <div id="answer-middle-frame">

            <div>


            </div>

        </div>

        <div id="answer-footer-frame">

            <div>
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
    $('#answer-header-logout').on('touchstart', function(e) {
        window.location="logout.php";
    });

    $('#answer-header-logout').on('click', function(e) {
        window.location="logout.php";
    });

    timeoutPeriod = 10000;
    setTimeout("refreshp();",timeoutPeriod);
    function refreshp(){
        window.location.href = window.location.href;
    }
</script>