## Php Async

The purpose of this package is to execute tasks in parallel with PHP. You can pass callback functions and a limit for simultaneous executions. This package can be useful in scenarios where you need to process large amounts of data or perform multiple API requests simultaneously to reduce overall processing time and improve application performance.

### Basic Usage
Are you looking for a way to speed up your PHP applications and process data more efficiently? With the Parallel PHP package, you can execute multiple tasks in parallel using callback functions, and limit the number of simultaneous executions to optimize performance. In this example, we'll show you how to use the package to process a large dataset and improve your application's processing time.
```php
use JoaoRoyer\PhpAsync\Handlers\Proccess;
use JoaoRoyer\PhpAsync\Helpers\EventsHelper;

$proccesses = [];
$event = EventsHelper::getInstance();
for ($i = 1; $i <= 105; $i++) {
   $proccesses[$i] = function () {
       sleep(1);
   };
    $event->listen(Proccess::EVENT_SUCCESS . $i, function (array $args) {
        print_r($args);
    });
    $event->listen(Proccess::EVENT_ERROR . $i, function (array $args) {
        print_r($args);
    });
}

Proccess::make($proccesses, 12);
```
This code imports the Proccess class from the JoaoRoyer\PhpAsync\Handlers namespace and the EventsHelper class from the JoaoRoyer\PhpAsync\Helpers namespace. It then creates an empty array called $proccesses and instantiates an EventsHelper object called $event.

A for loop is then used to populate the $proccesses array with 105 anonymous functions that each contain a sleep(1) call. The anonymous functions represent the tasks that will be executed in parallel by the Proccess::make() method.

Two event listeners are also created for each task, one for a successful execution and one for an error. The event listeners use the Proccess::EVENT_SUCCESS and Proccess::EVENT_ERROR constants to dynamically generate event names based on the index of the current task in the $proccesses array.

Finally, the Proccess::make() method is called with the $proccesses array and a limit of 12 simultaneous executions. This will execute the tasks in parallel, up to a maximum of 12 tasks at a time, and trigger the appropriate event listeners when each task completes or encounters an error.

