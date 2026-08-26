<?php

$files = glob(__DIR__.'/app/Http/Requests/**/*.php');
foreach ($files as $f) {
    echo "\n--- ".basename($f)." ---\n";
    $content = file_get_contents($f);
    preg_match('/public function rules\(\): array\s*\{(.*?)\}/s', $content, $matches);
    if (isset($matches[1])) {
        echo trim($matches[1]);
    } else {
        echo 'No rules found';
    }
}
