<?php

use Joaociasul\PhpAsync\Helpers\FilesHelper;

require __DIR__ . '/../vendor/autoload.php';

$fileName = __DIR__ . '/../storage/tasks.json';


for ($i = 0; $i <= 2000; $i++) {

    $job = new \Joaociasul\PhpAsync\Jobs\JobExample(['key' => $i]);
    \Joaociasul\PhpAsync\Jobs\Queue::dispatch($job);
}
