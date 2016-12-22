<?php
global $node_path, $fid, $pid, $flow_data, $url;
//obtain the flows pertaining to the current teacher

   $parsed_url = parse_url($url);
$url = 'https://'.$parsed_url['host'].$parsed_url['path'];

$flow_data = [];
$flows = [];

$flow_query = mysqli_query($link, "select * from flow where fid = '{$fid}' and teacher_id = '$teacher_id'");
if(mysqli_num_rows($flow_query) > 0) {
$flow_query_row = mysqli_fetch_assoc($flow_query);
$flow_query_row['id'] = $flow_query_row["fid"];
$flow_query_row['name'] = $flow_query_row["fname"];
$flows[] = $flow_query_row;
}

$flow = $flow_query_row;
$n_groups = floor($flow['pyramid_size']/$flow['nostupergrp']);
if($flow['multi_py'])
$n_pyramids = floor($flow['expected_students']/$flow['pyramid_size']);
else
$n_pyramids = 1;

date_default_timezone_set("Europe/Berlin");
\Flow\set_fid($fid);
//multiple async pyramids info
if($flow['multi_py'] and $flow['sync'] == 0) {
    $unfilled_pyramids = \Flow\get_not_full_pyramids();
    $last_expired_timestamp = \Flow\get_last_pyramid_expired_timestamp();
    $last_expired_timestamp_data = date("l jS G:i", $last_expired_timestamp);
    $flow_timestamps = \Flow\get_timestamps();
    $question_submit_expiry_timestamp = $flow_timestamps[0];
    $question_submit_expiry_timestamp_date = date("l jS G:i", $question_submit_expiry_timestamp);

    if(empty($unfilled_pyramids)) {
        $available_students = \Flow\get_available_students();
    }
}

//obtain the data for every flow
foreach($flows as $flow) {

$fid = $flow['id'];

//find the last pid for the current flow
$npid = null;
$result = mysqli_query($link,"select * from pyramid_students where fid='$fid' order by pid desc limit 1");

if(!mysqli_num_rows($result)) {
$npid = null;
break;
}
else {
$result_row = mysqli_fetch_assoc($result);
$npid = (int)$result_row['pid'];
}


//obtain the data for every pyramid in the current flow
$pyramid_data = [];
$final_answers = false;
for($i=0;$i<=$npid;$i++) {

$pyramid_query = mysqli_query($link, "select fname, pg_level, pg_group, pg_group_id, pg_pid from pyramid_groups, flow where pg_fid = fid and pg_fid = '$fid' and pg_pid = '{$i}' order by pg_level ASC, pg_group_id ASC");
if(mysqli_num_rows($pyramid_query) > 0) {
$py_created = true;
} else {
$py_created = false;
}

if(!$py_created) {
$pyramid_data[$i] = null;
continue;
} else {

}

//check if the pyramid has finished with the desired number of answers and get the final answers
if (mysqli_num_rows(mysqli_query($link, "select * from pyramid_groups where pg_fid = '$fid' and pg_pid = '{$i}'")) == mysqli_num_rows(mysqli_query($link, "select * from selected_answers where sa_fid = '$fid' and sa_pid = '{$i}'"))) {

$pyramid_level_answers = mysqli_query($link, "select * from flow where fid = '$fid'");
$pyramid_level_answers_row = mysqli_fetch_assoc($pyramid_level_answers);
$activity_level = $pyramid_level_answers_row['levels'];
$activity_level = $activity_level - 1;
$result_11 = mysqli_query($link, "select * from selected_answers where sa_fid = '$fid' and sa_pid = '{$i}' and sa_level = '$activity_level' and skip = 0");

while ($data_t_11 = mysqli_fetch_assoc($result_11)) {
$qa_last_selected_id = $data_t_11['sa_selected_id'];
$result_12 = mysqli_query($link, "select * from flow_student where fid = '$fid' and sid = '$qa_last_selected_id'");
$data_t_12 = mysqli_fetch_assoc($result_12);
$pyramid_data[$i]['answers'][] = $data_t_12['fs_answer'];
}

$final_answers = true;
} else {
$pyramid_data[$i]['finished'] = false;
}

$count = 0;
$count2 = 0;
$count3 = 0;
$grp_cnt = 0;
//pyramid levels data
while ($pyramid_query_row = mysqli_fetch_assoc($pyramid_query)) {
$flow_name = $pyramid_query_row["fname"];
$level = $pyramid_query_row["pg_level"];
$group = $pyramid_query_row["pg_group"];
$pg_group_id = $pyramid_query_row["pg_group_id"];
$answer = null;

//rated questions and score
$rated_questions_sql = <<<SQL
    select * from (
    select (select fs_answer from flow_student
    where fid = fsr_fid and sid = fsr_to_whom_rated_id)
    as answer, 
    (select fs_id from flow_student
    where fid = fsr_fid and sid = fsr_to_whom_rated_id)
    as `id`,
    sum(fsr_rating) as rating
    from flow_student_rating
    where fsr_fid = {$fid} and fsr_pid = {$i} and fsr_level = {$level} and fsr_group_id = {$pg_group_id} and fsr_to_whom_rated_id <> -1 and skip = 0
group by fsr_to_whom_rated_id
) as rated_answers
order by rating desc, `id` desc
SQL;

$ranking = [];
$rated_questions = mysqli_query($link, $rated_questions_sql);
while($rated_questions_row = mysqli_fetch_assoc($rated_questions)) {
$ranking[] = $rated_questions_row;
}

//there is a winning answer
$pyramid_level_answers = mysqli_query($link, "select * from selected_answers where sa_fid = '$fid' and sa_pid = '{$i}' and sa_level='$level' and sa_group_id='$pg_group_id' and skip = 0");
if (mysqli_num_rows($pyramid_level_answers) > 0) { //the group has selected an answer

while($pyramid_level_answers_row = mysqli_fetch_assoc($pyramid_level_answers)) {
$selected_id = $pyramid_level_answers_row['sa_selected_id'];

//get the answer text
$res4 = mysqli_query($link, "select * from flow_student where fid = '$fid' and sid = '$selected_id'");
if (mysqli_num_rows($res4) > 0) {
$data4 = mysqli_fetch_assoc($res4);
$answer = $data4['fs_answer'];

}
}
}
$pyramid_data[$i]['levels'][$level]['groups'][] = [
'members' => $group,
'answer' => mb_strimwidth($answer, 0, 20, '...', 'UTF-8'),
'ranking' => $ranking
];
}
}

$flow_data = $pyramid_data;
}

$flow = $flow_query_row;
if($npid === null) {
$pyramid = [];
for($i=0;$i<$flow['levels'];$i++) {
$groups = [];
for($j=0; $j < floor($n_groups/pow(2, $i)); $j++) {
$groups['groups'][] = [];
}
$pyramid['levels'][] = $groups;
}

for($y=0; $y<$n_pyramids; $y++) {
$flow_data[] = $pyramid;
}
}

$flow = $flow_data;

//\Pyramid\set_current_flow($fid);
\Flow\set_fid($fid);
\Pyramid\set_pid(0);
\Util\sql_gen();

//retrieve global flow last keys
$last_flow_keys = \Flow\get_flow_status();
$flow_properties = $flow_data;

//pyramid creation timestamp
$answer_timeout_data = \Answer\get_answer_timeout();
$pyramid_creation_timestamp = (int)$answer_timeout_data['start_timestamp'];

$pyramid_groups = \Pyramid\get_groups();
$pyramid_results = \Pyramid\get_results();
$users_with_groups = \Group\get_users_with_groups();
$groups_activity = [];

//obtain activity per group
foreach($pyramid_groups as $pyramid_groups_item) {
    $group_level = $pyramid_groups_item['group_level'];
    $group_id = $pyramid_groups_item['group_id'];
    $group_activity = \Group\get_group_activity($group_level, $group_id);

    $groups_activity['levels'][$group_level]['groups'][$group_id] = $group_activity;
}

$students_details = [];
foreach($users_with_groups as $k_sid => &$users_with_groups_item) {
    $users_with_groups_item['details'] = \Student\get_student_details($k_sid);
}

$pyramid_item[] = [
    'pyramid_creation_timestamp' => $pyramid_creation_timestamp,
    'users_with_groups' => $users_with_groups,
    'levels' => $groups_activity['levels'],
    'results' => $pyramid_results,
];

$current_flow_status = [
    'last_flow_keys' => $last_flow_keys,
    'flow_properties' => $flow_properties,
    'pyramid_data' => $pyramid_item,
];

$context = "global";

$item = "group";
$pyramid_template[$context][$item] = <<< HTML
<div class="activity-pyramid-level-group-block">
    <a class="{$context}-{$item} ui-btn ui-corner-all" data-rel="popup"></a>
    <ul class="selected-answers"></ul>
</div>
HTML;

$item = "selected-answer";
$pyramid_template[$context][$item] = <<< HTML
<li class="activity-pyramid-level-group-selected-answer">
    <span class="answer selected-answer"></span>
</li>
HTML;

$item = "available-student";
$pyramid_template[$context][$item] = <<< HTML
<tr class="activity-answer">
    <td class="username"><a></a></td>
    <td class="answer"></td>
    <td class="skip"></td>
    <td class="pyramid"></td>
</tr>
HTML;

$item = "level";
$pyramid_template[$context][$item] = <<< HTML
<div class="{$context}-{$item} activity-pyramid-level-block">
    <div class="{$context}-{$item}-name"></div>
</div>
HTML;

$item = "pyramid";
$pyramid_template[$context][$item] = <<< HTML
<div class="{$context}-{$item}">
    <div class="{$context}-{$item}-name name"></div>
    <div class="{$context}-{$item}-results results"></div>
</div>
HTML;

$item = "header-pyramid";
$pyramid_template[$context][$item] = <<< HTML
<li class="{$context}-{$item}">
    <div class="{$context}-{$item}-name name"></div>
    <ul class="{$context}-{$item}-results results"></ul>
</li>
HTML;

$item = "winning-answer-summary";
$pyramid_template[$context][$item] = <<< HTML
<li class="{$context}-{$item}">
    <ul class="{$context}-{$item}-winning-answer winning-answer"></ul>
</li>
HTML;

$context = "detail";
$item = "group";
$pyramid_template[$context][$item] = <<< HTML
<div class="{$context}-{$item} disabled">
    <div class="{$context}-{$item}-label label"></div>
    
    <div class="{$context}-{$item}-users-label">Participants</div>
    <ul class="{$context}-{$item}-users users"></ul>
    
    <div class="{$context}-{$item}-ratings-label disabled">Rating table</div>
    <ul class="{$context}-{$item}-ratings ratings"></ul>
    
    <div class="{$context}-{$item}-messages-label disabled">Discussion</div>
    <ul class="{$context}-{$item}-messages messages"></ul>
    

</div>
HTML;

$item = "group-user";
$pyramid_template[$context][$item] = <<< HTML
<li class="{$context}-{$item}">
    <div class="{$context}-{$item}-name"></div>
</li>
HTML;

$item = "user";
$pyramid_template[$context][$item] = <<< HTML
<div class="{$context}-{$item} disabled">
    <div class="{$context}-{$item}-username"></div>
    <div class="margin-icon ui-icon-plus ui-btn-icon-notext"></div>
    <div class="{$context}-{$item}-answer"></div>
    <div style="clear:both;"></div>
    <div class="{$context}-{$item}-answer-skip"></div>
    <div class="{$context}-{$item}-answer-date"></div>
    
    <div class="{$context}-{$item}-levels"></div>

</div>
HTML;

$item = "user-level";
$pyramid_template[$context][$item] = <<< HTML
<div class="{$context}-{$item}">
    <div class="level-label"></div>
    <div class="level-absent"></div>
    <div class="level-discussion-label disabled">Discussion</div>
    <ul class="{$context}-{$item}-messages messages"></ul>
    <ul class="{$context}-{$item}-ratings ratings"></ul>
</div>
HTML;

$item = "user-rating";
$pyramid_template[$context][$item] = <<< HTML
<li class="{$context}-{$item}">
    <div class="table-wrapper rating-wrapper">
        <div class="{$context}-{$item}-ratings rating"></div>
    </div>
    <div class="{$context}-{$item}-messages answer"></div>
    <div style="clear:both;"></div>
</li>
HTML;

$item = "group-rating";
$pyramid_template[$context][$item] = <<< HTML
<li class="{$context}-{$item}">
    <div class="table-wrapper rating-wrapper">
        <div class="{$context}-{$item}-ratings rating"></div>
    </div>
    <div class="{$context}-{$item}-messages answer"></div>
    <div style="clear:both;"></div>
</li>
HTML;

$item = "rating";
$pyramid_template[$context][$item] = <<< HTML
<div class="{$context}-{$item}">
    <div class="{$context}-{$item}-answer"></div>
    <div class="{$context}-{$item}-rating"></div>
</div>
HTML;

$item = "message";
$pyramid_template[$context][$item] = <<< HTML
<li class="{$context}-{$item} message">
    <span class="{$context}-{$item}-username username"></span>
    <span class="{$context}-{$item}-message message"></span>
</li>
HTML;



header('Content-Type: text/html; charset=utf-8');
    global $url;
?><!DOCTYPE html>
<html lang="en">
<head>
    <title>ViewPyramidPage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
    <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <script src="../../lib/js/activity_tracking.js"></script>
    <link rel="stylesheet" type="text/css" href="elements/resources/css/teacher/styles.css">
    <script>
        var current_flow_status;// = <?=json_encode($current_flow_status);?>;

        var pyramid_template = <?=json_encode($pyramid_template)?>;
        var navigation_flow = [];
        navigation_flow.push({
            "id" : '#global-pyramid'
        });


        function pyramid_hide(nav) {
            $(nav[nav.length-1].id).hide();
        }

        function show_page(nav) {
            $(nav[nav.length-1].id).show();
        }

        function global_group_click(event) {
            event.preventDefault();
            var id = $(event.target).attr("href");

            pyramid_hide(navigation_flow);
            $(id).show();
            navigation_flow.push({
               "id" : id
            });

            if(navigation_flow.length > 1) {
                $('#back-button').show();
            }
        }

        $(function() {
            //pyramid_status.init.start();
            //$('[data-role="popup"]').popup();
            $('#back-button').click(function() {
                var item = navigation_flow.pop();
                $(item.id).hide();

                if(navigation_flow.length <= 1) {
                    $('#back-button').hide();
                }

                show_page(navigation_flow);
            });

            /*$('#available-students-button').click(function() {
                pyramid_hide(navigation_flow);
                $('#available-students').show();

                navigation_flow.push({
                    "id" : '#available-students'
                });
            });*/

            $('#available-students-button').click(global_group_click);
            update_pyramid_data();
        });

        var pyramid_app_url = <?=json_encode($url)?>;

        function update_pyramid_data() {
            $.ajax(pyramid_app_url + 'activities_ajax.php?ldshake_fid=<?=$fid?>', {
                "success": function (data) {
                    console.log(data);
                    current_flow_status = data;
                    pyramid_status.init.start();
                    //$('[data-role="popup"]').popup();
                },
                "complete": function (jqXHR, status) {
                    setTimeout(update_pyramid_data, 10000);
                },
                "timeout": 15000,
                "dataType": "json",
                "method": "POST"
            });
        }


    </script>
    <!--<script src="https://cdn.socket.io/socket.io-1.3.7.js"></script>
    <script src="lib/actions.js"></script>
    <script type="text/javascript">
        var socket = io({multiplex : false, 'reconnection': true,'reconnectionDelay': 3000,'maxReconnectionAttempts':Infinity, path: '/<?=$node_path?>/'});
    </script>
-->
    <style>
        @font-face{
            font-family: 'Glyphicons Halflings';
            src: url('<?=$url?>vendors/fonts/glyphicons-halflings-regular.eot');
            src: url('<?=$url?>vendors/fonts/glyphicons-halflings-regular.eot') format('embedded-opentype'),
            url('<?=$url?>vendors/fonts/glyphicons-halflings-regular.woff2') format('woff2'),
            url('<?=$url?>vendors/fonts/glyphicons-halflings-regular.woff') format('woff'),
            url('<?=$url?>vendors/fonts/glyphicons-halflings-regular.ttf') format('truetype'),
            url('<?=$url?>vendors/fonts/glyphicons-halflings-regular.svg') format('svg');
        }

        .activity-pyramid-level-group-selected-answer,
        #available-students .answer,
        #available-students .username {
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }

        .activity-pyramid-level-group-block .selected-answers {
            padding: 0;
        }

        .global-header-pyramid-results {
            list-style: none;
        }

        .winning-answers {
            list-style: none;
            padding: 10px;
            border-left: solid #00f529 2px;
            background-color: #d7ffd0;
        }

        .global-winning-answer-summary-winning-answer {
            display: inline-block;
            padding: 0px;
        }

        .global-header-pyramid-results > li:before {
            font-family: 'Glyphicons Halflings';
            content:"\e084";
            position: relative;
            top: 3px;
            margin-right: 5px;
        }

        .global-pyramid {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .global-pyramid-name {
            font-size: 30px;
            background-color: #efefef;
            border-radius: 30px;
            padding: 0.2em;
            text-align: center;
            height: 40px;
            width: 40px;
        }

        .activity-answer {
            display: table-row;
        }

        .activity-answer > div {
            display: table-cell;
            width: 20%;
        }

        .disabled {
            display: none;
        }

        .table-wrapper {
            display: table;
        }

        .rating-wrapper {
            float: left;
            width: 1.5em;
            height: 1.5em;
            line-height: 1.5em;
        }

        .detail-user-rating,
        .detail-group-rating {
            list-style-type: none;
            margin-bottom: 0.5em;
        }

        li.detail-group-rating {
            min-width: 600px;
        }

        .detail-user-rating .rating,
        .detail-group-rating .rating {
            display: table-cell;
            background-color: #000;
            color: #fff;
            text-align: center;
            vertical-align: middle;
            border-radius: 1.5em;
        }

        .detail-user-rating .answer,
        .detail-group-rating .answer {
            display: block;
            padding-left: 2em;
            line-height: 1.5em;
        }

        .answer-skipped, .no-question {
            color: red;
        }

        .margin-icon {
            position:relative;
            display: inline-block;
            bottom: 5px;
            float:left;
        }

        .detail-user-answer {
            display: block;
            padding-left: 28px;
        }

        .detail-user-username {
            background: linear-gradient(to right, #f0f9ff 0%,#eff9ff 47%,#e5f5ff 100%);
            padding: 0.5em;
            font-weight: bold;
        }

        .activity-pyramid-level-block {
            text-align: center;
        }

        .activity-pyramid-level-group-block a {
            height: 100px;
            width: 100px;
            vertical-align: middle;
            display: table-cell;
            font-size: 200%;
            padding: 0.2em;
        }

        .ranking-position {
            width: 10px;
        }

        .ranking-text {
            width: 400px;
        }

        .ranking-position,
        .ranking-text {
            float: left;
            min-width: 20px;
        }

        .ranking-score {
            float: right;
            min-width: 20px;
        }

        .group-popup {
            width: 500px;
            padding: 20px;
        }

        .group-popup ul {
            list-style-type: none;
        }

        .activity-pyramid-level-group-block {
            width: 114px;
            margin: 0 30px 15px 30px;
            display: inline-block;
        }

    .activity-pyramid-group-block-list {
        width: 800px;
        margin: 0 auto 40px auto;
        text-align: center;
    }

        #available-students {
            border-spacing: 3px;
            margin: 0 auto 40px auto;
        }

        #available-students .username {
            max-width: 280px;
            background-color: #e2e2e2;
        }

        #available-students .answer {
            max-width: 400px;
            width: 400px;
            background-color: #efefef;
        }

        #available-students .skip {
            max-width: 20px;
            background-color: #e2e2e2;
        }

        #available-students .pyramid {
            width: 80px;
            background-color: #efefef;
        }

        #waiting-next-pyramid {
            font-size: 120%;
            text-align: center;
            background-color: #fffac9;
            padding: 10px;
            border-radius: 15px;
        }
    </style>
</head>
<body>


<input name="page" type="hidden" value="activity_tracking"/>
<input name="username" type="hidden" value="<?=htmlspecialchars($teacher_id)?>"/>
<div data-role="page">
    <div data-role="header">
        <h1>View Pyramid details</h1>
    </div>
    <div data-role="main" class="ui-content">

        <button id="back-button" class="disabled">Back</button>

        <div id="global-pyramid">
            <button id="available-students-button" href="#available-students">Student list</button>
            <div id="winning-answer-summary" class="disabled">
                <div class="winning-event-label">There are some winning submissions</div>
                <ul class="winning-answers"></ul>
            </div>
            <div id="multi_async_status_html"></div>
            <div id="waiting-next-pyramid"><span class="available"></span> available student(s), <span class="required"></span> required to create the next pyramid</div>
            <div id="flow-frame"></div>
        </div>

        <div id="detail-frame"></div>
        <div id="user-detail-frame"></div>
        <table id="available-students" class="disabled"><thead>
            <th>username</th>
            <th>question</th>
            <th>skip</th>
            <th>pyramid</th>
            </thead><tbody></tbody>
        </table>


        <div id="activity-info-main" class="disabled">

            <?php if(isset($final_answers)):?>
                <div id="activity-winning-answers-block">
                    <div id="activity-winning-answers-block-title"><?=TS("Most popular options:")?></div>
                    <?php foreach($flow as $pkey => $pyramid):?>
                        <?php if(isset($pyramid['answers'])):?>
                            <div class="activity-winning-answers-block-pyramid-block">
                                <div class="activity-winning-answers-block-pyramid-number"><?=TS("Pyramid") . ' ' . ($pkey+1)?></div>

                                <ul class="activity-winning-answers-block-pyramid-list">
                                    <?php foreach($pyramid['answers'] as $answer):?>
                                        <li class="activity-winning-answers-block-pyramid-list-item"><?=htmlspecialchars($answer)?></li>
                                    <?php endforeach ;?>
                                </ul>
                            </div>
                        <?php endif;?>
                    <?php endforeach; ?>
                </div>
            <?php endif;?>

            <?php if(isset($unfilled_pyramids)):?>
                <?php if(count($unfilled_pyramids)):?>
                    <div id="activity-pyramid-state-block">
                        <div id="activity-pyramid-state-block-title"><?=TS("Pyramid state:")?></div>
                        <ul class="activity-pyramid-state-block-pyramid-list">
                            <li class="activity-pyramid-state-block-pyramid-list-item">Submission will start at <?=date("l jS G:i", $last_expired_timestamp)?></li>
                            <?php foreach($unfilled_pyramids as $pkey => $unfilled_pyramid):?>
                                <li class="activity-pyramid-state-block-pyramid-list-item">Pyramid <?=($unfilled_pyramid['pid'] + 1)?> remaining slots <?=($unfilled_pyramid['slots'])?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php else:?>
                    <div id="activity-pyramid-state-block">
                        <ul class="activity-pyramid-state-block-pyramid-list">
                            <?php if(!(empty($available_students) and $npid === null)):?>
                                <li class="activity-pyramid-state-block-pyramid-list-item">Submission started at <?=date("l jS G:i", $last_expired_timestamp)?></li>
                                <li class="activity-pyramid-state-block-pyramid-list-item">Submission will end at <?=date("l jS G:i", $question_submit_expiry_timestamp)?></li>
                                <li class="activity-pyramid-state-block-pyramid-list-item">Available students <?=implode(', ', $available_students)?></li>
                            <?php else:?>
                                <li>Waiting for the first student.</li>
                            <?php endif;?>
                        </ul>
                    </div>
                <?php endif;?>
            <?php endif;?>

            <?php foreach($flow as $pkey => $pyramid): ?>
                <div class="activity-pyramid-block">

                    <?php foreach($pyramid['levels'] as $lkey => $level):?>
                        <div class="activity-pyramid-level-block">
                            <div class="activity-pyramid-group-block-number"><b>Pyramid <?=($pkey+1)?> level <?=($lkey+2)?></b></div>

                            <div class="activity-pyramid-group-block-list">
                                <?php foreach($level['groups'] as $gkey => $group):?>
                                    <div class="activity-pyramid-level-group-block">
                                        <a href="#grp<?=($pkey+1)?><?=$lkey?><?=($gkey+1)?>" data-rel="popup" class="ui-btn ui-corner-all">Group <?=($gkey+1)?> </a>
                                        <div data-role="popup" id="grp<?=($pkey+1)?><?=$lkey?><?=($gkey+1)?>" data-theme="a" class="group-popup ui-corner-all">
                                            <div class="popup-members-title"><b><?=TS("Members")?></b></div>
                                            <ul class="popup-members-list">
                                                <?php $members = explode(',', $group['members']);?>
                                                <?php foreach($members as $mkey => $member):?>
                                                    <li><?=$member?></li>
                                                <?php endforeach; ?>
                                            </ul>

                                            <div class="ranking">
                                                <div class="popup-ranking-title"><b><?=TS("Ranking")?></b></div>
                                                <ul>
                                                    <?php $i=0; foreach($group['ranking'] as $ar):?>
                                                        <li><div class="ranking-position"><?=(($i++)+1)?></div><div class="ranking-text"><?=htmlspecialchars($ar['answer'])?></div><div class="ranking-score"><?=$ar['rating']?></div><div style="clear:both"></div></li>
                                                    <?php endforeach;?>
                                                </ul>
                                            </div>
                                        </div>
                                        <?=htmlspecialchars($group['answer'])?>
                                    </div>
                                <?php endforeach;?>
                                <div style="clear:both"></div>
                            </div>
                        </div>
                    <?php endforeach;?>
                </div>
            <?php endforeach;?>
        </div>
    </div>
</div>

</body>
</html>