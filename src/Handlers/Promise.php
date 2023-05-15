<?php

namespace Joaociasul\PhpAsync\Handlers;

use Joaociasul\PhpAsync\Abstracts\AbstractAsync;
use Joaociasul\PhpAsync\Helpers\EventsHelper;

class Promise extends AbstractAsync
{
    private $callback;

    public function __construct(callable $callback)
    {
        $this->setEventsHelper(EventsHelper::getInstance());
        $this->callback = $callback;
    }

    public function run()
    {
        try {
            $this->createProccess();
            $this->exec($this->callback);
        } catch (\Exception $exception) {
            $this->runCatch($exception);
        }
    }

}