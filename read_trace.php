<?php
$content = file_get_contents(__DIR__ . '/writable/logs/log-' . date('Y-m-d') . '.log');
$lines = explode("\n", $content);
$capture = false;
$trace = [];
foreach(array_reverse($lines) as $line) {
    if (strpos($line, 'Unknown column \'nama_ibadah\'') !== false) {
        $capture = true;
    }
    if ($capture) {
        $trace[] = $line;
        if (strpos($line, 'ERROR -') !== false || strpos($line, 'CRITICAL -') !== false) {
            if (count($trace) > 20) {
               break;
            }
        }
    }
}
file_put_contents('error_trace.txt', implode("\n", array_reverse($trace)));
