<?php

namespace Joaociasul\PhpAsync\Helpers;

class EventsHelper
{
    private static $instance;
    private static $listeners = [];

    private function __construct() {}

    public static function getInstance(): EventsHelper {
        self::$instance = self::$instance ?? new self();
        return self::$instance;
    }

    /**
     * Registers a callback function to be executed when the specified event is triggered.
     *
     * @param string $event The name of the event to listen to.
     * @param callable $callback The callback function to be executed when the event is triggered.
     * @return void
     */
    public function listen(string $event, callable $callback): void {
        self::$listeners[$event][] = $callback;
    }

    /**
     * Dispatches an event to its listeners with optional arguments.
     *
     * @param string $event The name of the event to dispatch.
     * @param array $args Optional arguments to pass to the event listeners.
     * @return void
     */
    public function dispatch(string $event, array $args = []): void {
        if (isset(self::$listeners[$event])) {
            foreach (self::$listeners[$event] as $callback) {
                call_user_func($callback, $args);
            }
        }
    }
}
