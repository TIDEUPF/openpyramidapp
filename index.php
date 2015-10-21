<?php
include_once('init.php');

$question_form = View\element("question_form",array(
    'group' => 'Group A',
    'level' => 'Level 1/5',
    'question_text' => 'Lopsem',
    'question_submit_button' => 'Submit your question',
));

$html = View\element("page",array(
    'title' => 'title1',
    'body' => $question_form,
));

echo $html;