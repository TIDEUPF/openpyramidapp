<style>

    button.ui-btn,
    .ui-page-theme-a {
        color: #696969 !important;
    }

    body > .ui-page, #activity-frame {
        height: 100%;
    }

    #activity-middle-frame,
    #activity-footer-frame > div{
        padding: 0 1em 0 1em;
    }

    #activity-header-frame > div {
        padding: 0.5em 1em 0.25em 1em;
        background-color: #DFE5EC;
    }

    #activity-middle-frame {
        height: 60%;
        overflow: hidden;
    }

    #activity-header-frame,
    #activity-footer-frame {
        height: 20%;
        overflow: hidden;
    }

    #activity-header-level {
        float: right;
    }

    #activity-header-group {
        float: left;
    }

    #activity-textarea > textarea {
        height: auto !important;
    }

    #activity-next-button {
        display: none;
    }

</style>
<div id="activity-frame">
    <form action="" method="post">
        <div id="activity-header-frame">

            <div>
                <div id="activity-header-group"><?=$username?></div>
                <div id="activity-header-level"><?=$level?></div>
                <div style="clear:both"></div>
            </div>

        </div>
        <div id="activity-middle-frame">
            <h2>Student Login</h2>
            <h3><b>Login</b></h3>
            <div>
                <label for="usr">UserId</label>
                <input type="text" data-clear-btn="true" name="usr" value="">
            </div>

        </div>

        <div id="activity-footer-frame">

            <div>
                <div id="activity-waiting-group"><?=$question_waiting_message?></div>
                <div id="activity-next-button">
                    <button type="submit" class="ui-btn"><?= $question_submit_button?></button>
                </div>
            </div>

        </div>
        <?php foreach($hidden_input_array as $hidden_input_name => $hidden_input_value):?>
            <input type="hidden" name="<?=$hidden_input_name?>" value="<?=$hidden_input_value?>"
        <?php endforeach?>
    </form>
</div>
<script>

</script>