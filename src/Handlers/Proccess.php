<?php

namespace Joaociasul\PhpAsync\Handlers;

use Exception;
use InvalidArgumentException;
use Joaociasul\PhpAsync\Exceptions\PidException;
use Joaociasul\PhpAsync\Helpers\EventsHelper;
use Joaociasul\PhpAsync\Helpers\ValidationHelper;

class Proccess
{
    public const EVENT_SUCCESS = 'executed.';

    public const EVENT_ERROR = 'error.';

    /**
     * Calls the given callbacks in parallel using asynchronous processes.
     * After each callback execution, it will trigger an event "executed." + $arrayKeyOfCallables.
     * If an error occurs during the callback execution, it will trigger an event "error." + $arrayKeyOfCallables.
     *
     * @param array{
     *     callable
     * } $callables An array of callbacks to call.
     * @param int $limitForExecution The maximum number of simultaneous executions.
     *
     * @return void
     * @throws PidException
     * @throws InvalidArgumentException If any element of $callables is not a valid callable.
     */
    public static function make(array $callables, int $limitForExecution = 10): void
    {
        $event = EventsHelper::getInstance();
        $running = [];
        $count = 1;
        foreach ($callables as $key => $callable) {
            ValidationHelper::throwIfNotCallable($callable);
            $async = new Async();
            $async->setEventsHelper($event);
            $async->call($callable, $key);
            $running[$key] = $async;
            if ($count >= $limitForExecution) {
                self::await($running);
                $count = 1;
                continue;
            }
            $count++;
        }
        self::await($running);
    }

    /**
     * @param array{
     *     proccess:Async
     * } $proccesses
     * @return void
     */
    private static function await(array &$proccesses)
    {
        foreach ($proccesses as $key => $proccess) {
            $proccess->wait();
            unset($proccesses[$key]);
        }
    }
}
