<?php

use Joaociasul\PhpAsync\Handlers\Proccess;
use Joaociasul\PhpAsync\Helpers\FilesHelper;
use Joaociasul\PhpAsync\Helpers\UuidHelper;

require __DIR__ . '/../vendor/autoload.php';
$storagePath = __DIR__ . '/../storage/';

$fileName = $storagePath . 'tasks.json';

$count = 0;
while (true) {
    $count++;
    exec('ls ' . $storagePath, $output);
    $contentBkp = [];
    foreach ($output as $file) {
        if (preg_match('/task-bkp\.json$/', $file) && file_exists($storagePath . $file)) {
            $content = file_get_contents($storagePath . $file);
            if ($content && $content !== '[]') {
                FilesHelper::updateFileJsonAndGetOriginalContent($fileName, json_decode($content, true));
            }
            unlink($storagePath . $file);
        }
    }
    $uidFileBkp = UuidHelper::make();
    $filenameBkp = $storagePath . $uidFileBkp . 'task-bkp.json';
    copy($fileName, $filenameBkp);
    $content = FilesHelper::updateFileJsonAndGetOriginalContent($fileName, [], true);
    Proccess::make(
        array_map(
            function ($item) {
                return static function () use ($item) {
                    file_put_contents('/tmp/task-php.log', $item . "\n", FILE_APPEND);
                    sleep(1);
                };
            },
            $content
        ),
        100
    );
    unlink($filenameBkp);
    echo $count . "\n";
    usleep(550000);
}

