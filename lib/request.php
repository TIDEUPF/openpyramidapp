<?php
namespace Request;

function param($varname) {
    if(!isset($_REQUEST[$varname]))
        return null;

    $var_filtered = stripslashes(strip_tags(trim($_REQUEST[$varname])));

    return $var_filtered;
}