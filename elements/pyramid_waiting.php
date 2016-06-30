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
    }

    #answer-header-user {
        float: left;
    }

    #answer-header-logout {
        float: right;
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

    #answer-header-text {
        text-align: center;
        font-size: 1.35em;
        margin-top: 0.5em;
    }

    #pyramid-icon {
        position:fixed;
        top : 3px;
        left: 55%;
        padding: 0 !important;
    }

    #pyramid-icon img {
        height: 25px;
    }
</style>
<div id="answer-frame">
    <form method="post">
        <div id="answer-header-frame">

            <div id="pyramid-icon">
                <img src="elements/resources/pyramid_icons/<?=($hidden_input_array['levels']+1)?>l_l<?=($hidden_input_array['level'])?>.png">
            </div>

            <div id="topbar">
                <div id="answer-header-user" class="topbar_item"><?=$username?></div>
                <div id="answer-header-level" class="topbar_item"><?=$level?></div>
                <div id="answer-header-logout" class="topbar_item"><?=TS("Logout")?></div>
                <div style="clear:both"></div>
            </div>

            <div>
                <div id="answer-header-text">Waiting a pyramid to continue...</div>
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