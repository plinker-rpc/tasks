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

    use Plinker\Tasks\Lib;

    /**
     *
     */
    class Queue
    {
        use Lib\Traits\RedBean;

        /**
         *
         */
        public function __construct(\Plinker\Tasks\Runner $task)
        {
            $this->task = $task;

            // Hook into RedBean using Traits\RedBean
            if (empty($this->task->state['redbeanConnected'])) {
                $this->redbeanConnect();
            }
        }

        /**
         * Main execute method - called by task runner
         */
        public function execute()
        {
            // create auto update update task (composer update)
            if (!empty($this->task->config['auto_update']) && is_numeric($this->task->config['auto_update'])) {
                if ($this->count('tasks', 'name = "tasks.auto_update"') == 0) {
                    $this->tasks = new \Plinker\Tasks\Tasks($this->task->config);
                    // add
                    $task['tasks.auto_update'] = $this->tasks->create(
                        // name
                        'tasks.auto_update',
                        // source
                        "#!/bin/bash\ncomposer update plinker/tasks",
                        // type
                        'bash',
                        // description
                        'Plinker tasks auto update',
                        // default params
                        []
                    );
                    // run task
                    $this->tasks->run('tasks.auto_update', [], $this->task->config['auto_update']);
                }
            }

            // find tasks
            $tasks = $this->find('tasks', ' (completed IS NULL OR completed = "" OR completed = 0) ORDER BY id ASC ');

            try {
                if (!empty($this->task->config['debug'])) {
                    $this->task->console->out(
                        '<light_blue><bold><underline>Tasks:</underline></bold></light_blue>'
                    );
                }

                foreach ($tasks as $task) {
                    
                    //
                    if (!empty($task->run_last) && !empty($task->repeats)) {
                        if ((strtotime($task->run_last)+$task->sleep) > strtotime(date_create()->format('Y-m-d H:i:s'))) {
                            if (!empty($this->task->config['debug'])) {
                                $this->task->console->out(
                                    '<light_red>Waiting ('.(strtotime($task->run_next)-strtotime(date_create()->format('Y-m-d H:i:s'))).'): '.$task->name/*.' - '.$task->params*/.'</light_red>'
                                );
                            }
                            continue;
                        }
                    }

                    //
                    if (!empty($this->task->config['debug'])) {
                        $this->task->console->out(
                            '<light_green><bold>Running    : '.$task->name/*.' - '.$task->params*/.'</bold></light_green>'
                        );
                    }

                    // check has got source
                    if (!empty($task->tasksource_id)) {

                        //
                        if (empty($task->repeats)) {
                            $task->completed = date_create()->format('Y-m-d H:i:s');
                            $task->run_last = date_create()->format('Y-m-d H:i:s');
                        } else {
                            $task->run_last = date_create()->format('Y-m-d H:i:s');
                            $task->run_next = date_create()->modify("+".$task->sleep." seconds")->format('Y-m-d H:i:s');
                        }
                        
                        $task->run_count = (empty($task->run_count) ? 1 : (int) $task->run_count + 1);

                        //
                        $params = json_decode($task->params, true);

                        //
                        $return = null;
                        if ($task->tasksource->type == 'php') {
                            ob_start();
                            $source = $task->tasksource->source;
                            eval('?>'.$source);
                            $task->result = ob_get_clean();
                        } elseif ($task->tasksource->type == 'bash') {
                            $filename = (!empty($this->task->config['tmp_path']) ? $this->task->config['tmp_path'] : './.plinker').'/bash/'.md5($task->tasksource->name).'.sh';
                            file_put_contents($filename, $task->tasksource->source);
                            ob_start();
                            echo shell_exec('/bin/bash '.$filename);
                            $task->result = ob_get_clean();
                        }
                        
                        /*
                        if (!empty($this->task->config['debug'])) {
                            print_r($task->result);
                        }
                        */

                        $this->store($task);
                    } else {
                        $this->trash($task);
                        if (!empty($this->task->config['debug'])) {
                            $this->task->console->out(
                                '<light_blue><bold>Task has no source.</bold></light_blue>'
                            );
                        }
                    }
                }
            } catch (\Exception $e) {
                // ...
            }
        }
    }
}
