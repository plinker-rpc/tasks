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

use RedBeanPHP\R;

trait RedBean
{

    /**
     *
     */
    public function redbeanConnect()
    {
        if (!empty($this->task->config['database']['username'])) {
            R::setup(
                'mysql:host='.$this->task->config['database']['host'].';dbname='.$this->task->config['database']['name'],
                $this->task->config['database']['username'],
                $this->task->config['database']['password']
            );
        } elseif (!empty($this->task->config['database']['dsn'])) {
            R::setup(
                $this->task->config['database']['dsn'],
                $this->task->config['database']['username'],
                $this->task->config['database']['password']
            );
        } else {
            R::setup(
                'mysql:host='.$this->task->config['database']['host'].';dbname='.$this->task->config['database']['name']
            );
        }

        R::freeze(($this->task->config['database']['freeze'] === true));
        R::debug(($this->task->config['database']['debug'] === true));

        $this->task->state['redbeanConnected'] = true;
    }
    
    /**
     * Create
     */
    public function create($data = array())
    {
        if (!isset($data[0])) {
            throw new \Exception(__CLASS__.'::create() - First element in $data array must be key 0 => \'tablename\'');
        }

        $table = $data[0];
        unset($data[0]);

        $row = R::dispense($table);
        $row->import($data);

        return $row;
    }

    /**
     * findOrCreate
     */
    public function findOrCreate($data = array())
    {
        if (!isset($data[0])) {
            throw new \Exception(__CLASS__.'::create() - First element in $data array must be key 0 => \'tablename\'');
        }

        $table = $data[0];
        unset($data[0]);

        return R::findOrCreate($table, $data);
    }

    /**
     * Load (id)
     */
    public function load($table, $id)
    {
        return R::load($table, $id);
    }

    /**
     * Find
     */
    public function find($table = null, $where = null, $params = null)
    {
        if ($where !== null && $params !== null) {
            return R::find($table, $where, $params);
        } elseif ($where !== null && $params === null) {
            return R::find($table, $where);
        } else {
            return R::find($table);
        }
    }

    /**
     * Find One
     */
    public function findOne($table = null, $where = null, $params = null)
    {
        if ($where !== null && $params !== null) {
            return R::findOne($table, $where, $params);
        } elseif ($where !== null && $params === null) {
            return R::findOne($table, $where);
        } else {
            return R::findOne($table);
        }
    }

    /**
     * Get
     */
    public function findAll($table, $where = null, $params = null)
    {
        if ($where !== null && $params !== null) {
            return R::findAll($table, $where, $params);
        } elseif ($where !== null && $params === null) {
            return R::findAll($table, $where);
        } else {
            return R::findAll($table);
        }
    }

    /**
     * Update
     */
    public function update(\RedBeanPHP\OODBBean $row, $data = array())
    {
        $row->import($data);
        
        return $row;
    }

    /**
     * Store
     */
    public function store(\RedBeanPHP\OODBBean $row)
    {
        return R::store($row);
    }

    /**
     * Count
     */
    public function count($table, $where = null, $params = [])
    {
        return R::count($table, $where, $params);
    }

    /**
     * Export
     * Exports bean into an array
     */
    public function export(\RedBeanPHP\OODBBean $row)
    {
        return R::exportAll($row);
    }

    /**
     * Trash Row
     */
    public function trash(\RedBeanPHP\OODBBean $row)
    {
        return R::trash($row);
    }
    
    /**
     * Wipe Table
     */
    public function wipe($table)
    {
        return R::wipe($table);
    }

    /**
     * Nuke
     * Destroys database
     */
    public function nuke()
    {
        return R::nuke();
    }
    
    /**
     *
     */
    public function inspect(array $params = array())
    {
        if (!empty($params[0])) {
            return R::inspect($params[0]);
        } else {
            return R::inspect();
        }
    }
}
