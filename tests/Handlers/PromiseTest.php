<?php

namespace Handlers;

use Exception;
use Joaociasul\PhpAsync\Handlers\Promise;
use PHPUnit\Framework\TestCase;

class PromiseTest extends TestCase
{
    public function testPromise()
    {
        $storagePath = dirname(__DIR__, 2) . '/storage/';
        $filenameToAccert = $storagePath . "promiseAccert.test";
        $filenameToError = $storagePath . "promiseFailed.test";
        $promise = new Promise(function () {
            usleep(10);
        });
        $promise->then(function (Promise $promise) use ($filenameToAccert) {
            file_put_contents($filenameToAccert, '1');
        });
        $promise->catch(function (Exception $exception, Promise $promise) use($filenameToError) {
            file_put_contents($filenameToError, '1');
        });
        $promise->run();

        $promise1 = new Promise(function () {
            usleep(10);
            throw new \InvalidArgumentException('test');
        });
        $promise1->then(function (Promise $promise1) use ($filenameToAccert) {
            file_put_contents($filenameToAccert, '2');
        });
        $promise1->catch(function (Exception $exception, Promise $promise1) use ($filenameToError) {
            file_put_contents($filenameToError, '2');
        });
        $promise1->run();

        $promise->wait();
        $promise1->wait();

        $fileAccert = file_get_contents($filenameToAccert);
        $this->assertEquals('1', $fileAccert);
        $fileError = file_get_contents($filenameToError);
        $this->assertEquals('2', $fileError);
    }
}