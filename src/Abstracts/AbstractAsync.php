<?php

namespace Joaociasul\PhpAsync\Abstracts;

use Joaociasul\PhpAsync\Exceptions\PidException;
use Joaociasul\PhpAsync\Handlers\Proccess;
use Joaociasul\PhpAsync\Helpers\EventsHelper;
use Joaociasul\PhpAsync\Helpers\UuidHelper;

abstract class AbstractAsync
{
    protected $pid;

    private $processKey = null;

    protected $eventsHelper;

    /**
     * @throws PidException
     */
    protected function createProccess(): void
    {
        $this->pid = pcntl_fork();
        if ($this->pid === -1) {
            throw new PidException('Async proccess not created!');
        }
        $this->setProcesstKey(UuidHelper::make());
    }

    public function wait(): int
    {
        pcntl_waitpid($this->pid, $status);
        return $status;
    }

    public function exec(callable $callback, string $key = null): void
    {
        $event = $this->getEventsHelper();
        if ($this->pid === 0) {
            $callback();
            $event->dispatch(Proccess::EVENT_SUCCESS . $key ?? $this->getProcessKey(), [
                'uuid' => $this->getProcessKey(),
                'array_key' => $key,
            ]);
            exit;
        }
    }

    public function getPid()
    {
        return $this->pid;
    }

    public function setProcesstKey(string $key): void
    {
        $this->processKey = $key;
    }

    public function getProcessKey(): string
    {
        return $this->processKey;
    }

    public function setEventsHelper(EventsHelper $eventsHelper)
    {
        $this->eventsHelper = $eventsHelper;
    }

    public function getEventsHelper(): EventsHelper
    {
        return $this->eventsHelper;
    }
}
