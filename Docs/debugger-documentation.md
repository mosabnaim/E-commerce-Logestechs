# Logestechs_Debugger Documentation

This is a utility class provided by the Logestechs WordPress plugin to facilitate logging and debugging tasks.

## Instantiating the debugger

Instantiate the Logestechs_Debugger class as follows:

```php
$debugger = new Logestechs_Debugger();
```

## Logging a Message

Use the `log` method to log a message. The first argument is the message to log, and the second argument is optional and specifies the log type. If the message is an array, it will be iterated over and logged with keys transformed to capitalized words.

```php
$debugger->log('This is a log message', 'Log');
```

You can also log an array:

```php
$debugger->log([
    'key1' => 'value1',
    'key2' => 'value2'
], 'Log');
```

The `log` method can be chained:

```php
$debugger->log('This is a log message', 'Log')
    ->log('This is another log message', 'Log');
```

## Writing Logs

The `write` method writes the logs to a file:

```php
$debugger->write();
```

## Displaying Logs

Use the `display` method to display the logs. If the `$from_file` argument is true, it will display the logs from the log file; otherwise, it will display the current logs in memory:

```php
$debugger->display();      // Displays current logs in memory
$debugger->display(true);  // Displays logs from file
```

## Clearing Logs

Use the `clear` method to clear the log file:

```php
$debugger->clear();
```

## Logging Database Queries

Use the `log_db_queries` method to log all WordPress database queries:

```php
$debugger->log_db_queries();
```

## Logging HTTP Requests and Responses

Use the `log_http` method to log HTTP requests and responses:

```php
$debugger->log_http($request, $response);
```

## Logging Environment Information

Use the `log_env_info` method to log key environment details:

```php
$debugger->log_env_info();
```

## Timing a Function Execution

Use the `time_function_execution` method to time the execution of a function and log it:

```php
$debugger->time_function_execution(function() {
    // Some function...
}, 'Description of the function');
```

## Logging Memory Usage

Use the `log_memory_usage` method to log the current memory usage:

```php
$debugger->log_memory_usage('Description of the memory usage');
```
Certainly! Here's how you would document it:


## Start Timer

The `start_timer`, `split_timer`, and `stop_timer` methods are used to time and log the execution of code:

```php
$debugger->start_timer()
// Some code
$debugger->split_timer('End of first part of the code block')
// Some code
$debugger->stop_timer('End of entire code block')->display();
```

At the end of each `split_timer` or `stop_timer` call, the time taken since the last split or since the start, respectively, is logged. The message argument is optional and can be used to describe what was timed.

## Chaining Multiple Logging Methods

The Logestechs_Debugger class has been designed to allow method chaining. This is done to make it easy to log multiple events and perform various actions with less verbosity in your code. Here is an example of how this works:

```php
$debugger = new Logestechs_Debugger();
$debugger->log('A sample log event 1')->log('A sample log event 2')->write();

$debugger2 = new Logestechs_Debugger();
$debugger2->log('Another sample log event 1')->log('Another sample log event 2')->write()->display(true);
```

In the first code block, two log events are created, `A sample log event 1` and `A sample log event 2`, and then they are written to the log by calling the `write` method.

In the second block, two different log events are created, `Another sample log event 1` and `Another sample log event 2`, then they are written to the log and displayed to the screen by calling `write` and `display` methods respectively. The `display` method is called with a parameter `true` which means it will retrieve and display the logs from the log file. 

## Method Chaining

You can chain method calls in PHP when your method returns an object, usually its own `$this`. The object returned by one method call is then used for the next method call. You can see this in the above example where we chain the calls to `log()`, `write()`, and `display()`. 

This is commonly known as a Fluent Interface and can make code more readable and succinct, as it avoids repetitive variable assignments. 

Please note that when using this approach, you need to ensure that the methods used in the chain should return the object (`$this` in most cases) otherwise you might encounter issues with method calls on non-object results. 

Method chaining works best when actions modify the state of the object, or act on the same set of data. In this case, logging events and writing them to a log file are good examples of such actions.

Here are some examples of how you can chain methods from the `Logestechs_Debugger` class.

### Example 1: Logging an error and then writing the log to the file
```php
$debugger = new Logestechs_Debugger();
try {
    // Some code that can throw an exception
} catch ( Exception $e ) {
    $debugger->log( $e, 'Error' )->write();
}
```
In this example, the `log()` method logs the error and the `write()` method writes the logs to the file. The methods are chained together in a single statement.

### Example 2: Logging database queries and then displaying them in the debug modal
```php
$debugger = new Logestechs_Debugger();
$debugger->log_db_queries()->display();
```
Here, the `log_db_queries()` method logs the database queries and the `display()` method displays them in the debug modal.

### Example 3: Logging environment information, memory usage, and writing them to the file


This line of code clears the current logs, collects and records the system environment information and memory usage, then writes all this information into a log file for debugging purposes.
```php
$debugger = new Logestechs_Debugger();
$debugger->clear()->log_env_info()->log_memory_usage()->write();
```
In this example, the `log_env_info()` method logs the environment information, the `log_memory_usage()` method logs the memory usage, and the `write()` method writes them to the file.

