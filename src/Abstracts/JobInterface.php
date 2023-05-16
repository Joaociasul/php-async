<?php

namespace Joaociasul\PhpAsync\Abstracts;

use Exception;

interface JobInterface
{
    public function handle();

    public function failed(Exception $exception);

    public function success();
}