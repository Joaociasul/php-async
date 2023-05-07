<?php

namespace Joaociasul\PhpAsync\Handlers;

use Joaociasul\PhpAsync\Abstracts\AbstractAsync;
use Joaociasul\PhpAsync\Exceptions\PidException;

class Async extends AbstractAsync
{
    /**
     * @throws PidException
     */
    public function call(callable $callable): void
    {
        $this->createProccess();
        $this->exec($callable);
    }
}
