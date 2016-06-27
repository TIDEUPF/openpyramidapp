<?php
$flow_data = [];
//$fid_array = [10,11,12];

//obtain the flows pertaining to the current teacher
$flows = [];
$teacher_id = $_SESSION['user'];
$flow_query = mysqli_query($link, "select * from flow where teacher_id = '$teacher_id'");
if(mysqli_num_rows($flow_query) > 0) {
    while ($flow_query_row = mysqli_fetch_assoc($flow_query)) {
        $flow['id'] = $flow_query_row["fid"];
        $flow['name'] = $flow_query_row["fname"];
        $flows[] = $flow;
    }
}
//obtain the data for every flow
foreach($flows as $flow) {

    $fid = $flow['id'];

    //find the last pid for the current flow
    $npid = null;
    $result = mysqli_query($link,"select * from pyramid_students where fid='$fid' order by pid desc limit 1");

    if(!mysqli_num_rows($result))
        $npid = null;
    else {
        $result_row = mysqli_fetch_assoc($result);
        $npid = (int)$result_row['pid'];
    }


    //obtain the data for every pyramid in the current flow
    $pyramid_data = [];
    for($i=0;$i<=$npid;$i++) {

        $pyramid_query = mysqli_query($link, "select fname, pg_level, pg_group, pg_group_id, pg_pid from pyramid_groups, flow where pg_fid = fid and pg_fid = '$fid' and pg_pid = '{$i}' order by pg_level ASC, pg_group_id ASC");
        if(mysqli_num_rows($pyramid_query) > 0) {
            $py_created = true;
        } else {
            $py_created = false;
        }

        if(!$py_created) {
            $pyramid_data[$i] = null;
        } else {

        }

        //check if the pyramid has finished with the desired number of answers
        if (mysqli_num_rows(mysqli_query($link, "select * from pyramid_groups where pg_fid = '$fid' and pg_pid = '{$i}'")) == mysqli_num_rows(mysqli_query($link, "select * from selected_answers where sa_fid = '$fid' and sa_pid = '{$i}'"))) {

            $pyramid_level_answers = mysqli_query($link, "select * from flow where fid = '$fid'");
            $pyramid_level_answers_row = mysqli_fetch_assoc($pyramid_level_answers);
            $activity_level = $pyramid_level_answers_row['levels'];
            $activity_level = $activity_level - 1;
            $result_11 = mysqli_query($link, "select * from selected_answers where sa_fid = '$fid' and sa_pid = '{$i}' and sa_level = '$activity_level'");

            while ($data_t_11 = mysqli_fetch_assoc($result_11)) {
                $qa_last_selected_id = $data_t_11['sa_selected_id'];
                $result_12 = mysqli_query($link, "select * from flow_student where fid = '$fid' and sid = '$qa_last_selected_id'");
                $data_t_12 = mysqli_fetch_assoc($result_12);
                $pyramid_data[$i]['answers'][] = $data_t_12['fs_answer'];
            }
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
            $pyramid_level_answers = mysqli_query($link, "select * from selected_answers where sa_fid = '$fid' and sa_pid = '{$i}' and sa_level='$level' and sa_group_id='$pg_group_id'");
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
                $pyramid_data[$i]['levels'][$level]['groups'][] = [
                    'members' => $group,
                    'answer' => $answer,
                    'ranking' => $ranking
                ];
            }
        }
    }

    $flow_data[$flow_name] = $pyramid_data;
}

    $flow = $flow_data['forest2chat'];
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
</head>
<body>
<div data-role="page">
    <div data-role="header">
        <h1>View Pyramid details</h1>
    </div>
    <div data-role="main" class="ui-content">
        <div id="activity-info-main">
            <div id="activity-select-block">
                <div class="ui-field-contain">
                    <label for="select-activity">Activity name:</label>
                    <select name="select-activity" id="select-activity">
                        <?php foreach($flows as $flow_option):?>
                        <option value="<?=$flow_option['id']?>"><?=htmlspecialchars($flow_option['name'])?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>


            <div id="activity-winning-answers-block">
            <div id="activity-winning-answers-block-title"><?=TS("Most popular options:")?></div>
            <?php foreach($flow as $pkey => $pyramid):?>
                <?php if(count($flow) > 1 and isset($pyramid['answers'])):?>
                <div class="activity-winning-answers-block-pyramid-block">
                    <div class="activity-winning-answers-block-pyramid-number"><?=TS("Pyramid") . ' ' . ($pkey+1)?></div>
                <?php endif;?>
                    <ul class="activity-winning-answers-block-pyramid-list">
                    <?php foreach($pyramid['answers'] as $answer):?>
                        <li class="activity-winning-answers-block-pyramid-list-item"><?=htmlspecialchars($answer)?></li>
                    <?php endforeach ;?>
                    </ul>
                <?php if(count($flow) > 1):?>
                </div>
                <?php endif;?>

                <?php endforeach; ?>
            </div>

            <?php foreach($flow as $pkey => $pyramid): ?>
                <div class="activity-pyramid-block">

                    <?php foreach($pyramid['levels'] as $lkey => $level):?>
                        <div class="activity-pyramid-level-block">
                            <div class="activity-pyramid-group-block-number">Pyramid <?=($pkey+1)?> level <?=($lkey+1)?></div>

                            <div class="activity-pyramid-group-block-list">
                            <?php foreach($level['groups'] as $gkey => $group):?>
                                <div class="activity-pyramid-level-group-block">
                                    <a href="#grp<?=($pkey+1)?>2<?=($gkey+1)?>" data-rel="popup" class="ui-btn ui-corner-all">Group <?=($gkey+1)?> </a>
                                    <div data-role="popup" id="grp<?=($pkey+1)?>2<?=($gkey+1)?>" data-theme="a" class="group-popup ui-corner-all">
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
                                                <li><div class="ranking-position"><?=(($i++)+1)?></div><div class="ranking-text"><?=htmlspecialchars($ar['answer'])?></div><div class="ranking-score"><?=$ar['rating']?></div></li>
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
















            <div class="ui-corner-all custom-corners">
                <div class="ui-bar ui-bar-a">
                    <div class="ui-grid-a">
                        <div class="ui-block-a" style="width:300px"><div class="ui-bar ui-bar-a" style="height:30px">Most popular options:</div></div>
                        <div class="ui-block-b" style="width:950px"><div class="ui-bar ui-bar-a">
                                <ul data-role="listview">
                                    <?php foreach($flow as $pyramid):?>
                                        <?php foreach($pyramid['answers'] as $answer):?>
                                    <li><?=htmlspecialchars($answer)?></li>
                                        <?php endforeach ;?>
                                    <?php endforeach; ?>
                                </ul>
                            </div></div>
                    </div><!-- /grid-a -->
                </div>

                <style>
                    .group-popup {
                        padding: 15px;
                        font-weight: bold;
                        line-height: 18px;;

                    }
                </style>

                <?php foreach($flow as $pkey => $pyramid): ?>
                    <?php $block_letters = ['a', 'a', 'a', 'b', 'c', 'd', 'e', 'f', 'g'] ?>
                <div class="ui-bar ui-bar-a" style="height:30px">Pyramid No : <?=($pkey+1)?></div>
                <div class="ui-grid-<?=$block_letters[count($pyramid['levels'])-1]?>">
                    <div class="ui-block-a"><div class="ui-bar ui-bar-a" style="height:30px">Level: 2 &nbsp;&nbsp;  No of groups : <?=count($pyramid['levels'][1]['groups'])?></div>
                        <div class="ui-grid-<?=$block_letters[count($pyramid['levels'][1]['groups'])]?>"><!-- ui-grid-X where X depends of the no of columns needed -->
                            <?php foreach($pyramid['levels'][1]['groups'] as $key => $group): ?>
                            <div class="ui-block-<?=$block_letters[$key+2]?>">
                                <div class="ui-bar ui-bar-a">
                                    <a href="#grp<?=($pkey+1)?>2<?=($key+1)?>" data-rel="popup" class="ui-btn ui-corner-all">Group <?=($key+1)?> </a>
                                    <div data-role="popup" id="grp<?=($pkey+1)?>2<?=($key+1)?>" data-theme="a" class="group-popup ui-corner-all">
                                    <?=implode('<br>', explode(',', $group[0]))?>
                                    </div>
                                    <?=htmlspecialchars($group[1])?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="ui-block-b"><div class="ui-bar ui-bar-a" style="height:30px">Level: 3 &nbsp;&nbsp;  No of groups : <?=count($pyramid['levels'][2]['groups'])?></div>
                        <div class="ui-grid-<?=$block_letters[count($pyramid['levels'][2]['groups'])]?>">
                            <?php foreach($pyramid['levels'][2]['groups'] as $key => $group): ?>
                            <div class="ui-block-<?=$block_letters[$key+2]?>">
                                <div class="ui-bar ui-bar-a">
                                    <a href="#grp<?=($pkey+1)?>3<?=($key+1)?>" data-rel="popup" class="ui-btn ui-corner-all">Group <?=($key+1)?> </a>
                                    <div data-role="popup" id="grp<?=($pkey+1)?>3<?=($key+1)?>" data-theme="a" class="group-popup ui-corner-all">
                                        <?=implode('<br>', explode(',', $group[0]))?>
                                    </div>
                                    <?=htmlspecialchars($group[1])?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="ui-bar ui-bar-a" style="height:10px"></div>
                <?php endforeach;?>

            </div>
        </div>
    </div>
</div>

</body>
</html>