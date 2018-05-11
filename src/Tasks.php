<?php
namespace Plinker\Tasks {

    use Plinker\Tasks\Lib;
    use Opis\Closure\SerializableClosure;

    class Tasks
    {
        public $config = array();

        public function __construct(array $config = array())
        {
            $this->config = $config;

            // load model
            $this->model = new Model($this->config['database']);
        }

        /**
         * Create a task
         */
        public function create(
            $name = '',
            $source = '',
            $type = '',
            $description = '',
            $params = []
        ) {
            try {
                // find or create new task source
                $tasksource = $this->model->findOrCreate([
                    'tasksource',
                    'name' => $name
                ]);
            } catch (\Exception $e) {
                return $e->getMessage();
            }

            // update - source
            $tasksource->source = str_replace("\r", "", $source);
            $tasksource->checksum = md5($source);

            if (!empty($type)) {
                $tasksource->type = strtolower($type);
            } else {
                $tasksource->type = '';
            }

            // description
            if (!empty($description)) {
                $tasksource->description = $description;
            } else {
                $tasksource->description = '';
            }

            // params
            if (!empty($params)) {
                $tasksource->params = $params;
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
        public function update(
            $id = 0,
            $name = '',
            $source = '',
            $type = '',
            $description = '',
            $params = []
        ) {
            // find or create new task source
            $tasksource = $this->model->load('tasksource', $id);

            // update - source
            $tasksource->name = $name;
            $tasksource->source = str_replace("\r", "", $source);
            $tasksource->checksum = md5($source);

            // type
            if (!empty($type)) {
                $tasksource->type = strtolower($type);
            } else {
                $tasksource->type = '';
            }

            // description
            if (!empty($description)) {
                $tasksource->description = $description;
            } else {
                $tasksource->description = '';
            }

            // params
            if (!empty($params)) {
                $tasksource->params = $params;
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
        public function get($name = '')
        {
            // get task
            return $this->model->findOne('tasksource', 'name = ?', [$name]);
        }

        /**
         *
         */
        public function status($name = '')
        {
            // find or create new task source
            $task = $this->model->findOne('tasks', 'name = ?', [
                $name
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
        public function runCount($name = '')
        {
            // find or create new task source
            $task = $this->model->findOne('tasks', 'name = ?', [
                $name
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
        public function getById($id = 0)
        {
            return $this->model->load('tasksource', $id);
        }
        
        /**
         *
         */
        public function remove($name = '')
        {
            // get task
            $row = $this->model->findOne('tasksource', 'name = ?', [$name]);
            
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
        public function removeById($id = 0)
        {
            // get task
            $row = $this->model->load('tasksource', $id);
            
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
        public function getTaskSources()
        {
            // tasks
            return $this->model->findAll('tasksource');
        }

        /**
         *
         */
        public function getTasksLog($tasksource_id = 0)
        {
            // get task
            if (!empty($id)) {
                return $this->model->findAll('tasks', 'tasksource_id = ? ORDER BY id DESC', [$tasksource_id]);
            } else {
                return $this->model->findAll('tasks');
            }
        }

        /**
         *
         */
        public function getTasks($id = 0)
        {
            // get task
            if (!empty($params[0])) {
                return $this->model->findOne('tasks', 'id = ?', [$id]);
            } else {
                return $this->model->findAll('tasks');
            }
        }

        /**
         *
         */
        public function getTasksLogCount($tasksource_id = 0)
        {
            // get task
            if (!empty($params[0])) {
                return $this->model->count('tasks', 'tasksource_id = ?', [$tasksource_id]);
            } else {
                return $this->model->count('tasks');
            }
        }

        /**
         *
         */
        public function removeTasksLog($id = 0)
        {
            // get task
            $row = $this->model->load('tasks', $id);
            $this->model->trash($row);

            return true;
        }

        /**
         * Run
         * Puts task in tasking table for deamon to run.
         */
        public function run($name = '', $params = [], $sleep = 0)
        {
            // find or create new task source
            $task = $this->model->findOrCreate([
                'tasks',
                'name'   => $name,
                'params' => json_encode($params),
                'repeats' => !empty($sleep),
                'completed' => 0
            ]);

            $task->sleep = round((empty($sleep) ? 1: $sleep));

            // get task source id
            $task->tasksource = $this->model->findOne('tasksource', 'name = ?', [$name]);

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
        public function runNow($name = '')
        {
            // get task
            $tasksource = $this->model->findOne('tasksource', 'name = ?', [$name]);

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
        public function clear()
        {
            $this->model->exec('DELETE FROM tasks');

            return true;
        }

        /**
         *
         */
        public function reset()
        {
            $this->model->nuke();

            return true;
        }
    }

}
