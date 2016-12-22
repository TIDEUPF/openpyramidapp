<?php
include('dbvar.php');

//$teacher_id = $_SESSION['user'];
global $fis, $pid, $flow_data;

\Flow\set_fid($_REQUEST['ldshake_fid']);

//\Pyramid\set_current_flow($fid);
\Flow\set_fid($fid);

//retrieve global flow last keys
$last_flow_keys = \Flow\get_flow_status();
$flow_properties = $flow_data;

foreach($flow_properties as &$flow_properties_item) {
    if(is_numeric($flow_properties_item)) {
        $flow_properties_item = (int)$flow_properties_item;
    }
}

//async mode and multiple pyramids
if($flow_data['multi_py'] == 1 and $flow_data['sync'] == 0) {
    $unfilled_pyramids = \Flow\get_not_full_pyramids();
    $last_expired_timestamp = \Flow\get_last_pyramid_expired_timestamp();
    $last_expired_timestamp_date = date("l jS G:i", $last_expired_timestamp);
    $flow_timestamps = \Flow\get_timestamps();
    $question_submit_expiry_timestamp = $flow_timestamps[0];
    $question_submit_expiry_timestamp_date = date("l jS G:i", $question_submit_expiry_timestamp);

    if(empty($unfilled_pyramids)) {
        $available_students = \Flow\get_available_students();
    }

    $multi_async_properties = [
        'unfilled_pyramids' => $unfilled_pyramids,
        'last_expired_timestamp' => $last_expired_timestamp,
        'last_expired_timestamp_date' => $last_expired_timestamp_date,
        'flow_timestamps' => $flow_timestamps,
        'question_submit_expiry_timestamp' => $question_submit_expiry_timestamp,
        'question_submit_expiry_timestamp_date' => $question_submit_expiry_timestamp_date,
        'available_students' => $available_students,
    ];

    $flow_properties['async_multi'] = $multi_async_properties;
    $flow_properties['async_multi_html'] = \View\element("activity_async_multi_status", $multi_async_properties);
}

$pyramid_ids = \Flow\get_pyramid_ids();
$pyramid_item = [];

foreach($pyramid_ids as $pyramid_ids_item) {
    \Pyramid\set_pid($pyramid_ids_item);
    \Util\sql_gen();

//pyramid creation timestamp
    $answer_timeout_data = \Answer\get_answer_timeout();
    $pyramid_creation_timestamp = (int)$answer_timeout_data['start_timestamp'];

    $pyramid_groups = \Pyramid\get_groups();
    $pyramid_results = \Pyramid\get_results();
    $users_with_groups = \Group\get_users_with_groups();
    $groups_activity = [];

//obtain activity per group
    foreach ($pyramid_groups as $pyramid_groups_item) {
        $group_level = $pyramid_groups_item['group_level'];
        $group_id = $pyramid_groups_item['group_id'];
        $group_activity = \Group\get_group_activity($group_level, $group_id);

        $groups_activity['levels'][$group_level]['groups'][$group_id] = $group_activity;
    }

    $students_details = [];
    foreach ($users_with_groups as $k_sid => &$users_with_groups_item) {
        $users_with_groups_item['details'] = \Student\get_student_details($k_sid);
    }

    $pyramid_item[] = [
        'pyramid_creation_timestamp' => $pyramid_creation_timestamp,
        'users_with_groups' => $users_with_groups,
        'levels' => $groups_activity['levels'],
        'results' => $pyramid_results,
    ];
}
$current_flow_status = [
    'last_flow_keys' => $last_flow_keys,
    'flow_properties' => $flow_properties,
    'remaining_pyramids' => \Pyramid\remaining_pyramids(),
    'pyramid_data' => $pyramid_item,
];

header('Content-Type: application/json');
echo json_encode($current_flow_status);