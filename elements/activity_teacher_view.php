<?php
global $node_path, $fid, $pid, $flow_data;
//obtain the flows pertaining to the current teacher

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

//multiple async pyramids info
if($flow['multi_py'] and $flow['sync'] == 0) {
$unfilled_pyramids = \Flow\get_not_full_pyramids();
$last_expired_timestamp = \Flow\get_last_pyramid_expired_timestamp();

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

//rated questions and score
$rated_questions_sql = <<<SQL
    select * from (
    select (select fs_answer from flow_student
    where fid = fsr_fid and pid = fsr_pid and sid = fsr_to_whom_rated_id)
    as answer, sum(fsr_rating) as rating
    from flow_student_rating
    where fsr_fid = {$fid} and fsr_pid = {$i} and fsr_level = {$level} and fsr_group_id = {$pg_group_id} and fsr_to_whom_rated_id <> '-1' and skip = 0
group by fsr_to_whom_rated_id
) as rated_answers
order by rating desc
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
'answer' => $answer,
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

//pyramid creation timestamp
$answer_timeout_data = \Answer\get_answer_timeout();
$pyramid_creation_timestamp = (int)$answer_timeout_data['start_timestamp'];

$pyramid_groups = \Pyramid\get_groups();
$users_with_groups = \Group\get_users_with_groups();
$groups_activity = [];

//obtain activity per group
foreach($pyramid_groups as $pyramid_groups_item) {
    $group_level = $pyramid_groups_item['group_level'];
    $group_id = $pyramid_groups_item['group_id'];
    $group_activity = \Group\get_group_activity($group_level, $group_id);

    $groups_activity['level'][$group_level]['group'][$group_id] = $group_activity;
}

$students_details = [];
foreach($users_with_groups as $k_sid => &$users_with_groups_item) {
    $users_with_groups_item['details'] = \Student\get_student_details($k_sid);
}

header('Content-Type: text/html; charset=utf-8');
?><!DOCTYPE html>
<html lang="en">
<head>
    <title>ViewPyramidPage</title>
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

<input name="page" type="hidden" value="activity_tracking"/>
<input name="username" type="hidden" value="<?=htmlspecialchars($teacher_id)?>"/>
<div data-role="page">
    <div data-role="header">
        <h1>View Pyramid details</h1>
    </div>
    <div data-role="main" class="ui-content">
<pre>
        <?php
        echo var_export($users_and_groups);
        ?>
</pre>
        <div id="activity-info-main">

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
                            <li class="activity-pyramid-state-block-pyramid-list-item">Waiting for users since <?=date("l jS G:i", $last_expired_timestamp)?></li>
                            <li class="activity-pyramid-state-block-pyramid-list-item">Available students <?=implode(', ', $available_students)?></li>
                        </ul>
                    </div>
                <?php endif;?>
            <?php endif;?>

            <?php foreach($flow as $pkey => $pyramid): ?>
                <div class="activity-pyramid-block">

                    <?php foreach($pyramid['levels'] as $lkey => $level):?>
                        <div class="activity-pyramid-level-block">
                            <div class="activity-pyramid-group-block-number">Pyramid <?=($pkey+1)?> level <?=($lkey+2)?></div>

                            <div class="activity-pyramid-group-block-list">
                                <?php foreach($level['groups'] as $gkey => $group):?>
                                    <div class="activity-pyramid-level-group-block">
                                        <a href="#grp<?=($pkey+1)?><?=$lkey?><?=($gkey+1)?>" data-rel="popup" class="ui-btn ui-corner-all">Group <?=($gkey+1)?> </a>
                                        <div data-role="popup" id="grp<?=($pkey+1)?><?=$lkey?><?=($gkey+1)?>" data-theme="a" class="group-popup ui-corner-all">
                                            <div class="popup-members-title"><?=TS("Members")?></div>
                                            <ul class="popup-members-list">
                                                <?php $members = explode(',', $group['members']);?>
                                                <?php foreach($members as $mkey => $member):?>
                                                    <li><?=$member?></li>
                                                <?php endforeach; ?>
                                            </ul>

                                            <div class="ranking">
                                                <div class="popup-ranking-title"><?=TS("Ranking")?></div>
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