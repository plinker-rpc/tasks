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
 
namespace Plinker\Tasks\Lib\Traits;

trait Log
{

    /**
     *
     */
    public function __construct()
    {
        $this->log_path = realpath($this->log_path);
    }

    /**
     * Log (Monolog)
     *
     * @param string $str  - Log message
     * @param string $type - Log severity type [emergency, alert, critical, error, warning, notice, info, debug]
     * @param array $data - Array of data
     */
    public function log($str, $type = 'notice', $data = [])
    {
        $log = new \Monolog\Logger('task_log');
        
        //add file handler - log to file
        $log->pushHandler(
            new \Monolog\Handler\StreamHandler('logs/'.date('Y-m-d').'.log')
        );
        
        $method = 'add'.$type;
        if (method_exists($log, $type)) {
            $log->$method($str, $data);
        }
    }
}
