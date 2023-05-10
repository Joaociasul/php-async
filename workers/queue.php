<?php

use Joaociasul\PhpAsync\Helpers\FilesHelper;

require __DIR__ . '/../vendor/autoload.php';



$fileName = __DIR__ . '/../storage/tasks.json';

while (true) {
    $content = FilesHelper::updateFileJsonAndGetOriginalContent($fileName, [], true);
    \Joaociasul\PhpAsync\Handlers\Proccess::make(
        array_map(function ($item) {
            return function() use ($item) {
                echo $item;
                sleep(1);
            };
        }, $content), 100
    );
}

