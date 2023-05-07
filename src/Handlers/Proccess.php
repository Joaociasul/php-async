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
     * @throws InvalidArgumentException If any element of $callables is not a valid callable.
     * @return void
     */
    public static function make(array $callables, int $limitForExecution = 10): void
    {
        $event = EventsHelper::getInstance();
        $running = [];
        $count = 1;
        foreach ($callables as $key => $callable) {
            ValidationHelper::throwIfNotCallable($callable);
            $async = new Async();
            try {
                $async->call($callable);
            } catch (Exception $exception) {
                $event->dispatch(self::EVENT_ERROR . $key, [
                    'exception' => $exception
                ]);
                exit;
            }
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
        $event = EventsHelper::getInstance();
        foreach ($proccesses as $key => $proccess) {
            $proccess->wait();
            $event->dispatch(self::EVENT_SUCCESS . $key, [
                'uuid' => $proccess->getProcessKey(),
                'array_key' => $key,
            ]);
            unset($proccesses[$key]);
        }
    }
}
