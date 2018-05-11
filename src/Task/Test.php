<?php
/*
 +------------------------------------------------------------------------+
 | Plinker-RPC PHP                                                        |
 +------------------------------------------------------------------------+
 | Copyright (c)2017-2018 (https://github.com/plinker-rpc/core)           |
 +------------------------------------------------------------------------+
 | This source file is subject to MIT License                             |
 | that is bundled with this package in the file LICENSE.                 |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@cherone.co.uk so we can send you a copy immediately.        |
 +------------------------------------------------------------------------+
 | Authors: Lawrence Cherone <lawrence@cherone.co.uk>                     |
 +------------------------------------------------------------------------+
 */
 
namespace Plinker\Tasks\Task {

    //use Plinker\Tasks\Lib;

    /**
     * An example task, which prints "Hello Task!"
     * @usage:
     *    $task->run('Test');
     *    $task->daemon('Test');
     */
    class Test
    {
        //use Lib\Traits\RedBean;

        /**
         *
         */
        public function __construct(\Plinker\Tasks\Runner $task)
        {
            $this->task = $task;
        }

        /**
         * Main execute method - called by task runner
         */
        public function execute()
        {
            echo 'Hello Task!'.PHP_EOL;
        }
    }
}
