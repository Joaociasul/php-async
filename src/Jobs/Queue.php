<?php

namespace Joaociasul\PhpAsync\Jobs;

use Joaociasul\PhpAsync\Abstracts\JobInterface;
use Joaociasul\PhpAsync\Helpers\FilesHelper;
use Joaociasul\PhpAsync\Helpers\UuidHelper;

class Queue
{
    public static function dispatch(JobInterface $job)
    {
        $fileName = __DIR__ . '/../../storage/tasks.json';
        $queue[UuidHelper::make()] = serialize($job);

        FilesHelper::updateFileJsonAndGetOriginalContent($fileName, $queue);
    }
}