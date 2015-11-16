<?php

namespace Test;

function create_users() {
    global $students;

    $students = array();

    for($i=0;$i<10;$i++)
        $students[] = array('sid' => 'std_'.$i);

    for($i=0;$i<10;$i++) {
        login($students[$i]);
        $response = request($students[$i]['actions'][0]);
    }

}


function request($params) {
    global $baseurl;
    $path = array(
        'login' => 'student_login.php',
    );

    $response = \Httpful\Request::post($baseurl. $path[$params['action']], $params['request'])->expects("application/json")->sendIt();

    return $response->body;
}

function option($options) {
    return $options[mt_rand(0, count($options)-1)];
}

function login(&$student, $page) {

    $request = array(
        'loginBtn'  => '',
        'usr'       => $student['sid'],
    );

    $student['actions'][] = array(
        'action'    => 'login',
        'page'      => $page,
        'request'   => $request,
    );
}

function end($student, $page) {

    $request = array(
        'loginBtn'              => '',
        'usr'                   => $student['sid'],
        'final_answer_array'    => $page['final_answer_array'],
    );

    $student['actions'][] = array(
        'action'    => 'end',
        'page'      => $page,
        'request'   => $request,

    );
}

function timeout($student, $page) {

    $request = array(
        'loginBtn'              => '',
        'usr'                   => $student['sid'],
    );

    $student['actions'][] = array(
        'action'    => 'timeout',
        'page'      => $page,
        'request'   => $request,
    );
}

function question(&$student, $page) {
    $options = array(
        'wait',
        'answer',
        'skip',
        'empty',
        'refresh',
    );

    $action = option($options);

    switch($action) {
        case 'answer':
            $request = array(
                'qa'        => 'q_'.$student['sid'],
                'answer'    => '',
            );
            break;
        case 'skip':
            $request = array(
                'qa'      => '',
                'skip'    => '',
            );
            break;
        case 'empty':
            $request = array(
                'qa'        => '',
                'answer'    => '',
            );
            break;
        case 'wait':
        case 'refresh':
            $request = null;
            break;
    }
    $request = array(
        'qa'        => 'q_'.$student['sid'],
        'answer'    => '',
    );


    $request = \array_merge($request, $page['hidden_input_array']);

    $student['actions'][] = array(
        'action'    => $action,
        'page'      => $page,
        'request'   => $request,

    );
}

function rating(&$student, $page) {
    $options = array(
        'wait',
        'rate',
        'refresh',
        'empty',
    );

    $action = option($options);


    $rating_vars_identifiers = array(
        'optradio',
    );

    for($i=1;$i<=$page['numofqustions'];$i++) {
        foreach ($rating_vars_identifiers as $rating_var) {
            $rating[$rating_var . $i] = option(array(1,2,3,4,5));
        }
    }


    switch($action) {
        case 'answer':
            for($i=1;$i<=$page['numofqustions'];$i++) {
                foreach ($rating_vars_identifiers as $rating_var) {
                    $rating[$rating_var . $i] = option(array(1,2,3,4,5));
                }
            }

            $request = array(
                'qa'        => 'q_'.$student['sid'],
                'answer'    => '',
            );

            $request = array_merge($request, $rating);
            break;
        case 'empty':
            for($i=1;$i<=$page['numofqustions'];$i++) {
                foreach ($rating_vars_identifiers as $rating_var) {
                    $rating[$rating_var . $i] = '';
                }
            }

            $request = array(
                'qa'        => 'q_'.$student['sid'],
                'answer'    => '',
            );

            $request = array_merge($request, $rating);
            break;
        case 'wait':
        case 'refresh':
            $request = null;
            break;

    }
    $request = array(
        'qa'        => 'q_'.$student['sid'],
        'answer'    => '',
    );


    $request = \array_merge($request, $page['hidden_input_array']);

    $student['actions'][] = array(
        'action'    => $action,
        'page'      => $page,
        'request'   => $request,

    );
}
