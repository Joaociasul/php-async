<?php

use Joaociasul\PhpAsync\Helpers\FilesHelper;

require __DIR__ . '/../vendor/autoload.php';

$fileName = __DIR__ . '/../storage/tasks.json';

for ($i = 0; $i <= 2000; $i++) {
    $newContent = [
        'key-' . $i => "value" . $i,
    ];
    FilesHelper::updateFileJsonAndGetOriginalContent($fileName, $newContent);
//    usleep(10);
}
