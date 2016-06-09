<?php
session_start();
include('dbvar.php');

function non_valid() {
    header("location: logout.php");
}

if(!isset($_SESSION['user']) or !isset($_REQUEST['activity'])) {
    non_valid();
} else {
    $activity = (int)$_REQUEST['activity'];

    if (empty($activity))
        non_valid();

    $uname = mysqli_real_escape_string($link, $_SESSION['user']);
    $query_result = mysqli_query($link, "select * from activity where uid = '$uname' and id = $activity limit 1 ");

    if (!(mysqli_num_rows($query_result) > 0))
        non_valid();

    $row = mysqli_fetch_assoc($query_result);

    $data = json_decode($row);

    if ($data)
        non_valid();

}

    header('Content-Type: text/html; charset=utf-8');
?><!DOCTYPE html>
<html lang="en">
<head>
    <title>PyramidConfigurations</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
    <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <link rel="stylesheet" type="text/css" href="elements/resources/css/teacher/styles.css">
</head>
<body>
<div id="center-frame">
<div id="pyramid-levels-2" class="pyramid-animation" style="position: relative;float: left;margin-left: 15px;">
    <svg viewBox="70 5 280 210">
        <polygon points="210,5 350,210 70,210" style="fill:pink;stroke:purple;stroke-width:2" />
        <circle cx="120" cy="175" r="12" stroke="green" stroke-width="2" fill="yellow" />
        <circle cx="180" cy="175" r="12" stroke="green" stroke-width="2" fill="yellow" />
        <circle cx="240" cy="175" r="12" stroke="green" stroke-width="2" fill="yellow" />
        <circle cx="300" cy="175" r="12" stroke="green" stroke-width="2" fill="yellow" />
        <line x1="112" y1="150" x2="308" y2="150" style="stroke:rgb(255,0,155);stroke-width:4" />

        <text x="120" y="205" fill="red">Level 1 – Individual level</text>
        <text x="55" y="140" fill="red" transform="rotate(-52,50,60)">Rating level(s)</text>

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

<div id="pyramid-levels-3" class="pyramid-animation" style="position: relative;float: left;margin-left: 15px;">
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

        <text x="55" y="140" fill="red" transform="rotate(-52,50,60)">Rating level(s)</text>

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

<div id="pyramid-levels-4" class="pyramid-animation" style="position: relative;float: left;margin-left: 15px;">
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

        <text x="55" y="140" fill="red" transform="rotate(-52,50,60)">Rating level(s)</text>

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

<div id="popup" style="/*display:none; */position: relative;float: left;width:500px;height:500px;">
    <h4>Pyramid Configurations</h4>
    <form id="myform">
        <div class="ui-field-contain ui-mini">
            <label for="slider-s">No. of students per group at rating level 1:<a href="#popupInfo" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                <div data-role="popup" id="popupInfo" class="ui-content" data-theme="a" style="max-width:700px;">
                    <p>This specifies the initial group size at level 2 (first rating level) after option submission level. This size will be doubled when groups propagate to upper levels.</p>
                </div>
            </label>
            <input type="range" name="slider-s" id="slider-s" value="3" min="2" max="10" data-highlight="true">
        </div>

        <div class="ui-field-contain">
            <label for="slider-s2">No. of levels:<a href="#popupInfo5" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                <div data-role="popup" id="popupInfo5" class="ui-content" data-theme="a" style="max-width:700px;">
                    <p>This includes both option submission level and rating levels. It is recommended to have 3 to 4 levels for active participation.</p>
                </div></label>
            <input type="range" name="slider-s2" id="slider-s2" value="3" min="2" max="4" data-highlight="true">
        </div>

        <div class="ui-field-contain">
            <label for="flip-1">Allow multiple pyramids:<a href="#popupInfo4" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                <div data-role="popup" id="popupInfo4" class="ui-content" data-theme="a" style="max-width:700px;">
                    <p>If your class is relatively large class, it would be better to enable this feature, so several pyramids will be created and students will be automatically allocated.</p>
                </div></label>
            <select name="flip-1" id="flip-1" data-role="slider">
                <option value="off">No</option>
                <option value="on" selected="selected">Yes</option>
            </select>
        </div>

        <div class="ui-field-contain">
            <label for="minInfo">Minimum students per pyramid:<a href="#popupInfo3" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                <div data-role="popup" id="popupInfo3" class="ui-content" data-theme="a" style="max-width:700px;">
                    <p>Number of students allowed to be grouped into a single pyramid. Based on the total number of students and this value, several pyramids may require and it will be automatically suggested by the system.</p>
                </div></label>
            <input type="number" name="minInfo" id="minInfo" value="12" data-clear-btn="true">
        </div>

        <div class="ui-field-contain">
            <label for="flip-1">Discussion :<a href="#popupInfo6" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" title="More info">More</a>
                <div data-role="popup" id="popupInfo6" class="ui-content" data-theme="a" style="max-width:700px;">
                    <p>If discussion is enabled, students can chat with peers to clarify and negotiate their options during rating phases.</p>
                </div></label>
            <select name="flip-2" id="flip-2" data-role="slider">
                <option value="off">No</option>
                <option value="on" selected="selected">Yes</option>
            </select>
        </div>

        <div class="ui-input-btn ui-btn ui-btn-inline ui-corner-all">
            Create<input name="create" type="submit" data-enhanced="true" value="Create">
        </div>
    </form>

    <!--<div class="ui-field-contain">-->
    <a href="#popupAdvanced" data-rel="popup" data-position-to="window" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-icon-check ui-btn-icon-left ui-btn-a" data-transition="pop">Advanced Settings</a>
    <div data-role="popup" id="popupAdvanced" data-theme="a" class="ui-corner-all">

        <form>
            <div style="padding:10px 20px;">
                <h4>Advanced Pyramid Configurations</h4>
                <h5>It is optional to change these default values.</h5>

                <div id="pop-background"></div>

                <label for="optimer">Option submission timer:<a text-data="#cpopup1" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext"
                                                                title="This timer specifies the time permitted for initial option (artifact) submission for students">More</a>
                    <div id="cpopup1-text" class="ui-content tooltip-popup" data-theme="a" style="display:none">
                        <p>If discussion is enabled, students can chat with peers to clarify and negotiate their options during rating phases.</p>
                    </div>
                </label>
                <div style="position:relative;float:left;">
                    <input type="number" name="optimer" id="optimer" value="" data-clear-btn="true" data-wrapper-class="numk" />
                </div>

                <div style="position:relative;float:left; margin-left:10px; margin-top:2px;">
                    <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
                        <input type="radio" name="radio-choice-h-2" id="radio-choice-h-2a" value="on" checked="checked">
                        <label for="radio-choice-h-2a">Minutes</label>
                        <input type="radio" name="radio-choice-h-2" id="radio-choice-h-2b" value="off">
                        <label for="radio-choice-h-2b">Hours</label>
                        <input type="radio" name="radio-choice-h-2" id="radio-choice-h-2c" value="other">
                        <label for="radio-choice-h-2c">Days</label>
                    </fieldset></div>
                <div style="clear:both;"></div>

                <label for="hardtimer">Option submission hard timer:<a href="#popup2" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext"
                                                                       title="This timer specifies the maximum time permitted for initial option (artifact) submission for students. Once expired, every student will be promoted next level."></a>
                </label>
                <div style="position:relative;float:left;">
                    <input type="number" name="hardtimer" id="hardtimer" data-wrapper-class="numk" value="" data-clear-btn="true" />
                </div>

                <div style="position:relative;float:left; margin-left:10px; margin-top:2px;">
                    <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
                        <input type="radio" name="radio-choice-h2-2" id="radio-choice-h2-2a" value="on" checked="checked">
                        <label for="radio-choice-h2-2a">Minutes</label>
                        <input type="radio" name="radio-choice-h2-2" id="radio-choice-h2-2b" value="off">
                        <label for="radio-choice-h2-2b">Hours</label>
                        <input type="radio" name="radio-choice-h2-2" id="radio-choice-h2-2c" value="other">
                        <label for="radio-choice-h2-2c">Days</label>
                    </fieldset></div>
                <div style="clear:both;"></div>

                <label for="ratimer">Rating timer:<a href="#popup3" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext"
                                                     title="This timer specifies the time permitted for rating at each level including discussion time."></a>
                </label>
                <div style="position:relative;float:left;">
                    <input type="number" name="ratimer" id="ratimer" data-wrapper-class="numk" value="" data-clear-btn="true">
                </div>

                <div style="position:relative;float:left; margin-left:10px; margin-top:2px;">
                    <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
                        <input type="radio" name="radio-choice-h3-2" id="radio-choice-h3-2a" value="on" checked="checked">
                        <label for="radio-choice-h3-2a">Minutes</label>
                        <input type="radio" name="radio-choice-h3-2" id="radio-choice-h3-2b" value="off">
                        <label for="radio-choice-h3-2b">Hours</label>
                        <input type="radio" name="radio-choice-h3-2" id="radio-choice-h3-2c" value="other">
                        <label for="radio-choice-h3-2c">Days</label>
                    </fieldset></div>
                <div style="clear:both;"></div>

                <label for="rahardtimer">Rating hard timer:<a href="#popup4" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext"
                                                              title="This is the maximum time allowed for rating and discussion at each level. Once expired everyone is promoted to next level."></a>
                </label>
                <div style="position:relative;float:left;">
                    <input type="number" name="rahardtimer" id="rahardtimer" data-wrapper-class="numk" value="" data-clear-btn="true">
                </div>

                <div style="position:relative;float:left; margin-left:10px; margin-top:2px;">
                    <fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
                        <input type="radio" name="radio-choice-h4-2" id="radio-choice-h4-2a" value="on" checked="checked">
                        <label for="radio-choice-h4-2a">Minutes</label>
                        <input type="radio" name="radio-choice-h4-2" id="radio-choice-h4-2b" value="off">
                        <label for="radio-choice-h4-2b">Hours</label>
                        <input type="radio" name="radio-choice-h4-2" id="radio-choice-h4-2c" value="other">
                        <label for="radio-choice-h4-2c">Days</label>
                    </fieldset></div>
                <div style="clear:both;"></div>

                <label for="slider-s3">Satisfaction percentage:<a href="#popup5" data-rel="popup" data-transition="pop" class="my-tooltip-btn ui-btn ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext"
                                                                  title="When this percentage is reached, students will be promoted for the next level. This is important when longer timer values are defined at MOOC scenarios with less participation."></a>
                </label>
                <input type="range" name="slider-s3" id="slider-s3" value="60" min="30" max="100" data-highlight="true">

                <button type="submit" class="ui-btn ui-corner-all ui-shadow ui-btn-b ui-btn-icon-left ui-icon-check">Submit</button>
            </div>
        </form>
    </div>
    <!--</div>-->
</div>

<div style="clear: both;"></div>

<script>
    var e = document.querySelector('#click-circle1');
    e.addEventListener('click', function(event){
        var e = document.querySelector('#popup');
        //e.setAttribute('style', 'display:block;');
        e.style.display = 'block';
        // alert('OK');
    });

    var e = document.querySelector('#click-circle2');
    e.addEventListener('click', function(event){
        var e = document.querySelector('#popup');
        e.style.display = 'block';
        // e.setAttribute('style', 'display:block;');
    });

    //number of levels

    $(document).on('pageinit', function() {
        $('#slider-s').on('slidestop', function(event) {
            var n_students = parseInt($('#slider-s').val(), 10);

            var radius = Math.pow(n_students,1/10) / Math.pow(3,1/10) * 17;
            $('.level1 circle').attr('r', radius);
        });

        $('#slider-s2').on('slidestop', function(event) {
            var pyramid_number = $('#slider-s2').val();

            $('.pyramid-animation').hide();
            $('#pyramid-levels-' + pyramid_number).show();
        });
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


</script>
</div>
</body>
</html>


