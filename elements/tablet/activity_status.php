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
        width: 700px;
        margin: 20px auto 0 auto;
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
        width: 15%;
        float: right;
        text-align: right;
        cursor: pointer;
    }

    #answer-submit-button {
        width: 300px;
        margin-left: auto;
        margin-right: auto;
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
        font-size: 2.35em;
        margin-top: 0.5em;
        /* padding-left: 1.0em; */
        text-align: center;
    }

    #pre-header {
        padding-left: 1.0em;
    }

    #answer-header-frame > #topbar {
        padding: 0.5em 1em 0.25em 1em;
        background-color: #DFE5EC;
    }

    #activity-status-info-block {
        width: 400px;
        float: left;
    }

    #activity-status-img-block {
        width: 300px;
        height: 300px;
        float: right;
    }
</style>
<style>
    .pyramid-animation {
        display: none;
    }

    #activity-status-img-block {
        width: 300px;
        height: 300px;
    }
</style>

<div id="answer-frame">
    <form method="post" action="student.php" data-ajax="false">
        <div id="answer-header-frame">

            <div id="topbar">
                <div id="answer-header-user" class="topbar_item"><?=$username?></div>
                <div id="answer-header-level" class="topbar_item"><?=$level?></div>
                <div id="answer-header-logout" class="topbar_item"><?=TS("Logout")?></div>
                <div style="clear:both"></div>
            </div>
            <div id="pre-header">
                <div id="answer-header-text"><?=TS("Activity status")?></div>
            </div>

        </div>
        <div id="answer-middle-frame">

            <div id="activity-status-info-block">
                <div class="activity-status-info-item">
                    <?=TS("Now you are in Level")?> <?=$ui_level?>
                </div>
                <?php if($ui_level == 1 and $question_submitted):?>
                <div class="activity-status-info-item">
                    <?=TS("You have submitted the question!")?>
                </div>
                <?php endif;?>
                <?php if($ui_level > 1 and $rating_submitted):?>
                <div class="activity-status-info-item">
                    <?=TS("You have submitted rating!")?>
                </div>
                <?php endif;?>
                <?php if($ui_level > 1):?>
                <div class="activity-status-info-item">
                    <?=TS("No. rating pending from others:")?> <?=$n_inactive_peers?>
                </div>
                <?php endif;?>
                <?php if(true):?>
                <div class="activity-status-info-item">
                    <?=TS("Time remaining for the next level:")?> <span id="time-remaining"></span>
                </div>
                <?php endif;?>
            </div>

            <div id="activity-status-img-block">
                <div id="pyramid-summary-levels-block">
                    <div id="pyramid-levels-2" class="pyramid-animation" >
                        <svg viewBox="70 5 280 210">
                            <polygon points="210,5 350,210 70,210" style="fill:pink;stroke:purple;stroke-width:2" />
                            <circle cx="140" cy="165" r="15" stroke="green" stroke-width="2" fill="yellow" />
                            <circle cx="188" cy="165" r="15" stroke="green" stroke-width="2" fill="yellow" />
                            <circle cx="237" cy="165" r="15" stroke="green" stroke-width="2" fill="yellow" />
                            <circle cx="285" cy="165" r="15" stroke="green" stroke-width="2" fill="yellow" />

                            <line x1="130" y1="125" x2="290" y2="125" style="stroke:rgb(255,0,155);stroke-width:4" />

                            <text x="120" y="205" fill="red"><?=TS('Level 1 – Individual level')?></text>
                            <text x="55" y="140" fill="red" transform="rotate(-52,50,60)"><?=TS('Rating level(s)')?></text>

                            <circle cx="210" cy="85" r="23" fill="red" stroke="blue" stroke-width="2"></circle>
                        </svg>
                    </div>

                    <div id="pyramid-levels-3" class="pyramid-animation">
                        <svg viewBox="70 5 280 210">
                            <polygon points="210,5 350,210 70,210" style="fill:pink;stroke:purple;stroke-width:2" />
                            <circle id="activity-3level-level1" cx="120" cy="175" r="12" stroke="green" stroke-width="2" fill="yellow" />
                            <circle cx="180" cy="175" r="12" stroke="green" stroke-width="2" fill="yellow" />
                            <circle cx="240" cy="175" r="12" stroke="green" stroke-width="2" fill="yellow" />
                            <circle cx="300" cy="175" r="12" stroke="green" stroke-width="2" fill="yellow" />
                            <line x1="112" y1="150" x2="308" y2="150" style="stroke:rgb(255,0,155);stroke-width:4" />

                            <text x="120" y="205" fill="red">Level 1 – Individual level</text>
                            <text x="55" y="140" fill="red" transform="rotate(-52,50,60)">Rating level(s)</text>

                            <g class="level1">
                                <circle id="activity-3level-level2" cx="230" cy="120" r="17" fill="green" stroke="black" stroke-width="2"></circle>
                                <circle cx="180" cy="120" r="17" fill="green" stroke="black" stroke-width="2"></circle>
                            </g>


                            <line x1="149" y1="95" x2="271" y2="95" style="stroke:rgb(255,0,155);stroke-width:4" />

                            <circle  id="activity-3level-level3" cx="210" cy="62" r="23" fill="red" stroke="blue" stroke-width="2"></circle>
                        </svg>
                    </div>

                    <div id="pyramid-levels-4" class="pyramid-animation">
                        <svg viewBox="40 5 340 257">
                            <polygon points="210,5 380,261 40,261" style="fill:pink;stroke:purple;stroke-width:2" />
                            <line x1="112" y1="150" x2="308" y2="150" style="stroke:rgb(255,0,155);stroke-width:4" />
                            <line x1="78" y1="205" x2="343" y2="205" style="stroke:rgb(255,0,155);stroke-width:4" />

                            <g  transform="translate(0,225)">
                                <circle cx="90" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="120" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="150" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="180" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="210" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="240" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="270" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="300" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="330" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <text x="120" y="30" fill="red">Level 1 – Individual level</text>

                                <g class="level1">
                                    <circle id="click-circle1" cx="180" cy="-45" r="17" fill="purple" stroke="black" stroke-width="2"></circle>
                                    <circle id="click-circle1" cx="230" cy="-45" r="17" fill="green" stroke="black" stroke-width="2"></circle>
                                    <circle id="click-circle1" cx="130" cy="-45" r="17" fill="green" stroke="black" stroke-width="2"></circle>
                                    <circle id="click-circle1" cx="280" cy="-45" r="17" fill="purple" stroke="black" stroke-width="2"></circle>
                                </g>

                                <g>
                                    <circle id="click-circle1" cx="180" cy="-105" r="17" fill="purple" stroke="black" stroke-width="2"></circle>
                                    <circle id="click-circle1" cx="230" cy="-105" r="17" fill="green" stroke="black" stroke-width="2"></circle>
                                </g>
                            </g>

                            <text x="55" y="140" fill="red" transform="rotate(-52,50,60)">Rating level(s)</text>

                            <line x1="149" y1="95" x2="271" y2="95" style="stroke:rgb(255,0,155);stroke-width:4" />

                            <circle cx="210" cy="62" r="23" fill="red" stroke="blue" stroke-width="2"></circle>
                        </svg>
                    </div>

                    <div id="pyramid-levels-5" class="pyramid-animation">
                        <svg viewBox="0 5 420 307">
                            <polygon points="210,5 420,311 0,311" style="fill:pink;stroke:purple;stroke-width:2" />
                            <line x1="112" y1="150" x2="308" y2="150" style="stroke:rgb(255,0,155);stroke-width:4" />
                            <line x1="72" y1="205" x2="345" y2="205" style="stroke:rgb(255,0,155);stroke-width:4" />
                            <line x1="40" y1="255" x2="380" y2="255" style="stroke:rgb(255,0,155);stroke-width:4" />

                            <g  transform="translate(0,275)">
                                <circle cx="50" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="90" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="125" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="165" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="207" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="247" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="285" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="325" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <circle cx="360" r="12" stroke="green" stroke-width="2" fill="yellow" />
                                <text x="120" y="30" fill="red">Level 1 – Individual level</text>

                                <g class="level1">
                                    <circle id="click-circle1" cx="180" cy="-45" r="17" fill="purple" stroke="black" stroke-width="2"></circle>
                                    <circle id="click-circle1" cx="230" cy="-45" r="17" fill="green" stroke="black" stroke-width="2"></circle>
                                    <circle id="click-circle1" cx="130" cy="-45" r="17" fill="green" stroke="black" stroke-width="2"></circle>
                                    <circle id="click-circle1" cx="280" cy="-45" r="17" fill="purple" stroke="black" stroke-width="2"></circle>
                                    <circle id="click-circle1" cx="85" cy="-45" r="17" fill="purple" stroke="black" stroke-width="2"></circle>
                                    <circle id="click-circle1" cx="330" cy="-45" r="17" fill="green" stroke="black" stroke-width="2"></circle>
                                </g>

                                <g>
                                    <circle id="click-circle1" cx="180" cy="-100" r="17" fill="purple" stroke="black" stroke-width="2"></circle>
                                    <circle id="click-circle1" cx="230" cy="-100" r="17" fill="green" stroke="black" stroke-width="2"></circle>
                                    <circle id="click-circle1" cx="280" cy="-100" r="17" fill="purple" stroke="black" stroke-width="2"></circle>
                                    <circle id="click-circle1" cx="130" cy="-100" r="17" fill="green" stroke="black" stroke-width="2"></circle>
                                </g>

                                <g>
                                    <circle id="click-circle1" cx="180" cy="-150" r="17" fill="purple" stroke="black" stroke-width="2"></circle>
                                    <circle id="click-circle1" cx="230" cy="-150" r="17" fill="green" stroke="black" stroke-width="2"></circle>
                                </g>
                            </g>

                            <text x="55" y="140" fill="red" transform="rotate(-52,50,60)">Rating level(s)</text>

                            <!--circle cx="170" cy="120" r="17" stroke="green" stroke-width="2" fill="yellow"-->


                            <line x1="149" y1="95" x2="271" y2="95" style="stroke:rgb(255,0,155);stroke-width:4" />

                            <circle cx="210" cy="62" r="23" fill="red" stroke="blue" stroke-width="2"></circle>

                        </svg>
                    </div>
                </div>
            </div>

        </div>

        <div id="answer-footer-frame">


        </div>

        <?php foreach($hidden_input_array as $hidden_input_name => $hidden_input_value):?>
            <input type="hidden" name="<?=$hidden_input_name?>" value="<?=$hidden_input_value?>">
        <?php endforeach?>
    </form>
</div>
<script>
    var ui_level = <?=$ui_level?>;
    var levels = <?=$levels?>;
    var time_remaining = <?=$time_remaining?>;
    var figure_level_toogle_color = false;
    show_time_remaining(time_remaining);

    $('#pyramid-levels-' + levels).show();
    $('#activity-3level-level' + ui_level).attr("stroke-width", "4");
    setInterval(function() {
        figure_level_toogle_color = !figure_level_toogle_color;
        var color = (figure_level_toogle_color) ? "white" : "black";
        $('#activity-3level-level' + ui_level).attr("stroke", color);
    }, 1000);

    $('#answer-header-logout').on('touchstart', function(e) {
        window.location="logout.php";
    });

    $('#answer-header-logout').on('click', function(e) {
        window.location="logout.php";
    });

    function show_time_remaining(time_left) {
        var hours_left = Math.floor(time_left/3600);
        var minutes_left = Math.floor((time_left - hours_left*3600)/60);
        if(hours_left == 1)
            text_left = hours_left + ' hour ';
        else if(hours_left > 1)
            text_left = hours_left + ' hours ';

        if(minutes_left == 1)
            text_left += minutes_left + ' minute ';
        else if(minutes_left > 1)
            text_left += minutes_left + ' minutes ';

        $('#time-remaining').text(text_left);
    }

</script>

