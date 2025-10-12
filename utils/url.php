<?php
// utils/url.php

function url($path = '') {
    $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    return $basePath . '/' . ltrim($path, '/');
}

function asset($path) {
    return url('assets/' . ltrim($path, '/'));
}
?>