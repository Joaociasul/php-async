<?php

namespace JoaoRoyer\PhpAsync\Handlers;

use JoaoRoyer\PhpAsync\Abstracts\AbstractAsync;
use JoaoRoyer\PhpAsync\Exceptions\PidException;

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
