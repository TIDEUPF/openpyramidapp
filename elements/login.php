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

        <div id="activity-middle-frame">
            <h2>Student Login</h2>
            <div>
                <label for="usr"><b>UserId</b></label>
                <input type="text" data-clear-btn="true" name="usr" value="">
            </div>

        </div>

        <div id="activity-footer-frame">

            <div>
                <div>
                    <button type="submit" class="ui-btn">Log in</button>
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