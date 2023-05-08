<?php

namespace Joaociasul\PhpAsync\Handlers;

use Joaociasul\PhpAsync\Abstracts\AbstractAsync;

class Promise extends AbstractAsync
{
    public function __construct(callable $callback)
    {
        try {
            $this->createProccess();
            $this->exec($callback);
        } catch (\Exception $exception) {
            $this->runCatch($exception);
        }
    }

}