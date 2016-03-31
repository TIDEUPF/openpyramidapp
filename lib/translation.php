<?php

function load_translation($lang){
    $filepath = __DIR__ . '/../translations/' . $lang . '.php';
    if(file_exists($filepath))
        include($filepath);
}

function T($untranslated) {
    global $translation;

    if(!$translation)
        return $untranslated;

    if(empty($untranslated))
        return "";

    if(isset($translation[$untranslated]))
        return $translation[$untranslated];
    else
        return $untranslated;
}

function TS($untranslated) {
    return htmlspecialchars(T($untranslated));
}