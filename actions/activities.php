<?php
$flow_data = [];
$fid_array = [10,11,12];
foreach($fid_array as $fid) {

    //find the last pid
    $npid = null;
    $result = mysqli_query($link,"select * from pyramid_students where fid='$fid' order by pid desc limit 1");

    if(!mysqli_num_rows($result))
        $npid = null;
    else {
        $result_row = mysqli_fetch_assoc($result);
        $npid = (int)$result_row['pid'];
    }


    $pyramid_data = [];

    for($i=0;$i<=$npid;$i++) {


        $res2 = mysqli_query($link, "select fname, pg_level, pg_group, pg_group_id, pg_pid from pyramid_groups, flow where pg_fid = fid and pg_fid = '$fid' and pg_pid = '{$i}' order by pg_level ASC, pg_group_id ASC");
        if(mysqli_num_rows($res2) > 0) {
            $py_created = true;
        } else {
            $py_created = false;
        }

        if(!$py_created) {
            $pyramid_data[$i] = null;
        } else {

        }


        if (mysqli_num_rows(mysqli_query($link, "select * from pyramid_groups where pg_fid = '$fid' and pg_pid = '{$i}'")) == mysqli_num_rows(mysqli_query($link, "select * from selected_answers where sa_fid = '$fid' and sa_pid = '{$i}'"))) {

            $res3 = mysqli_query($link, "select * from flow where fid = '$fid'");
            $data3 = mysqli_fetch_assoc($res3);
            $activity_level = $data3['levels'];
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
        while ($data2 = mysqli_fetch_assoc($res2)) {
            $flow_name = $data2["fname"];
            $level = $data2["pg_level"];
            $group = $data2["pg_group"];
            $pg_group_id = $data2["pg_group_id"];
            //$pyramid_data['flow_name'] = $flow_name;

            $res3 = mysqli_query($link, "select * from selected_answers where sa_fid = '$fid' and sa_pid = '{$i}' and sa_level='$level' and sa_group_id='$pg_group_id'");
            if (mysqli_num_rows($res3) > 0) { //the group has selected an answer

                while($data3 = mysqli_fetch_assoc($res3)) {
                    $selected_id = $data3['sa_selected_id'];

                    $res4 = mysqli_query($link, "select * from flow_student where fid = '$fid' and sid = '$selected_id'");
                    if (mysqli_num_rows($res4) > 0) {
                        $data4 = mysqli_fetch_assoc($res4);
                        $answer = $data4['fs_answer'];
                    }
                }
                $pyramid_data[$i]['levels'][$level]['groups'][] = [$group, $answer];
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
</head>
<body>
<div data-role="page">
    <div data-role="header">
        <h1>View Pyramid details</h1>
    </div>
    <div data-role="main" class="ui-content">
        <form>
            <div class="ui-field-contain">
                <label for="select-native-1">Activity name:</label>
                <select name="select-native-1" id="select-native-1">
                    <option value="1">Activity 1</option>
                    <option value="2">Activity 2</option>
                    <option value="3">Activity 3</option>
                    <option value="4">Activity 4</option>
                </select>
            </div>

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
        </form>
    </div>
</div>

</body>
</html>