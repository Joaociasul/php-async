<?php

namespace Joaociasul\PhpAsync\Abstracts;

use Joaociasul\PhpAsync\Exceptions\PidException;
use Joaociasul\PhpAsync\Helpers\UuidHelper;

abstract class AbstractAsync
{
    protected $pid;

    private $processKey = null;

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

    public function exec(callable $callback): void
    {
        if ($this->pid === 0) {
            $callback();
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
}
