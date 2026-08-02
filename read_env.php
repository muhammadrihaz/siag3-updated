<?php
$content = file_get_contents('.env');
$lines = explode("\n", $content);
foreach($lines as $line) {
    if (strpos($line, 'app.baseURL') !== false) {
        echo $line . "\n";
    }
}
