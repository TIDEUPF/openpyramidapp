<?php
include_once('init.php');

\Action\execute($action);

$question_form = View\element("question_form",array(
    'username' => 'Pablo',
    'level' => 'Level 1/5',
    'question_text' => 'Write a question',
    'question_submit_button' => 'Submit your question',
    'hidden_input_array' => array(
        'a_lvl' => '2',
        'a_peer_group_id' => '3',
    ),
));

$rating_form = View\element("question_rating",array(
    'username' => 'Pablo',
    'level' => 'Level 2/5',
    'header_text' => 'Rate the following questions',
//    'question_text' => 'Rate the questions',
    'question_text_array' => array(
        'q1' => 'What is your favourite color?',
        'q2' => 'Do you like pizza?',
        'q3' => 'What music do you listen to?',
    ),
    'question_rate_submit' => 'Rate',
));
/*
$html = View\element("page",array(
    'title' => 'title1',
    'body' => $rating_form,
    //'body' => $question_form,
    //'body' => $login_form,
));

echo $html;
*/