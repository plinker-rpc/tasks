**Plinker-RPC - Tasks**
=========

Plinker PHP RPC client/server makes it really easy to link and execute PHP component classes on remote systems, while maintaining the feel of a local method call.

A tasks component which manages a couple of database tables which a task agent runs over and completes.

**Composer**

    {
    	"require": {
    		"plinker/core": ">=v0.1",
    		"plinker/tasks": ">=v0.1"
    	}
    }



Making a remote call.
--------------------

WIP: To be updated with info on how to use this component, also add the tasks code.


    <?php
    require 'vendor/autoload.php';

    /**
     * Initialize plinker client.
     *
     * @param string $url to host
     * @param string $component namespace of class to interface to
     * @param string $public_key to authenticate on host
     * @param string $private_key to authenticate on host
     * @param string $config component construct config
     */
    $plink = new Plinker\Core\Client(
        'http://example.com',
        'Test\Demo',
        'username',
        'password',
        array(
            'time' => time()
        )
    );
    echo '<pre>'.print_r($plink->test(), true).'</pre>';


**then the server part...**

    <?php
    require 'vendor/autoload.php';

    /**
     * POST Server Part
     */
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $server = new Plinker\Core\Server(
            $_POST,
            'username',
            'password'
        );
        exit($server->execute());
    }

See the [organisations page](https://github.com/plinker-rpc) for additional components.