<?php
include_once('init.php');

$question_form = View\element("question_form",array(
    'group' => 'Group A',
    'level' => 'Level 1/5',
    'question_text' => 'Write a question',
    'question_submit_button' => 'Submit your question',
));

$rating_form = View\element("question_rating",array(
    'group' => 'Group A',
    'level' => 'Level 2/5',
    'header_text' => 'Rate the following questions',
    'question_text' => 'Rate the questions',
    'question_text_array' => array(
        'What is your favourite color ?',
        'Do you like pizza?',
    ),
    'question_submit_button' => 'Submit your question',
));

$html = View\element("page",array(
    'title' => 'title1',
    'body' => $rating_form,
    'body' => $question_form,
));

echo $html;