<?php

namespace Joaociasul\PhpAsync\Handlers;

use Exception;
use Joaociasul\PhpAsync\Abstracts\AbstractAsync;
use Joaociasul\PhpAsync\Exceptions\PidException;
use Joaociasul\PhpAsync\Helpers\EventsHelper;

class Async extends AbstractAsync
{
    /**
     * @throws PidException
     */
    public function call(callable $callable, string $key = null): void
    {
        $event = $this->getEventsHelper();
        try{
            $this->createProccess();
            $this->exec($callable, $key);
        } catch (Exception $exception) {
            $event->dispatch(Proccess::EVENT_ERROR . $key ?? $this->getProcessKey(), [
                'exception' => $exception
            ]);
            exit;
        }
    }
}
