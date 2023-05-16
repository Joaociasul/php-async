<?php

use Joaociasul\PhpAsync\Abstracts\JobInterface;
use Joaociasul\PhpAsync\Handlers\Proccess;
use Joaociasul\PhpAsync\Helpers\FilesHelper;
use Joaociasul\PhpAsync\Helpers\UuidHelper;

require __DIR__ . '/../vendor/autoload.php';
$storagePath = __DIR__ . '/../storage/';

$limitForExecution = 10;

$argsRegex = [
    'process' =>  '/--proccess=(\d+)/',
    'sleep' => '/--sleep=(\d)+/',
];

$arguments = [
    'process' => 10,
    'sleep' => 1,
];

$args = implode("\n", $argv);
foreach ($argsRegex as $key => $pattern) {
    preg_match($pattern, $args, $matches);
    $arguments[$key] = $matches[1] ?? $arguments[$key];
}

$fileName = $storagePath . 'tasks.json';
while (true) {
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
            function ($job) {
                return static function () use ($job) {
                    /**
                     * @var JobInterface $instance
                     */
                    $instance = unserialize($job);
                    try {
                        $instance->handle();
                        $instance->success();
                    } catch (\Exception $e) {
                        $instance->failed($e);
                        throw $e;
                    }

                };
            },
            $content
        ),
        $arguments['process']
    );
    unlink($filenameBkp);
    sleep((int) $arguments['sleep']);
}

