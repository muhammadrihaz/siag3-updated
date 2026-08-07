<?php
$f = 'writable/logs/log-2026-08-07.log';
if (!file_exists($f)) {
    echo "No log found today\n";
    exit;
}
$lines = file($f);
$count = count($lines);
for ($i = max(0, $count - 40); $i < $count; $i++) {
    echo $lines[$i];
}
