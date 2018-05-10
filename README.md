# PlinkerRPC - Tasks

The tasks component allows you to write code based tasks which are completed by a daemon, 
this could allow you to create a single interface to control a cluster of servers tasks.

## Install

Require this package with composer using the following command:

``` bash
$ composer require plinker/tasks
```

## CRON Daemon

You should create a file which will be run via cron:

**cron.php**

    <?php
    require 'vendor/autoload.php';

    if (php_sapi_name() != 'cli') {
        header('HTTP/1.0 403 Forbidden');
        exit('CLI script');
    }

    $task = new Plinker\Tasks\Runner([
        'database' => [
            'dsn'      => 'sqlite:./.plinker/database.db',
            'host'     => '',
            'name'     => '',
            'username' => '',
            'password' => '',
            'freeze'   => false,
            'debug'    => false
        ],
        'debug'       => true,
        'log'         => true,
        'sleep_time'  => 2,
        'tmp_path'    => './.plinker',
        'auto_update' => 86400
    ]);
    
    $task->daemon('Queue');
    
Then add a cron job:

 - `@reboot while sleep 1; do cd /var/www/html/examples/tasks && /usr/bin/php run.php ; done`


## Client

Creating a client instance is done as follows:


    <?php
    require 'vendor/autoload.php';

    /**
     * Initialize plinker client.
     *
     * @param string $server - URL to server listener.
     * @param string $config - server secret, and/or a additional component data
     */
    $client = plinker_client('http://example.com/server.php', 'a secret password', [
        // database connection
        'database' => [
            'dsn'      => 'sqlite:./.plinker/database.db',
            'host'     => '',
            'name'     => '',
            'username' => '',
            'password' => '',
            'freeze'   => false,
            'debug'    => false,
        ],
        // displays output to task runner console
        'debug' => true,
    
        // daemon sleep time
        'sleep_time' => 1,
        'tmp_path'   => './.plinker'
    ]);
    
    // or using global function
    $client = plinker_client('http://example.com/server.php', 'a secret password', [
        // database connection
        'database' => [
            'dsn'      => 'sqlite:./.plinker/database.db',
            'host'     => '',
            'name'     => '',
            'username' => '',
            'password' => '',
            'freeze'   => false,
            'debug'    => false,
        ],
        // displays output to task runner console
        'debug' => true,
    
        // daemon sleep time
        'sleep_time' => 1,
        'tmp_path'   => './.plinker'
    ]);
    

## Example

    // create the task
    try {
    	// create task
    	$client->tasks->create(
    		// name
    		'Hello World',
    		// source
    		'<?php echo "Hello World";',
    		// type
    		'php',
    		// description
    		'...',
    		// default params
    		[]
    	);
    } catch (\Exception $e) {
    	if ($e->getMessage() == 'Unauthorised') {
    		echo 'Error: Connected successfully but could not authenticate! Check public and private keys.';
    	} else {
    		echo 'Error:'.str_replace('Could not unserialize response:', '', trim(htmlentities($e->getMessage())));
    	}
    }
    
    //run task now - executed as apache user
    $client->tasks->runNow('Hello World');
    
    // place task in queue to run every 5 seconds
    $client->tasks->run('Hello World', [1], 5);
    
    // get task status
    $client->tasks->status('Hello World');
    
    // get task run count
    $client->tasks->runCount('Hello World');
    
    // clear all tasks
    $client->tasks->clear();
    

## Methods

Once setup, you call the class though its namespace to its method.

### List

....

| Parameter   | Type           | Description   | Default        |
| ----------  | -------------  | ------------- |  ------------- | 
| dir         | string         | Base path to list files and folders from | `./` |
| extended    | bool           | Return extended fileinfo | `false` |
| depth       | int            | Iterator depth | `10` |


**Call**
``` php
$result = $client->files->list('./', false, 10);
```

**Response**
``` text

```

## Testing

There are no tests setup for this component.

## Contributing

Please see [CONTRIBUTING](https://github.com/plinker-rpc/files/blob/master/CONTRIBUTING) for details.

## Security

If you discover any security related issues, please contact me via [https://cherone.co.uk](https://cherone.co.uk) instead of using the issue tracker.

## Credits

- [Lawrence Cherone](https://github.com/lcherone)
- [All Contributors](https://github.com/plinker-rpc/files/graphs/contributors)

## Links

Want to see an example project which uses this component?

 - [PlinkerUI](https://github.com/lcherone/PlinkerUI)


## Development Encouragement

If you use this project and make money from it or want to show your appreciation,
please feel free to make a donation [https://www.paypal.me/lcherone](https://www.paypal.me/lcherone), thanks.

## Sponsors

Get your company or name listed throughout the documentation and on each github repository, contact me at [https://cherone.co.uk](https://cherone.co.uk) for further details.

## License

The MIT License (MIT). Please see [License File](https://github.com/plinker-rpc/files/blob/master/LICENSE) for more information.

See the [organisations page](https://github.com/plinker-rpc) for additional components.
