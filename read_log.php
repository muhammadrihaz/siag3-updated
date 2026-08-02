<?php
$lines = file(__DIR__ . '/writable/logs/log-' . date('Y-m-d') . '.log');
$errors = array_filter($lines, function($line) {
    return strpos($line, 'ERROR -') !== false || strpos($line, 'CRITICAL -') !== false || strpos($line, 'Exception') !== false;
});
file_put_contents('error_log_utf8.txt', implode("", array_slice($errors, -50)));
