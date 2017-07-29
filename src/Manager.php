<?php
namespace Plinker\Tasks {

    use RedBeanPHP\R;
    use Opis\Closure\SerializableClosure;

    class Manager {

        public $config = array();
        private $tab;

        public function __construct(array $config = array())
        {
            $this->config = $config;
            
            // load model
            $this->model = new Model($this->config['database']);
        }
        
        public function log(array $params = array())
        {
            return $this->model->findAll('log');
        }
        
        /**
         * 
         */
        public function create(array $params = array())
        {
            return 'Task created';
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
            return 'Task info';
        }

        /**
         * 
         */
        public function stop(array $params = array())
        {
            return 'Task stop';
        }

        /**
         * 
         */
        public function clear(array $params = array())
        {
            return 'Task clear';
        }

        

        // /**
        //  * 
        //  */
        // public function announce(array $params = array())
        // {
        //     if (empty($params[0])) {
        //         return new \Exception('Peer callback url is required.');
        //     }
            
        //     $this->model->store($this->model->create(['log', 'time' => time(), 'action' => 'announce', 'ip' => $this->getIPAddress()]));
            
            
        //     $peer = $this->model->findOrCreate([
        //         'peer',
        //         'ip' => $this->getIPAddress(),
        //         'peer' => $params[0]
        //     ]);

        //     $peer->token = hash('sha256', (
        //         $_SERVER['REMOTE_ADDR'].$params[0].microtime(true)
        //     ));
            
        //     $this->model->store($peer);
            
        //     return $peer;
        // }
        
        // public function peers(array $params = array())
        // {
        //     $this->model->store($this->model->create(['log', 'time' => time(), 'action' => 'peers', 'ip' => $this->getIPAddress()]));
            
        //     $peer = @$params[0];

        //     if (!empty($peer['token'])) {
        //         // find peer
        //         $peer = $this->model->findOne('peer', 'token = ?', [
        //             $peer['token']
        //         ]);
                
        //         if (empty($peer)) {
        //             return new \Exception('Unauthorized');
        //         }
                
        //         return $this->model->findAll('peer');
        //     } else {
        //         return new \Exception('Peer announce record empty.');
        //     }
        // }
        
        // public function broadcast(array $params = array())
        // {
        //     $this->model->store($this->model->create(['log', 'time' => time(), 'action' => 'broadcast', 'ip' => $this->getIPAddress()]));
            
        //     $peer = @$params[0];

        //     if (!empty($peer['token'])) {
                
        //         // find peer
        //         $peer = $this->model->findOne('peer', 'token = ?', [
        //             $peer['token']
        //         ]);
                
        //         if (empty($peer)) {
        //             return new \Exception('Unauthorized');
        //         }
                
        //         $peers =  $this->model->findAll('peer');
                
        //         foreach ($peers as $id => $own_peer) {
        //             $peer_network = new \Plinker\Core\Client(
        //                 $own_peer->peer,
        //                 'Peer\Peer',
        //                 hash('sha256', gmdate('h').$this->config['plinker']['public_key']),
        //                 hash('sha256', gmdate('h').$this->config['plinker']['private_key']),
        //                 $this->config,
        //                 $this->config['plinker']['encrypted']
        //             );
                    
        //             $result[$id] = $peer_network->announce($peer['peer']);
                    
        //             $result[$id]['response'] = $peer_network->{@$params[1]}($result[$id]);
        //         }

        //     } else {
        //         return new \Exception('Peer announce record empty.');
        //     }

        //     return $result;
        // }

        // public function disconnect(array $params = array())
        // {
        //     $peer =  $this->model->findOrCreate([
        //         'peer',
        //         'ip' => $this->getIPAddress()
        //     ]);
            
        //     $this->model->trash($peer);
            
        //     return true;
        // }
        
        // public function testClosure($params = array())
        // {
        //     $test = function ($what) {
        //         return $what.' - Thanks buddy...';
        //     };
    
        //     return new SerializableClosure($test);
        // }

    }

}
