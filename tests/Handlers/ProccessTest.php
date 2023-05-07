<?php

namespace JoaoRoyer\PhpAsyncTests\Handlers;

use InvalidArgumentException;
use JoaoRoyer\PhpAsync\Handlers\Proccess;
use JoaoRoyer\PhpAsync\Helpers\EventsHelper;
use PHPUnit\Framework\TestCase;

class ProccessTest extends TestCase
{
    public function testMake()
    {
        $proccesses = [];
        $event = EventsHelper::getInstance();
        for ($i = 1; $i <= 100; $i++) {
            $keyProccess = 'key' . $i;
            $proccesses[$keyProccess] = function () use ($i) {
                usleep(5);
                if ($i === 100) {
                    throw new InvalidArgumentException('test exit');
                }
            };
            $event->listen(Proccess::EVENT_SUCCESS . $keyProccess, function (array $args) use ($keyProccess) {
                $this->assertEquals($keyProccess, $args['array_key']);
            });

            $event->listen(Proccess::EVENT_ERROR . $keyProccess, function (array $args) use ($keyProccess) {
                $this->assertEquals('test exit', $args['exception']->getMessage());
            });
        }
        Proccess::make($proccesses, 20);
    }
}