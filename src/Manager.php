<?php
namespace Plinker\Tasks {

    use Opis\Closure\SerializableClosure;

    class Manager {

        public $config = array();
        private $tab;

        public function __construct(array $config = array())
        {
            $this->config = $config;
            
            // load model
            $this->model = new Model($this->config['database']);
            //$this->task = new Task($this->config['database']);
        }

        /**
         * 
         */
        public function create(array $params = array())
        {
            if ($params[0] == 'task') {

                // find or create new task source
                $tasksource = $this->model->findOrCreate([
                    'tasksource',
                    'name' => $params[1][0]
                ]);
                        
                // update - source
                $tasksource->source = $params[1][1];
                        
                // update - created/updated date
                if (empty($tasksource->created)) {
                    $tasksource->created = date_create()->format('Y-m-d h:i:s'); 
                } else {
                    $tasksource->updated = date_create()->format('Y-m-d h:i:s'); 
                }
                        
                // store
                $this->model->store($tasksource);

                //
                return $this->model->export($tasksource)[0];
            }
            
            return '$tasks->create(\''.$params[0].'\,...\') - Cant do that';
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
                'completed' => 0
            ]);

            // get task source id
            $task->tasksource = $this->model->findOne('tasksource', 'name = ?', [$params[0]]);
            
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
            
            $source = unserialize($tasksource->source);
            
            $return = null;
            ob_start();
            $return = $source($params[1]);
            $return = ob_get_contents().$return;
            ob_end_clean();
            
            return $return;
        }

        /**
         * 
         */
        public function status(array $params = array())
        {
            return 'Task status';
        }
        
        /**
         * 
         */
        public function info(array $params = array())
        {
            return $this->model->findOne('tasks', 'id = ?', [
                $params[0]
            ]);
        }
        
        /**
         * 
         */
        public function clear(array $params = array())
        {
            $this->model->exec('DELETE FROM tasks');
            
            return true;
        }

    }

}
