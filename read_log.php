<?php
$lines = file("writable/logs/log-2026-08-07.log");
$found = [];
foreach($lines as $line) {
    if (strpos($line, 'Unknown column') !== false) {
        $found[] = $line;
    }
}
echo "Found " . count($found) . " errors:\n";
echo implode("\n", array_slice($found, -5)); // show last 5
