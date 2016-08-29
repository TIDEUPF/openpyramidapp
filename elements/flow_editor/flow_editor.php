<?php
header('Content-Type: text/html; charset=utf-8');
?><!DOCTYPE html>
<html lang="en">
<head>
    <title>CreateActivityPage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
    <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <link rel="stylesheet" type="text/css" href="elements/resources/css/teacher/styles.css">

    <script src="https://cdn.socket.io/socket.io-1.3.7.js"></script>
    <script src="lib/actions.js"></script>
    <script type="text/javascript">
        var socket = io({multiplex : false, 'reconnection': true,'reconnectionDelay': 3000,'maxReconnectionAttempts':Infinity, path: '/<?=$node_path?>/'});
    </script>
</head>

<body>
<input name="page" type="hidden" value="create"/>
<input name="username" type="hidden" value="<?=htmlspecialchars($teacher_id)?>"/>
<div data-role="page">
    <div data-role="main" class="ui-content">
        <div id="center-frame">
            <form data-ajax="false" method="post" action="teacher.php">
                <div id="page-1" class="page">
                    <div class="ui-field-contain">
                        <label for="activity"><?=TS("Activity name")?>:</label>
                        <input type="text" name="activity" id="activity" placeholder="<?=TS("Activity name")?>" value="" data-clear-btn="true">
                    </div>

                    <div class="ui-field-contain">
                        <label for="task_description"><?=TS("Student task")?>:<a href="#popupInfo1" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                            <div data-role="popup" id="popupInfo1" class="ui-content" data-theme="a" style="max-width:350px;">
                                <p><?=TS("This is the task description that will appear for students when they access the pyramid activity")?>.</p>
                            </div></label>
                        <textarea name="task_description" id="task_description"></textarea>
                    </div>

                    <div class="ui-field-contain">
                        <fieldset id="learning_setting_fieldset" data-role="controlgroup">
                            <legend><?=TS("Learning setting")?>:<a href="#popupInfo7" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                                <div data-role="popup" id="popupInfo7" class="ui-content" data-theme="a" style="max-width:700px;">
                                    <p><?=TS("This field specifies whether the classroom setting is a face-to-face or virtual learning context")?>.</p>
                                </div></legend>
                            <input type="radio" name="learning_setting" id="learning_setting-a" value="classroom" checked <?=$data_disabled?>>
                            <label for="learning_setting-a"><?=TS("Classroom")?></label>
                            <input type="radio" name="learning_setting" id="learning_setting-b" value="distance" <?=$data_disabled?>>
                            <label for="learning_setting-b"><?=TS("Distance")?></label>
                        </fieldset>
                    </div>

                    <div class="ui-field-contain">
                        <label for="discussion"><?=TS("Discussion")?> :<a href="#popupInfo6" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                            <div data-role="popup" id="popupInfo6" class="ui-content" data-theme="a" style="max-width:700px;">
                                <p><?=TS("If discussion is enabled, students can discussion with peers to clarify and negotiate their options during rating phases")?>.</p>
                            </div></label>
                        <select name="discussion" id="discussion" data-role="slider" <?=$data_disabled?>>
                            <option value="0"><?=TS("No")?></option>
                            <option value="1" selected="selected"><?=TS("Yes")?></option>
                        </select>
                    </div>

                    <a goto="2" class="create-flow-next ui-btn ui-corner-all ui-shadow ui-btn-icon-right ui-icon-arrow-r">Next</a>
                </div>

                <?php /*second screen*/?>
                <div id="page-2" class="page" style="display: none;">
                    <div id="pyramid-levels-3" class="pyramid-animation" style="position: relative;float: left;margin-left: 15px;">
                        <svg viewBox="70 5 280 210">
                            <polygon points="210,5 350,210 70,210" style="fill:pink;stroke:purple;stroke-width:2" />
                            <circle cx="120" cy="175" r="12" stroke="green" stroke-width="2" fill="yellow" />
                            <circle cx="180" cy="175" r="12" stroke="green" stroke-width="2" fill="yellow" />
                            <circle cx="240" cy="175" r="12" stroke="green" stroke-width="2" fill="yellow" />
                            <circle cx="300" cy="175" r="12" stroke="green" stroke-width="2" fill="yellow" />
                            <line x1="112" y1="150" x2="308" y2="150" style="stroke:rgb(255,0,155);stroke-width:4" />

                            <text x="120" y="205" fill="red"><?=TS('Level 1 – Individual level')?></text>
                            <text x="55" y="140" fill="red" transform="rotate(-52,50,60)"><?=TS('Rating level(s)')?></text>

                            <!-- first level first 2 groups animation -->
                            <circle cx="120" cy="175" stroke="green" stroke-width="2" fill="yellow">
                                <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="120" to="150" />
                                <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="175" to="160" />
                                <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                            </circle>

                            <circle cx="180" cy="175" stroke="green" stroke-width="2" fill="yellow">
                                <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="180" to="150" />
                                <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="175" to="160" />
                                <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                            </circle>

                            <!--circle cx="170" cy="120" r="17" stroke="green" stroke-width="2" fill="yellow"-->

                            <!-- first level next 2 groups animation -->
                            <circle cx="240" cy="180" r="12" stroke="green" stroke-width="2" fill="yellow">
                                <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="240" to="270" />
                                <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="180" to="160" />
                                <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                            </circle>

                            <circle cx="300" cy="180" r="16" stroke="green" stroke-width="2" fill="yellow">
                                <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="300" to="270" />
                                <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="180" to="160" />
                                <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                            </circle>

                            <g class="level1">
                                <circle id="click-circle2"  r="17" stroke="black" stroke-width="2" visibility="hidden">
                                    <set attributeName="visibility" attributeType="CSS" to="visible" begin="2s" dur="2s" fill="freeze" />
                                    <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="270" to="230" />
                                    <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="160" to="120" />
                                    <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                    <animate attributeName="fill" attributeType="CSS" from="yellow" to="green" begin="2s" dur="4s" fill="freeze" />
                                </circle>

                                <circle id="click-circle1" r="17" stroke="black" stroke-width="2" fill="purple" visibility="hidden">
                                    <set attributeName="visibility" attributeType="CSS" to="visible" begin="2s" dur="2s" fill="freeze" />
                                    <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="150" to="180" />
                                    <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="160" to="120" />
                                    <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                    <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                </circle>
                            </g>


                            <line x1="149" y1="95" x2="271" y2="95" style="stroke:rgb(255,0,155);stroke-width:4" />

                            <circle stroke="blue" stroke-width="2" visibility="hidden">
                                <set attributeName="visibility" attributeType="CSS" to="visible" begin="4.5s" dur="2s" fill="freeze" />
                                <animate attributeName="cx" attributeType="XML" begin="4.5s" dur="3s" fill="freeze" from="180" to="210" />
                                <animate attributeName="cy" attributeType="XML" begin="4.5s" dur="3s" fill="freeze" from="120" to="62" />
                                <animate attributeName="r" attributeType="XML" begin="4.5s" dur="3s" fill="freeze" from="17" to="23" />
                                <animate attributeName="fill" attributeType="CSS" from="purple" to="red" begin="4.5s" dur="2s" fill="freeze" />
                            </circle>

                            <circle stroke="blue" stroke-width="2" visibility="hidden">
                                <set attributeName="visibility" attributeType="CSS" to="visible" begin="4.5s" dur="2s" fill="freeze" />
                                <animate attributeName="cx" attributeType="XML" begin="4.5s" dur="3s" fill="freeze" from="230" to="210" />
                                <animate attributeName="cy" attributeType="XML" begin="4.5s" dur="3s" fill="freeze" from="120" to="62" />
                                <animate attributeName="r" attributeType="XML" begin="4.5s" dur="3s" fill="freeze" from="17" to="23" />
                                <animate attributeName="fill" attributeType="CSS" from="green" to="red" begin="4.5s" dur="2s" fill="freeze" />
                            </circle>
                        </svg>
                    </div>

                    <div id="pyramid-levels-2" class="pyramid-animation" style="position: relative;float: left;margin-left: 15px;">
                        <svg viewBox="70 5 280 210">
                            <polygon points="210,5 350,210 70,210" style="fill:pink;stroke:purple;stroke-width:2" />
                            <circle cx="140" cy="165" r="15" stroke="green" stroke-width="2" fill="yellow" />
                            <circle cx="188" cy="165" r="15" stroke="green" stroke-width="2" fill="yellow" />
                            <circle cx="237" cy="165" r="15" stroke="green" stroke-width="2" fill="yellow" />
                            <circle cx="285" cy="165" r="15" stroke="green" stroke-width="2" fill="yellow" />

                            <line x1="130" y1="125" x2="290" y2="125" style="stroke:rgb(255,0,155);stroke-width:4" />

                            <text x="120" y="205" fill="red"><?=TS('Level 1 – Individual level')?></text>
                            <text x="55" y="140" fill="red" transform="rotate(-52,50,60)"><?=TS('Rating level(s)')?></text>

                            <!-- first level first 2 groups animation -->
                            <!--
                            <circle cx="120" cy="175" stroke="green" stroke-width="2" fill="yellow">
                                <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="120" to="150" />
                                <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="175" to="160" />
                                <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                            </circle>

                            <circle cx="180" cy="175" stroke="green" stroke-width="2" fill="yellow">
                                <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="180" to="150" />
                                <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="175" to="160" />
                                <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                            </circle>
                -->
                            <!--circle cx="170" cy="120" r="17" stroke="green" stroke-width="2" fill="yellow"-->

                            <!-- first level next 2 groups animation -->
                            <!--
                            <circle cx="240" cy="180" r="12" stroke="green" stroke-width="2" fill="yellow">
                                <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="240" to="270" />
                                <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="180" to="160" />
                                <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                            </circle>

                            <circle cx="300" cy="180" r="16" stroke="green" stroke-width="2" fill="yellow">
                                <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="300" to="270" />
                                <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="180" to="160" />
                                <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                            </circle>
                            -->

                            <circle stroke="blue" stroke-width="2" visibility="hidden">
                                <set attributeName="visibility" attributeType="CSS" to="visible" begin="0.2s" dur="2s" fill="freeze" />
                                <animate attributeName="cx" attributeType="XML" begin="0.2s" dur="2s" fill="freeze" from="180" to="210" />
                                <animate attributeName="cy" attributeType="XML" begin="0.2s" dur="2s" fill="freeze" from="120" to="85" />
                                <animate attributeName="r" attributeType="XML" begin="0.2s" dur="2s" fill="freeze" from="17" to="23" />
                                <animate attributeName="fill" attributeType="CSS" from="yellow" to="red" begin="0.2s" dur="2s" fill="freeze" />
                            </circle>

                            <circle stroke="blue" stroke-width="2" visibility="hidden">
                                <set attributeName="visibility" attributeType="CSS" to="visible" begin="0.2s" dur="2s" fill="freeze" />
                                <animate attributeName="cx" attributeType="XML" begin="0.2s" dur="2s" fill="freeze" from="230" to="210" />
                                <animate attributeName="cy" attributeType="XML" begin="0.2s" dur="2s" fill="freeze" from="120" to="85" />
                                <animate attributeName="r" attributeType="XML" begin="0.2s" dur="2s" fill="freeze" from="17" to="23" />
                                <animate attributeName="fill" attributeType="CSS" from="yellow" to="red" begin="0.2s" dur="2s" fill="freeze" />
                            </circle>
                        </svg>
                    </div>

                    <div id="pyramid-levels-4" class="pyramid-animation" style="position: relative;float: left;margin-left: 15px;">
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
                                <text x="120" y="30" fill="red"><?=TS('Level 1 – Individual level')?></text>

                                <!-- first level first 2 groups animation -->
                                <circle cx="120" stroke="green" stroke-width="2" fill="yellow">
                                    <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="120" to="150" />
                                    <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="0" to="-15" />
                                    <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                    <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                    <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                                </circle>

                                <circle cx="180" stroke="green" stroke-width="2" fill="yellow">
                                    <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="180" to="150" />
                                    <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="0" to="-15" />
                                    <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                    <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                    <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                                </circle>

                                <!-- first level next 2 groups animation -->
                                <circle cx="240" r="12" stroke="green" stroke-width="2" fill="yellow">
                                    <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="240" to="270" />
                                    <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="0" to="-15" />
                                    <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                    <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                    <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                                </circle>

                                <circle cx="300" r="16" stroke="green" stroke-width="2" fill="yellow">
                                    <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="300" to="270" />
                                    <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="0" to="-15" />
                                    <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                    <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                    <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                                </circle>

                                <g class="level1">
                                    <circle id="click-circle1" r="17" stroke="black" stroke-width="2" fill="purple" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="2s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="150" to="180" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-45" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle2" r="17" stroke="black" stroke-width="2" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="2s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="270" to="230" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-45" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" from="yellow" to="green" begin="2s" dur="4s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle3" r="17" stroke="black" stroke-width="2" fill="green" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="4s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="150" to="130" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-45" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle4" r="17" stroke="black" stroke-width="2" fill="purple" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="4s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="150" to="280" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-45" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                </g>

                                <g>
                                    <circle id="click-circle5" r="17" stroke="black" stroke-width="2" fill="purple" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="4s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="150" to="180" />
                                        <animate attributeName="cy" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="-55" to="-105" />
                                        <animate attributeName="r" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle6" r="17" stroke="black" stroke-width="2" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="4s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="270" to="230" />
                                        <animate attributeName="cy" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="-55" to="-105" />
                                        <animate attributeName="r" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" from="yellow" to="green" begin="2s" dur="4s" fill="freeze" />
                                    </circle>
                                </g>
                            </g>

                            <text x="55" y="140" fill="red" transform="rotate(-52,50,60)"><?=TS('Rating level(s)')?></text>

                            <!--circle cx="170" cy="120" r="17" stroke="green" stroke-width="2" fill="yellow"-->


                            <line x1="149" y1="95" x2="271" y2="95" style="stroke:rgb(255,0,155);stroke-width:4" />

                            <circle stroke="blue" stroke-width="2" visibility="hidden">
                                <set attributeName="visibility" attributeType="CSS" to="visible" begin="4.5s" dur="2s" fill="freeze" />
                                <animate attributeName="cx" attributeType="XML" begin="6s" dur="3s" fill="freeze" from="180" to="210" />
                                <animate attributeName="cy" attributeType="XML" begin="6s" dur="3s" fill="freeze" from="120" to="62" />
                                <animate attributeName="r" attributeType="XML" begin="6s" dur="3s" fill="freeze" from="17" to="23" />
                                <animate attributeName="fill" attributeType="CSS" from="purple" to="red" begin="6s" dur="2s" fill="freeze" />
                            </circle>

                            <circle stroke="blue" stroke-width="2" visibility="hidden">
                                <set attributeName="visibility" attributeType="CSS" to="visible" begin="4.5s" dur="2s" fill="freeze" />
                                <animate attributeName="cx" attributeType="XML" begin="6s" dur="3s" fill="freeze" from="230" to="210" />
                                <animate attributeName="cy" attributeType="XML" begin="6s" dur="3s" fill="freeze" from="120" to="62" />
                                <animate attributeName="r" attributeType="XML" begin="6s" dur="3s" fill="freeze" from="17" to="23" />
                                <animate attributeName="fill" attributeType="CSS" from="green" to="red" begin="4.5s" dur="2s" fill="freeze" />
                            </circle>
                        </svg>
                    </div>

                    <div id="pyramid-levels-5" class="pyramid-animation" style="position: relative;float: left;margin-left: 15px;">
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
                                <text x="120" y="30" fill="red"><?=TS('Level 1 – Individual level')?></text>

                                <!-- first level first 2 groups animation -->
                                <circle cx="120" stroke="green" stroke-width="2" fill="yellow">
                                    <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="120" to="150" />
                                    <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="0" to="-15" />
                                    <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                    <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                    <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                                </circle>

                                <circle cx="180" stroke="green" stroke-width="2" fill="yellow">
                                    <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="180" to="150" />
                                    <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="0" to="-15" />
                                    <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                    <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                    <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                                </circle>

                                <!-- first level next 2 groups animation -->
                                <circle cx="240" r="12" stroke="green" stroke-width="2" fill="yellow">
                                    <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="240" to="270" />
                                    <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="0" to="-15" />
                                    <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                    <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                    <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                                </circle>

                                <circle cx="300" r="16" stroke="green" stroke-width="2" fill="yellow">
                                    <animate attributeName="cx" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="300" to="270" />
                                    <animate attributeName="cy" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="0" to="-15" />
                                    <animate attributeName="r" attributeType="XML" begin="0s" dur="2s" fill="freeze" from="12" to="16" />
                                    <animate attributeName="fill" attributeType="CSS" from="yellow" to="purple" begin="0s" dur="2s" fill="freeze" />
                                    <set attributeName="visibility" attributeType="CSS" to="hidden" begin="2s" dur="5s" fill="freeze" />
                                </circle>

                                <g class="level1">
                                    <circle id="click-circle1" r="17" stroke="black" stroke-width="2" fill="purple" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="2s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="150" to="180" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-45" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle2" r="17" stroke="black" stroke-width="2" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="2s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="270" to="230" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-45" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" from="yellow" to="green" begin="2s" dur="4s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle3" r="17" stroke="black" stroke-width="2" fill="green" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="4s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="150" to="130" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-45" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle4" r="17" stroke="black" stroke-width="2" fill="purple" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="4s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="150" to="280" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-45" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle13" r="17" stroke="black" stroke-width="2" fill="purple" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="4s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="150" to="85" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-45" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle14" r="17" stroke="black" stroke-width="2" fill="green" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="4s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="150" to="330" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-45" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                </g>

                                <g>
                                    <circle id="click-circle5" r="17" stroke="black" stroke-width="2" fill="purple" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="4s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="150" to="180" />
                                        <animate attributeName="cy" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="-55" to="-100" />
                                        <animate attributeName="r" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle6" r="17" stroke="black" stroke-width="2" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="4s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="270" to="230" />
                                        <animate attributeName="cy" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="-55" to="-100" />
                                        <animate attributeName="r" attributeType="XML" begin="4s" dur="2s" fill="freeze" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" from="yellow" to="green" begin="2s" dur="4s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle23" r="17" stroke="black" stroke-width="2" fill="purple" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="6s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="230" to="280" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-100" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle24" r="17" stroke="black" stroke-width="2" fill="green" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="6s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="230" to="130" />
                                        <animate attributeName="cy" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="-15" to="-100" />
                                        <animate attributeName="r" attributeType="XML" begin="2s" dur="2s" fill="freeze" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                </g>

                                <g>
                                    <circle id="click-circle7" r="17" stroke="black" stroke-width="2" fill="purple" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="6s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="6s" dur="2s" fill="freeze" from="150" to="180" />
                                        <animate attributeName="cy" attributeType="XML" begin="6s" dur="2s" fill="freeze" from="-100" to="-150" />
                                        <animate attributeName="r" attributeType="XML" begin="6s" dur="2s" fill="freeze" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" begin="2s" dur="2s" fill="freeze" />
                                    </circle>

                                    <circle id="click-circle8" r="17" stroke="black" stroke-width="2" visibility="hidden">
                                        <set attributeName="visibility" attributeType="CSS" to="visible" begin="6s" dur="2s" fill="freeze" />
                                        <animate attributeName="cx" attributeType="XML" begin="6s" dur="2s" fill="freeze" from="270" to="230" />
                                        <animate attributeName="cy" attributeType="XML" begin="6s" dur="2s" fill="freeze" from="-100" to="-150" />
                                        <animate attributeName="r" attributeType="XML" begin="6s" dur="2s" fill="freeze" from="16" to="17" />
                                        <animate attributeName="fill" attributeType="CSS" from="yellow" to="green" begin="2s" dur="4s" fill="freeze" />
                                    </circle>
                                </g>
                            </g>

                            <text x="55" y="140" fill="red" transform="rotate(-52,50,60)"><?=TS('Rating level(s)')?></text>

                            <!--circle cx="170" cy="120" r="17" stroke="green" stroke-width="2" fill="yellow"-->


                            <line x1="149" y1="95" x2="271" y2="95" style="stroke:rgb(255,0,155);stroke-width:4" />

                            <circle stroke="blue" stroke-width="2" visibility="hidden">
                                <set attributeName="visibility" attributeType="CSS" to="visible" begin="4.5s" dur="2s" fill="freeze" />
                                <animate attributeName="cx" attributeType="XML" begin="8s" dur="3s" fill="freeze" from="180" to="210" />
                                <animate attributeName="cy" attributeType="XML" begin="8s" dur="3s" fill="freeze" from="120" to="62" />
                                <animate attributeName="r" attributeType="XML" begin="8s" dur="3s" fill="freeze" from="17" to="23" />
                                <animate attributeName="fill" attributeType="CSS" from="purple" to="red" begin="6s" dur="2s" fill="freeze" />
                            </circle>

                            <circle stroke="blue" stroke-width="2" visibility="hidden">
                                <set attributeName="visibility" attributeType="CSS" to="visible" begin="4.5s" dur="2s" fill="freeze" />
                                <animate attributeName="cx" attributeType="XML" begin="8s" dur="3s" fill="freeze" from="230" to="210" />
                                <animate attributeName="cy" attributeType="XML" begin="8s" dur="3s" fill="freeze" from="120" to="62" />
                                <animate attributeName="r" attributeType="XML" begin="8s" dur="3s" fill="freeze" from="17" to="23" />
                                <animate attributeName="fill" attributeType="CSS" from="green" to="red" begin="4.5s" dur="2s" fill="freeze" />
                            </circle>
                        </svg>
                    </div>

                    <div id="popup" style="position: relative;float: left;width:500px;">
                        <h4><?=TS("Pyramid Configurations")?></h4>

                        <div class="ui-field-contain">
                            <label for="expected_students"><?=TS("Total number of students")?>:<a href="#popupInfo2" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                                <div data-role="popup" id="popupInfo2" class="ui-content" data-theme="a" style="max-width:350px;">
                                    <p><?=TS("This is the total number of expected students in the class available for the activity. This could be an estimated value (specially during a massive open online course case)")?>.</p>
                                </div></label>
                            <input type="number" name="expected_students" id="expected_students" placeholder="<?=TS("Total class size")?>" data-clear-btn="true">
                        </div>

                        <div class="ui-field-contain ui-mini">
                            <label for="first_group_size"><?=TS("No. of students per group at rating level 1")?>:<a href="#popupInfo" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                                <div data-role="popup" id="popupInfo" class="ui-content" data-theme="a" style="max-width:700px;">
                                    <p><?=TS("This specifies the initial group size at level 2 (first rating level) after option submission level. This size will be doubled when groups propagate to upper levels")?>.</p>
                                </div>
                            </label>
                            <input type="range" name="first_group_size" id="first_group_size" value="3" min="2" max="10" data-highlight="true">
                        </div>

                        <div class="ui-field-contain">
                            <label for="n_levels"><?=TS("No. of levels")?>:<a href="#popupInfo5" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                                <div data-role="popup" id="popupInfo5" class="ui-content" data-theme="a" style="max-width:700px;">
                                    <p><?=TS("This includes both option submission level and rating levels. It is recommended to have 3 to 4 levels for active participation")?>.</p>
                                </div></label>
                            <input type="range" name="n_levels" id="n_levels" value="3" min="2" max="5" data-highlight="true">
                        </div>

                        <div id="multiple_pyramids_block" class="ui-field-contain">
                            <label for="multiple_pyramids"><?=TS("Allow multiple pyramids")?>:<a href="#popupInfo4" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                                <div data-role="popup" id="popupInfo4" class="ui-content" data-theme="a" style="max-width:700px;">
                                    <p><?=TS("If your class is relatively large class, it would be better to enable this feature, so several pyramids will be created and students will be automatically allocated")?>.</p>
                                </div>
                            </label>
                            <select name="multiple_pyramids" id="multiple_pyramids" data-role="slider">
                                <option value="0"><?=TS("No")?></option>
                                <option value="1" selected="selected"><?=TS("Yes")?></option>
                            </select>
                            <label for="n_levels"><?=TS("No. of pyramids created")?>:<a href="#popupInfo52" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                                <div data-role="popup" id="popupInfo52" class="ui-content" data-theme="a" style="max-width:700px;">
                                    <p><?=TS("This fields show the number of pyramids the app will create based on the total number of students and students per pyramid")?>.</p>
                                </div></label>
                            <input type="number" name="n_pyramids" id="n_pyramids" value="" data-highlight="true" readonly>
                        </div>

                        <div class="ui-field-contain">
                            <label for="minInfo"><?=TS("Minimum students per pyramid")?>:<a href="#popupInfo3" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                                <div data-role="popup" id="popupInfo3" class="ui-content" data-theme="a" style="max-width:700px;">
                                    <p><?=TS("Number of students allowed to be grouped into a single pyramid. Based on the total number of students and this value, several pyramids may require and it will be automatically suggested by the system")?>.</p>
                                </div></label>
                            <input type="number" name="min_students_per_pyramid" id="min_students_per_pyramid" value="20" data-clear-btn="true">
                        </div>

                        <div class="ui-field-contain">
                            <label for="n_final_outcomes"><?=TS("Final outcomes")?> :<a href="#popupInfoOutcomes" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                                <div data-role="popup" id="popupInfoOutcomes" class="ui-content" data-theme="a" style="max-width:700px;">
                                    <p><?=TS("Total number of outcomes taking in account all pyramids")?>.</p>
                                </div></label>
                            <input type="number" name="n_final_outcomes" id="n_final_outcomes" value="" readonly>
                        </div>

                        <!--<div class="ui-field-contain">-->
                        <a href="#popupAdvanced" data-rel="popup" data-position-to="window" class="ui-btn ui-corner-all ui-shadow ui-btn-inline" data-transition="pop"><?=TS("Advanced Settings")?></a>
                        <div data-role="popup" id="popupAdvanced" data-theme="a" class="ui-corner-all">

                            <div style="padding:10px 20px;">
                                <h4><?=TS("Advanced Pyramid Configurations")?></h4>
                                <h5><?=TS("It is optional to change these default values")?>.</h5>

                                <div id="pop-background"></div>

                                <label for="s_question"><?=TS("Option submission timer")?>:<a text-data="#cpopup1" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext"
                                                                                              title="This timer specifies the time permitted for initial option (artifact) submission for students"><?=TS("More")?></a>
                                    <div id="cpopup1-text" class="ui-content tooltip-popup" data-theme="a" style="display:none">
                                        <p><?=TS("This timer specifies the time permitted for initial option (artifact) submission for students")?>.</p>
                                    </div>
                                </label>
                                <div style="position:relative;float:left;">
                                    <input type="number" name="s_question_unit_value" id="s_question_unit_value" value="" data-clear-btn="true" data-wrapper-class="numk" />
                                </div>

                                <div style="position:relative;float:left; margin-left:10px; margin-top:2px;">
                                    <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
                                        <input type="radio" name="s_question_unit" id="s_question_unit-a" value="m" checked="checked">
                                        <label for="s_question_unit-a"><?=TS("Minutes")?></label>
                                        <input type="radio" name="s_question_unit" id="s_question_unit-b" value="h">
                                        <label for="s_question_unit-b"><?=TS("Hours")?></label>
                                        <input type="radio" name="s_question_unit" id="s_question_unit-c" value="d">
                                        <label for="s_question_unit-c"><?=TS("Days")?></label>
                                    </fieldset></div>
                                <div style="clear:both;"></div>

                                <label for="h_question"><?=TS("Option submission hard timer")?>:<a text-data="#cpopup2" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext"
                                                                                                   title="This timer specifies the maximum time permitted for initial option (artifact) submission for students. Once expired, every student will be promoted next level."><?=TS("More")?></a>
                                    <div id="cpopup2-text" class="ui-content tooltip-popup" data-theme="a" style="display:none">
                                        <p><?=TS("This timer specifies the maximum time permitted for initial option (artifact) submission for students. Once expired, every student will be promoted next level")?>.</p>
                                    </div>

                                </label>
                                <div style="position:relative;float:left;">
                                    <input type="number" name="h_question_unit_value" id="h_question_unit_value" data-wrapper-class="numk" value="" data-clear-btn="true" />
                                </div>

                                <div style="position:relative;float:left; margin-left:10px; margin-top:2px;">
                                    <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
                                        <input type="radio" name="h_question_unit" id="h_question_unit-a" value="m" checked="checked">
                                        <label for="h_question_unit-a"><?=TS("Minutes")?></label>
                                        <input type="radio" name="h_question_unit" id="h_question_unit-b" value="h">
                                        <label for="h_question_unit-b"><?=TS("Hours")?></label>
                                        <input type="radio" name="h_question_unit" id="h_question_unit-c" value="d">
                                        <label for="h_question_unit-c"><?=TS("Days")?></label>
                                    </fieldset></div>
                                <div style="clear:both;"></div>

                                <label for="s_rating"><?=TS("Rating timer")?>:<a text-data="#cpopup3" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext"
                                                                                 title="This timer specifies the time permitted for rating at each level including discussion time."><?=TS("More")?></a>
                                    <div id="cpopup3-text" class="ui-content tooltip-popup" data-theme="a" style="display:none">
                                        <p><?=TS("This timer specifies the time permitted for rating at each level including discussion time")?>.</p>
                                    </div>

                                </label>
                                <div style="position:relative;float:left;">
                                    <input type="number" name="s_rating_unit_value" id="s_rating_unit_value" data-wrapper-class="numk" value="" data-clear-btn="true">
                                </div>

                                <div style="position:relative;float:left; margin-left:10px; margin-top:2px;">
                                    <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
                                        <input type="radio" name="s_rating_unit" id="s_rating_unit-a" value="m" checked="checked">
                                        <label for="s_rating_unit-a"><?=TS("Minutes")?></label>
                                        <input type="radio" name="s_rating_unit" id="s_rating_unit-b" value="h">
                                        <label for="s_rating_unit-b"><?=TS("Hours")?></label>
                                        <input type="radio" name="s_rating_unit" id="s_rating_unit-c" value="d">
                                        <label for="s_rating_unit-c"><?=TS("Days")?></label>
                                    </fieldset></div>
                                <div style="clear:both;"></div>

                                <label for="h_rating"><?=TS("Rating hard timer")?>:<a text-data="#cpopup4" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext"
                                                                                      title="This is the maximum time allowed for rating and discussion at each level. Once expired everyone is promoted to next level."><?=TS("More")?></a>
                                    <div id="cpopup4-text" class="ui-content tooltip-popup" data-theme="a" style="display:none">
                                        <p><?=TS("This is the maximum time allowed for rating and discussion at each level. Once expired everyone is promoted to next level")?>.</p>
                                    </div>

                                </label>
                                <div style="position:relative;float:left;">
                                    <input type="number" name="h_rating_unit_value" id="h_rating_unit_value" data-wrapper-class="numk" value="" data-clear-btn="true">
                                </div>

                                <div style="position:relative;float:left; margin-left:10px; margin-top:2px;">
                                    <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
                                        <input type="radio" name="h_rating_unit" id="h_rating_unit-a" value="m" checked="checked">
                                        <label for="h_rating_unit-a"><?=TS("Minutes")?></label>
                                        <input type="radio" name="h_rating_unit" id="h_rating_unit-b" value="h">
                                        <label for="h_rating_unit-b"><?=TS("Hours")?></label>
                                        <input type="radio" name="h_rating_unit" id="h_rating_unit-c" value="d">
                                        <label for="h_rating_unit-c"><?=TS("Days")?></label>
                                    </fieldset></div>
                                <div style="clear:both;"></div>

                                <label for="satisfaction"><?=TS("Satisfaction percentage")?>:<a text-data="#cpopup5" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext"
                                                                                                title="When this percentage is reached, students will be promoted for the next level. This is important when longer timer values are defined at MOOC scenarios with less participation."><?=TS("More")?></a>
                                    <div id="cpopup5-text" class="ui-content tooltip-popup" data-theme="a" style="display:none">
                                        <p><?=TS("When this percentage is reached, students will be promoted for the next level. This is important when longer timer values are defined at MOOC scenarios with less participation")?>.</p>
                                    </div>

                                </label>
                                <input type="range" name="satisfaction" id="satisfaction" value="60" min="30" max="100" data-highlight="true">

                                <a data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check"><?=TS("Submit")?></a>
                            </div>
                        </div><!--popup-->
                        <br>
                        <div class="ui-input-btn ui-btn ui-btn-inline ui-shadow ui-corner-all ui-icon-check ui-btn-icon-left ui-btn-a">
                            <?=(($edit) ? TS('Update') : TS('Create'))?><input name="create_flow" type="submit" data-enhanced="true" value="<?=(($edit) ? 'update' : 'create')?>">
                        </div>

                        <!--<div id="popup-clearance"></div>-->

                        <!--fields not enetred by the user -->
                        <input type="hidden" name="sync">
                        <input type="hidden" name="random_selection">
                        <input type="hidden" name="n_selected_answers">

                        <input type="hidden" name="s_question">
                        <input type="hidden" name="h_question">
                        <input type="hidden" name="s_rating">
                        <input type="hidden" name="h_rating">

                        <input type="hidden" name="flow_data">
                        <?php if($edit):?>
                            <input type="hidden" name="fid" value="<?=$fid?>">
                        <?php endif;?>
                        <?php if(isset($ldshake)):?>
                            <input type="hidden" name="ldshake_doc_id" value="<?=$ldshake_doc_id?>">
                            <input type="hidden" name="ldshake_sectoken" value="<?=$ldshake_sectoken?>">
                            <input type="hidden" name="ldshake_save" value="true">
                        <?php endif;?>
                    </div>

                    <div style="clear: both;"></div>

                </div>
            </form>
        </div>
    </div>
</div>


<script>

    //validation

    //timers in seconds
    var timers = [
        's_question',
        'h_question',
        's_rating',
        'h_rating'
    ];

    var new_flow_fields = [
        'satisfaction',
        'n_selected_answers',
        'n_levels',
        'first_group_size',
        'discussion',
        'random_selection',
        'multiple_pyramids',
        's_question',
        'h_question',
        's_rating',
        'h_rating'
    ];

    var edit_flow_fields = <?=json_encode($flow_fields)?>;

    var units_in_seconds = {
        'd': 86400,
        'h': 3600,
        'm': 60
    };

    var sync_table = {
        'classroom': 'sync',
        'distance': 'async'
    };

    var discussion_table = {
        0: 'no_discussion',
        1: 'discussion'
    };

    var defaults = <?=json_encode($defaults)?>;
    var sync = 'sync';
    var discussion = 'discussion';

    var edit = <?=(isset($edit)) ? 'true' : 'false'?>;
    <?php if(isset($edit)):?>
    var flow_edit_data = <?=json_encode($flow_object)?>;
    var current_default = flow_edit_data;
    var default_flow_fields = edit_flow_fields;
    var sync = default_flow_fields.sync;
    var discussion = default_flow_fields.discussion;
    <?php else:?>
    var current_default = defaults[sync][discussion];
    var default_flow_fields = new_flow_fields;
    <?php endif;?>

    function time_field_to_seconds(field) {
        var unit_time = get_field_integer(field + '_unit_value');
        var unit = $('[name="' + field + '_unit' + '"]:checked').val();
        var unit_seconds = units_in_seconds[unit];
        var seconds_time = unit_time * unit_seconds;

        return seconds_time;
    }

    function time_seconds_to_units(value) {
        var converted_value = {};
        for(var unit in units_in_seconds) {
            if(value < units_in_seconds[unit])
                continue;

            if(value % units_in_seconds[unit] && unit != 'm')
                continue;

            converted_value.value = value / units_in_seconds[unit];
            converted_value.unit = unit;
            break;
        }

        return converted_value;
    }

    function restore_timers() {
        for(var timer in timers) {
            var value = current_default[timers[timer]];
            var converted_value = time_seconds_to_units(value);

            set_field(timers[timer] + '_unit_value', converted_value.value);
            set_field(timers[timer] + '_unit', converted_value.unit);
        }
        $('#popupAdvanced-popup fieldset').controlgroup();
    }

    function timers_to_seconds() {
        for(var timer in timers) {
            var converted_value = time_field_to_seconds(timers[timer]);

            set_field(timers[timer], converted_value);
        }
    }

    function flow_load_fields() {
        for(var field in default_flow_fields) {
            set_field(default_flow_fields[field], current_default[default_flow_fields[field]]);
        }
    }

    function set_n_final_outcomes() {
        var n_groups = get_n_groups();
        var n_levels_rating = get_field_integer("n_levels") - 1;
        var n_pyramids = get_number_of_pyramids();

        var n_outcomes_per_pyramid = Math.floor(n_groups / Math.pow(2, n_levels_rating - 1));
        var n_final_outcomes = n_outcomes_per_pyramid * n_pyramids;

        $('[name="n_final_outcomes"]').val(n_final_outcomes);
    }

    function n_pyramids_update(event) {
        var number_of_pyramids = get_number_of_pyramids();

        $('[name="n_pyramids"]').val(number_of_pyramids);
    }

    function get_n_groups() {
        var expected_students_setting = get_field_integer("expected_students");
        var number_of_pyramids = get_number_of_pyramids();
        var expected_students_per_pyramid = Math.floor(expected_students_setting / number_of_pyramids);
        var first_group_size_setting = get_field_integer("first_group_size");

        if(!(expected_students_per_pyramid > 0 && first_group_size_setting > 0))
            return false;

        var n_groups = Math.floor(expected_students_per_pyramid / first_group_size_setting);

        return n_groups;
    }

    function max_possible_levels_update(event) {
        var max_possible_levels = 4;
        var n_groups = get_n_groups();

        if(n_groups < 2)
            return false;
        else if (n_groups < 4)
            max_possible_levels = 3;
        else if (n_groups < 8)
            max_possible_levels = 4;
        else
            max_possible_levels = 5;

        $('[name="n_levels"]').attr("max", max_possible_levels);
        $('[name="n_levels"]').slider("refresh");
    }

    function update_fields() {
        n_pyramids_update();
        update_first_group_size();
        max_possible_levels_update();
        set_n_final_outcomes();
    }

    function get_number_of_pyramids() {
        var number_of_pyramids = 1;

        try {
            var multiple_pyramids_setting = get_field_integer("multiple_pyramids");
            var min_students_per_pyramid_setting = get_field_integer("min_students_per_pyramid");
            var expected_students_setting = get_field_integer("expected_students");

            if (multiple_pyramids_setting) {
                number_of_pyramids = Math.floor(expected_students_setting / min_students_per_pyramid_setting);
            }
        } catch (e) {

        }

        return Math.max(1, number_of_pyramids);
    }

    function update_first_group_size() {
        var expected_students_setting = get_field_integer("expected_students");
        var number_of_pyramids = get_number_of_pyramids();
        var first_group_size_setting = get_field_integer("first_group_size");
        var expected_students_per_pyramid = Math.floor(expected_students_setting / number_of_pyramids);

        var max_possible_size = Math.min(10, Math.floor(expected_students_per_pyramid/2));

        if(first_group_size_setting > max_possible_size)
            set_field("first_group_size", max_possible_size);

        $('[name="first_group_size"]').attr("max", max_possible_size);
        $('[name="first_group_size"]').slider("refresh");
    }

    var post_process_data = function() {
        //validate
        $page2_fields = $('#page-2').find('input[type="text"], input[type="number"], textarea').not('[readonly]');

        var successful_validation = true;
        $page2_fields.each(function() {
            $('label[for="' + $(this).attr("name") + '"]').removeClass("missing");
        });

        $page2_fields.each(function() {
            if($(this).val() == "") {
                $('label[for="' + $(this).attr("name") + '"]').addClass("missing");
                successful_validation = false;
            }

            if($(this).attr("name") == "number") {
                try {
                    get_field_integer($(this).attr("name"));
                } catch (e) {
                    $('label[for="' + $(this).attr("name") + '"]').addClass("missing");
                    successful_validation = false;
                }
            }
        });

        if(!successful_validation)
            return false;

        timers_to_seconds();

        //sync
        var learning_setting = $('[name="learning_setting"]:checked').val();
        var sync = (learning_setting === "classroom") ? 1 : 0;
        $('[name="sync"]').val(sync);

        //random_selection
        var random_selection = current_default['random_selection'];
        $('[name="random_selection"]').val(random_selection);

        //n_selected_answers
        var n_selected_answers = current_default['n_selected_answers'];
        $('[name="n_selected_answers"]').val(n_selected_answers);

        var flow_data = {};
        for(var i in edit_flow_fields) {
            var field = edit_flow_fields[i];
            //flow_data[field] = $('[name="' + field +'"]').val();
            flow_data[field] = get_field(field);
        }

        $('[name="flow_data"]').val(JSON.stringify(flow_data));
    };

    //calculate the remaining variables before submitting
    $('form').submit(post_process_data);


    var e = document.querySelector('#click-circle1');
    e.addEventListener('click', function(event){
        var e = document.querySelector('#popup');
        e.style.display = 'block';
    });

    var e = document.querySelector('#click-circle2');
    e.addEventListener('click', function(event){
        var e = document.querySelector('#popup');
        e.style.display = 'block';
        // e.setAttribute('style', 'display:block;');
    });

    //next page
    $('.create-flow-next').click(function(event) {
        event.stopPropagation();
        event.preventDefault();

        //validate the fields
        $page1_fields = $('#page-1').find('input[type="text"], input[type="number"], textarea');

        var successful_validation = true;
        $page1_fields.each(function() {
            $('label[for="' + $(this).attr("name") + '"]').removeClass("missing");
        });

        $page1_fields.each(function() {
            if($(this).val() == "") {
                $('label[for="' + $(this).attr("name") + '"]').addClass("missing");
                successful_validation = false;
            }
        });

        if(!successful_validation)
            return false;

        var goto = $(this).attr('goto');

        if(!edit) {
            var learning_setting = $('[name="learning_setting"]:checked').val();
            var discussion_setting = $('[name="discussion"]').val();
            sync = sync_table[learning_setting];
            discussion = discussion_table[discussion_setting];
            current_default = defaults[sync][discussion];
            flow_load_fields();
            restore_timers();
        } else {
            update_fields();
        }

        $('#page-1').fadeOut(400,'swing', function() {
            var n_levels_setting = $('[name="n_levels"]').val();
            $('.pyramid-animation').hide();
            var figure = $('#pyramid-levels-' + n_levels_setting).html();
            $('#pyramid-levels-' + n_levels_setting).html(figure);
            $('#pyramid-levels-' + n_levels_setting).show();
            $('#page-' + goto).fadeIn();
        });
    });

    $(document).on('pagebeforecreate', function() {
        //init
        flow_load_fields();
    });

    $(document).on('pagecreate', function() {
        flow_load_fields();
        restore_timers();

        $('a[data-rel="popup"]').attr('tabindex', '-1');

        //disable enter submit
        $('input').keypress(function(event) {

            try {
                update_fields();
            } catch (e) {

            }
            if(event.keyCode == 13) {
                return false;
            }
        });

        setInterval(function () {
            try {
                update_fields();
            } catch (e) {

            }
        }, 700);

        //update sliders
        $('[data-role="slider"], [data-type="range"]').slider('refresh');

        //update control groups
        $('[data-role="controlgroup"]').controlgroup('refresh')

        $('#first_group_size').on('slidestop', function(event) {
            var n_students = parseInt($('#first_group_size').val(), 10);

            var radius = Math.pow(n_students,1/10) / Math.pow(3,1/10) * 17;
            $('.level1 circle').attr('r', radius);
        });

        $('#n_levels').on('slidestop', function(event) {
            var pyramid_number = $('#n_levels').val();

            $('.pyramid-animation').hide();
            $('#pyramid-levels-' + pyramid_number).show();
        });

        $('[data-role="popup"]').popup( "option", "history", false );

        $('[name="expected_students"]').on('change', update_fields);
        $('[name="first_group_size"]').on('slidestop', update_fields);
        $('[name="n_levels"]').on('slidestop', update_fields);
        $('[name="multiple_pyramids"]').on('change', update_fields);
        $('[name="min_students_per_pyramid"]').on('change', update_fields);
    });

    //tooltip popups
    $('[text-data]').each(function() {
        $this = $(this);
        $tooltip = $($this.attr('text-data') + '-text');
        $tooltip.on('click', function(event) {
            event.stopPropagation();
            event.preventDefault();
        });
    });

    $('[text-data]').on('click', function(event) {
        event.stopPropagation();
        event.preventDefault();
        $this = $(this);
        $tooltip = $($this.attr('text-data') + '-text');
        $tooltip.removeClass('out');
        $tooltip.addClass('in');
        $tooltip.addClass('pop');
        $tooltip.addClass('ui-overlay-shadow');
        $tooltip.addClass('ui-corner-all');
        $('#pop-background').show();
        $tooltip.show();
    });

    $('#pop-background').on('click', function(event) {
        event.stopPropagation();
        $('[text-data]').each(function() {
            $tooltip = $($this.attr('text-data') + '-text');
            $tooltip.removeClass('in');
            $tooltip.addClass('out');
            $tooltip.hide();
        });
        $(this).hide();
    });


    //util
    function set_field(field, value) {
        if($('[name="' + field + '"][type="radio"]').length) {
            $('[name="' + field + '"][value="' + value + '"]').prop("checked", true);
        } else if($('select[name="' + field + '"]').length) {
            $('[name="' + field + '"] [value="' + value + '"]').prop("selected", true);
        } else {
            $('[name="' + field + '"]').val(value);
        }
    }

    function get_field(field) {
        if($('[name="' + field + '"][type="radio"]').length) {
            return $('[name="' + field + '"]:checked').val();
        } else if($('select[name="' + field + '"]').length) {
            return $('[name="' + field + '"] option:selected').val();
        } else {
            return $('[name="' + field + '"]').val();
        }
    }

    function get_field_integer(field) {
        var field_text = $('[name="' + field + '"]').val();
        var field_setting = parseInt(field_text, 10);
        if(!(field_setting > 0 || field_setting < 0 || field_text === "0"))
            throw field_text + " is not an integer";

        return field_setting;
    }

    // set #lds_editor_iframe[presave=true] in the ILDE iframe
    window.addEventListener('message', function (event) {
        console.log("message received");
        console.log(event);
        if (event.data.type == 'ldshake_presave') {
            var save_button_array = document.querySelector('[name="authoringIFrame1-frame"]').contentWindow.document.querySelectorAll('img[class="submit"][src="/images/stock-apply.png"]');
            ldshake_event_save_data = event.data;

            //ajax save
            post_process_data();
            var form_element = document.querySelector("form"));
            var form_object = FormData(form_element);

            $.post('ldshake.php', form_object, function() {
                ldshakeSendSaveReadyMessage(ldshake_event_save_data);
                });
        }
    }, false);

    function ldshakeSendSaveReadyMessage(data) {
        if(!ldshake_event_save_data) return;

        top.postMessage({type: 'ldshake_restapi_presave_ready'}, data.params.ldshake_origin);
        ldshake_event_save_data = null;
        console.log("exe: save_ready");
    }

</script>
</body>
</html>