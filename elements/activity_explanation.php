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
        margin-top: 0.5em;
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
        font-size: 1.35em;
        margin-top: 0.5em;
        /*padding-left: 1.0em;*/
    }

    #pre-header {
        padding-left: 1.0em;
    }

    #answer-header-frame > #topbar {
        padding: 0.5em 1em 0.25em 1em;
        background-color: #DFE5EC;
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
            <div id="pre-header">
                <div id="answer-header-text">Activity description</div>
            </div>

        </div>
        <div id="answer-middle-frame">

            <div>

                In this activity, you have to propose a question to ask from the teacher individually. Then you rate questions suggested by your peers in every level. Finally you all will be selecting the highest rated question/s to be submitted for the teacher to answer. When all members finish rating at one level, the next level options will appear.

                <br><br>
                <i>En aquesta activitat has de proposar una pregunta per al professor, després hauràs de puntuar les preguntes del teu grup. Quan tots hagueu puntuat les preguntes apareixerà el següent nivell.</i>



            </div>

        </div>

        <div id="answer-footer-frame">

            <div>
                <div id="answer-submit-button"><button type="submit" name="next" class="ui-btn">Start the activity</button></div>
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
</script>