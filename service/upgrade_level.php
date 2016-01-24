<?php

include_once('../init.php');

global $link, $fid, $pyramid_minsize, $flow_data;
echo 'init passed';
while(true) {
    sleep(1);

    echo "iteration started\n";

    $answer_timeout_array = \Answer\get_answer_timeout();
    $answer_timeout = $answer_timeout_array['start_timestamp'];

    if(time() > $answer_timeout + $flow_data['question_timeout']) {
        $complete_query = mysqli_query($link, "select * from pyramid_groups where pg_level = 0 and pg_started = 0 and pg_fid = '$fid'");
        while($complete_result = mysqli_fetch_assoc($link, $complete_query)) {
            $time = time();
            $complete_peer_array = explode(',', $complete_result['pg_group']);

            //dummy insert to provide at least one answer to the engine
            mysqli_query($link, "insert into flow_student values (null, '$fid','$complete_peer_array[0]', '', 1, $time)");
            mysqli_query($link, "update pyramid_groups set pg_started = 1, pg_start_timestamp='{$time}' where pg_started = '0' and pg_fid='{$fid}' and pg_level='0' and pg_group_id='{$complete_result['pg_group_id']}'");
        }
    } else {
        continue;
    }

    $time = time();
    $sql = <<<SQL
select * from pyramid_groups
where pg_start_timestamp <= '{$max_valid_start_time}'
and pg_started = 1
and pg_fid = '$fid'
and not exists (
select * from select_answers
where sa_fid = '$fid'
and sa_level = pg_level
and sa_group_id = pg_group_id
)
SQL;

    $complete_query = mysqli_query($link, $sql);

    while($complete_result = mysqli_fetch_assoc($link, $complete_query)) {

        //to sum the ratings
        $time = time();
        $ssa_result_1 = mysqli_query($link, "SELECT fsr_to_whom_rated_id, skip, SUM(fsr_rating) as sum FROM `flow_student_rating` where fsr_fid = '$fid' and fsr_level = '{$complete_result['pg_level']}' and fsr_group_id = '{$complete_result['pg_group_id']}' group by fsr_to_whom_rated_id order by SUM(fsr_rating) desc limit 1");
        if($ssa_result_1_array = mysqli_fetch_assoc($ssa_result_1)) {
            mysqli_query($link, "insert into selected_answers values ('$fid', '$activity_level', '$peer_group_id', '{$ssa_result_1_array['fsr_to_whom_rated_id']}', '{$ssa_result_1_array['sum']}', '0')");
        } else {
            mysqli_query($link, "insert into selected_answers values ('$fid', '$activity_level', '$peer_group_id', '-1', '0', '1')");
        }
        mysqli_query($link, "update pyramid_groups set pg_started = 1, pg_start_timestamp='{$time}' where pg_started = '0' and pg_fid='{$fid}' and pg_level='{$complete_result['pg_level']}' and pg_group_id='{$complete_result['pg_group_id']}'");

    }

}
