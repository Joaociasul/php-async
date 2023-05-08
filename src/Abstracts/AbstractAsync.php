<?php

namespace Joaociasul\PhpAsync\Abstracts;

use Exception;
use Joaociasul\PhpAsync\Exceptions\PidException;
use Joaociasul\PhpAsync\Handlers\Proccess;
use Joaociasul\PhpAsync\Helpers\EventsHelper;
use Joaociasul\PhpAsync\Helpers\UuidHelper;

abstract class AbstractAsync
{
    protected $pid;

    private $processKey = null;

    protected $eventsHelper;

    private $exception;

    private $callableThen;

    private $callableCatch;

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
            $this->runThen();
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

    public function then(callable $callable): void
    {
        $this->callableThen = $callable;
    }

    public function catch(callable $callable): void
    {
        $this->callableCatch = $callable;
    }

    protected function runCatch(Exception $exception, string $key = null)
    {
        $this->eventsHelper->dispatch(Proccess::EVENT_ERROR . $key ?? $this->getProcessKey(), [
            'exception' => $exception
        ]);
        if ($this->callableCatch) {
            call_user_func($this->callableCatch, $exception, $this);
        }
        exit;
    }

    protected function runThen(string $key = null)
    {
        $this->eventsHelper->dispatch(Proccess::EVENT_SUCCESS . $key ?? $this->getProcessKey(), [
            'uuid' => $this->getProcessKey(),
            'array_key' => $key,
        ]);
        if ($this->callableThen) {
            call_user_func($this->callableThen, $this);
        }
        exit;
    }

    protected function setException(Exception $exception)
    {
        $this->exception = $exception;
    }

    protected function getException(): Exception
    {
        return $this->exception;
    }
}
