<?php

namespace Joaociasul\PhpAsync\Jobs;

use Exception;
use InvalidArgumentException;
use Joaociasul\PhpAsync\Abstracts\JobInterface;

class JobExample implements JobInterface
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        $rand = rand(1, 5);
        if ($rand === 2) {
            throw new InvalidArgumentException("Roleta russa: " . $rand . "\n");
        }
        echo "Handle success\n";
    }

    public function failed(Exception $exception)
    {
        echo $exception->getMessage();
    }

    public function success()
    {
        echo "successsss\n";
    }
}