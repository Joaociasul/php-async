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
        $filenameToAccert = $storagePath . "proccessAccert.test";
        $filenameToError = $storagePath . "proccessFailed.test";
        file_put_contents($filenameToAccert, '0');
        file_put_contents($filenameToError, '0');

        for ($i = 1; $i <= 10; $i++) {
            $keyProccess = 'key' . $i;
            $proccesses[$keyProccess] = function () use ($i) {
                usleep(50);
                if ($i === 10) {
                    throw new InvalidArgumentException('test exit');
                }
            };
            $event->listen(Proccess::EVENT_SUCCESS . $keyProccess, function (array $args) use ($filenameToAccert, &$storagePath) {
                $numAccerts = (int) file_get_contents($filenameToAccert);
                $numAccerts++;
                file_put_contents($filenameToAccert, (string) $numAccerts);
            });

            $event->listen(Proccess::EVENT_ERROR . $keyProccess, function (array $args) use ($filenameToError, $storagePath)  {
                $numErrors = (int) file_get_contents($filenameToError);
                $numErrors++;
                file_put_contents($filenameToError, (string) $numErrors);
            });
        }
        Proccess::make($proccesses, 1);
        $numErrors = (int) file_get_contents($filenameToError);
        $numAccerts = (int) file_get_contents($filenameToAccert);
        $this->assertSame(9, $numAccerts);
        $this->assertSame(1, $numErrors);
    }
}