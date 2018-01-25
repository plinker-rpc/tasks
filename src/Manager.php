<?php
namespace Plinker\Tasks {

    use Plinker\Tasks\Lib;
    use Opis\Closure\SerializableClosure;

    class Manager
    {
        public $config = array();

        public function __construct(array $config = array())
        {
            $this->config = $config;

            // load model
            $this->model = new Model($this->config['database']);
        }

        /**
         *
         */
        public function create(array $params = array())
        {
            if (empty($params[0])) {
                return 'Error: missing first argument.';
            }

            if (empty($params[1])) {
                return 'Error: missing second argument.';
            }

            try {
                // find or create new task source
                $tasksource = $this->model->findOrCreate([
                    'tasksource',
                    'name' => $params[0]
                ]);
            } catch (\Exception $e) {
                return $e->getMessage();
            }

            // update - source
            $tasksource->source = str_replace("\r", "", $params[1]);
            $tasksource->checksum = md5($params[1]);

            if (!empty($params[2])) {
                $tasksource->type = strtolower($params[2]);
            } else {
                $tasksource->type = '';
            }

            // description
            if (!empty($params[3])) {
                $tasksource->description = $params[3];
            } else {
                $tasksource->description = '';
            }

            // description
            if (!empty($params[4])) {
                $tasksource->params = $params[4];
            } else {
                $tasksource->params = '';
            }

            // update - Newd/updated date
            if (empty($tasksource->created)) {
                $tasksource->updated = date_create()->format('Y-m-d h:i:s');
                $tasksource->created = date_create()->format('Y-m-d h:i:s');
            } else {
                $tasksource->updated = date_create()->format('Y-m-d h:i:s');
            }

            // store
            $this->model->store($tasksource);

            //
            return $this->model->export($tasksource)[0];
        }

        /**
         *
         */
        public function update(array $params = array())
        {
            if (empty($params[0])) {
                return 'Error: missing first argument.';
            }

            if (!is_numeric($params[0])) {
                return 'Error: first argument must be the task id.';
            }

            if (empty($params[1])) {
                return 'Error: missing second argument.';
            }

            if (empty($params[2])) {
                return 'Error: missing third argument.';
            }

            // find or create new task source
            $tasksource = $this->model->load('tasksource', $params[0]);

            // update - source
            $tasksource->name = $params[1];
            $tasksource->source = str_replace("\r", "", $params[2]);
            $tasksource->checksum = md5($params[2]);

            // type
            if (!empty($params[3])) {
                $tasksource->type = strtolower($params[3]);
            } else {
                $tasksource->type = '';
            }

            // description
            if (!empty($params[4])) {
                $tasksource->description = $params[4];
            } else {
                $tasksource->description = '';
            }

            // params
            if (!empty($params[5])) {
                $tasksource->params = $params[5];
            } else {
                $tasksource->params = '';
            }

            // set updated/created date
            if (empty($tasksource->created)) {
                $tasksource->updated = date_create()->format('Y-m-d H:i:s');
                $tasksource->created = date_create()->format('Y-m-d H:i:s');
            } else {
                $tasksource->updated = date_create()->format('Y-m-d H:i:s');
            }

            // store
            $this->model->store($tasksource);

            //
            return $this->model->export($tasksource)[0];
        }

        /**
         *
         */
        public function get(array $params = array())
        {
            // get task
            return $this->model->findOne('tasksource', 'name = ?', [$params[0]]);
        }

        /**
         *
         */
        public function status(array $params = array())
        {
            // find or create new task source
            $task = $this->model->findOne('tasks', 'name = ?', [
                $params[0]
            ]);

            if (empty($task->id)) {
                return 'not found';
            }

            if (!empty($task->completed)) {
                return 'completed';
            }

            if (!empty($task->repeats) && empty($task->completed)) {
                return 'running';
            }

            return false;
        }

        /**
         *
         */
        public function runCount(array $params = array())
        {
            // find or create new task source
            $task = $this->model->findOne('tasks', 'name = ?', [
                $params[0]
            ]);

            if (empty($task->id)) {
                return 0;
            }

            if (!empty($task->run_count)) {
                return $task->run_count;
            }

            return 0;
        }

        /**
         *
         */
        public function getById(array $params = array())
        {
            return $this->model->load('tasksource', $params[0]);
        }
        
        /**
         *
         */
        public function remove(array $params = array())
        {
            // get task
            $row = $this->model->findOne('tasksource', 'name = ?', [$params[0]]);
            
            if (empty($row->id)) {
                return 'not found';
            }

            // remove all tasks
            foreach ($row->ownTasks as $tasks) {
                $this->model->trash($tasks);
            }

            // remove task
            $this->model->trash($row);

            return true;
        }

        /**
         *
         */
        public function removeById(array $params = array())
        {
            // get task
            $row = $this->model->load('tasksource', $params[0]);
            
            if (empty($row->id)) {
                return 'not found';
            }

            // remove all tasks
            foreach ($row->ownTasks as $tasks) {
                $this->model->trash($tasks);
            }

            // remove task
            $this->model->trash($row);

            return true;
        }

        /**
         *
         */
        public function getTaskSources(array $params = array())
        {
            // tasks
            return $this->model->findAll('tasksource');
        }

        /**
         *
         */
        public function getTasksLog(array $params = array())
        {
            // get task
            if (!empty($params[0])) {
                return $this->model->findAll('tasks', 'tasksource_id = ? ORDER BY id DESC', [$params[0]]);
            } else {
                return $this->model->findAll('tasks');
            }
        }

        /**
         *
         */
        public function getTasks(array $params = array())
        {
            // get task
            if (!empty($params[0])) {
                return $this->model->findOne('tasks', 'id = ?', [$params[0]]);
            } else {
                return $this->model->findAll('tasks');
            }
        }

        /**
         *
         */
        public function getTasksLogCount(array $params = array())
        {
            // get task
            if (!empty($params[0])) {
                return $this->model->count('tasks', 'tasksource_id = ?', [$params[0]]);
            } else {
                return $this->model->count('tasks');
            }
        }

        /**
         *
         */
        public function removeTasksLog(array $params = array())
        {
            // get task
            $row = $this->model->load('tasks', $params[0]);
            $this->model->trash($row);

            return true;
        }

        /**
         * Run
         * Puts task in tasking table for deamon to run.
         */
        public function run(array $params = array())
        {
            // find or create new task source
            $task = $this->model->findOrCreate([
                'tasks',
                'name'   => $params[0],
                'params' => json_encode($params[1]),
                'repeats' => !empty($params[2]),
                'completed' => 0
            ]);

            $task->sleep = round((empty($params[2]) ? 1: $params[2]));

            // get task source id
            $task->tasksource = $this->model->findOne('tasksource', 'name = ?', [$params[0]]);

            if (empty($task->completed) && empty($task->result)) {
                // store task
                $this->model->store($task);
                return [];
            }

            // store task
            $this->model->store($task);

            return $this->model->export($task)[0];
        }

        /**
         * Run Now
         * Does not put task in tasking table for deamon to run.
         * note: will be run as apache user
         */
        public function runNow(array $params = array())
        {
            // get task
            $tasksource = $this->model->findOne('tasksource', 'name = ?', [$params[0]]);

            if (empty($tasksource)) {
                return ['error' => 'Task not found'];
            }

            if (empty($tasksource->source)) {
                $this->model->trash($tasksource);
                return ['error' => 'Task has no source, task has been removed'];
            }

            //
            if ($tasksource->type == 'php') {
                ob_start();
                eval('?>'.$tasksource->source);
                return ob_get_clean().$return;
            } elseif ($tasksource->type == 'bash') {
                $filename = (!empty($this->config['tmp_path']) ? $this->config['tmp_path'] : './.plinker').'/bash/'.md5($task->tasksource->name).'.sh';
                file_put_contents($filename, $task->tasksource->source);
                ob_start();
                echo shell_exec('/bin/bash '.$filename);
                return ob_get_clean();
            }

            return 'Invalid task type';
        }

        /**
         *
         */
        public function clear(array $params = array())
        {
            $this->model->exec('DELETE FROM tasks');

            return true;
        }

        /**
         *
         */
        public function reset(array $params = array())
        {
            $this->model->nuke();

            return true;
        }

    }

}
