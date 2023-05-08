<?php

namespace Joaociasul\PhpAsyncTests\Handlers;

use InvalidArgumentException;
use Joaociasul\PhpAsync\Handlers\Proccess;
use Joaociasul\PhpAsync\Helpers\EventsHelper;
use PHPUnit\Framework\TestCase;

class ProccessTest extends TestCase
{
    public function testMake(){
        $proccesses = [];
        $this->expectOutputString('');
        $event = EventsHelper::getInstance();
        $storagePath = dirname(__DIR__, 2) . '/storage/';
        file_put_contents($storagePath . "accert.txt", '0');
        file_put_contents($storagePath . "error.txt", '0');

        for ($i = 1; $i <= 10; $i++) {
            $keyProccess = 'key' . $i;
            $proccesses[$keyProccess] = function () use ($i) {
                usleep(50);
                if ($i === 10) {
                    throw new InvalidArgumentException('test exit');
                }
            };
            $event->listen(Proccess::EVENT_SUCCESS . $keyProccess, function (array $args) use (&$storagePath) {
                $numAccerts = (int) file_get_contents($storagePath . "accert.txt");
                $numAccerts++;
                file_put_contents($storagePath . "accert.txt", (string) $numAccerts);
            });

            $event->listen(Proccess::EVENT_ERROR . $keyProccess, function (array $args) use ($storagePath)  {
                $numErrors = (int) file_get_contents($storagePath . "error.txt");
                $numErrors++;
                file_put_contents($storagePath . "error.txt", (string) $numErrors);
            });
        }
        Proccess::make($proccesses, 1);
        $numErrors = (int) file_get_contents($storagePath . "error.txt");
        $numAccerts = (int) file_get_contents($storagePath . "accert.txt");
        $this->assertSame(9, $numAccerts);
        $this->assertSame(1, $numErrors);
    }
}