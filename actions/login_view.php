<?php

$login_form = View\element("login", array(
    'hidden_input_array' => array(
    ),
));

\View\page(array(
    'title' => 'Student Login',
    'body' => $login_form,
));