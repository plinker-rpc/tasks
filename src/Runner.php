<?php
namespace Plinker\Tasks;

use League\CLImate\CLImate;

/**
 *
 */
class Runner
{
    public $vars   = [];
    public $state  = [];
    public $config = [];

    /**
     * @param array $config - Config which you want to pass to the task.
     */
    public function __construct($config = [
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
        'debug' => false,
        
        // log output to file ./logs/d-m-Y.log
        'log' => false,
        
        // daemon sleep time
        'sleep_time' => 1,
        'tmp_path'   => './.plinker'
    ])
    {
        $this->config = $config;
        $this->constructBootTime = microtime(true);
        $this->console = new CLImate;
    }

    /**
     * Setter
     */
    public function __set($index, $value)
    {
        $this->vars[$index] = $value;
    }

    /**
     * Getter
     */
    public function __get($index)
    {
        return $this->vars[$index];
    }

    /**
     * Run once
     *
     * @param string $class - Name of the task class in /src/task/*.
     * @param array $config - Config which you want to pass to the task.
     */
    public function run($class, $config = [])
    {
        $this->config = $this->config + $config;

        $this->class = __NAMESPACE__ . '\\Task\\' . $class;

        $this->task = new $this->class($this);

        if (!empty($this->config['debug']) && !isset($this->config['tools'])) {
            $this->console->out(
                "<bold><red>DEBUG MODE ENABLED:</red></bold>\n".
                " - <underline>Turn off debug in production</underline>, which will stop this output to the console."
            );
        }

        $this->task->execute();
    }

    /**
     * Daemon - run continuously for 1 minute.
     *
     * @param string $class - Name of the task class in /src/task/*.
     * @param array $config - Config which you want to pass to the task.
     */
    public function daemon($class, $config = [])
    {
        $this->config = (array) $config + (array) $this->config;
        
        // check tmp path exists
        if (!file_exists((!empty($this->config['tmp_path']) ? $this->config['tmp_path'] : './.plinker'))) {
            mkdir((!empty($this->config['tmp_path']) ? $this->config['tmp_path'] : './.plinker'), 0755, true);
            file_put_contents((!empty($this->config['tmp_path']) ? $this->config['tmp_path'] : './.plinker/.htaccess'), 'deny from all');
            chown((!empty($this->config['tmp_path']) ? $this->config['tmp_path'] : './.plinker'), 'www-data');
        }

        // init pid/lock file
        $pid = new Lib\PID((!empty($this->config['tmp_path']) ? $this->config['tmp_path'] : './.plinker'), $class);

        $sleep_time = !empty($this->config['sleep_time']) ? $this->config['sleep_time'] : 1;

        if ($pid->running) {
            if (!empty($this->config['debug'])) {
                $this->console->out(
                    "<bold><red>DEBUG MODE ENABLED:</red></bold>\n".
                    " - <underline>Turn off debug in production</underline>, which will stop this output to the console.\n".
                    " - Initial Memory usage: ".$pid->script_memory_usage().".\n".
                    " - Sleep time: $sleep_time second between iterations.\n".
                    " - <red>Exited, process already running!</red>"
                );
            }
            exit;
        } else {
            $startTime = microtime(true);
            $stopTime = $startTime + 59;

            if ($this->config['debug']) {
                $i = 1;
            }

            while (microtime(true) < $stopTime) {
                if (!empty($this->config['debug'])) {
                    $this->console->clear();
                    $this->console->out(
                        "<bold><red>DEBUG MODE ENABLED:</red></bold>\n".
                        " - <underline>Turn off debug in production</underline>, which will stop this output to the console.\n".
                        " - Initial Memory usage: ".$pid->script_memory_usage().".\n".
                        " - Sleep time: $sleep_time second between iterations.\n".
                        " - Stop Time: ".@date_create('@'.(int) $stopTime)->format('H:i:s')
                    );
                }

                // time the iteration as not to be running when next cron runs ...
                $loopStart = microtime(true);

                if (!empty($this->config['debug'])) {
                    $this->console->border();
                    $this->console->out(" <bold><green># Start Iteration $i</green></bold>\n");
                }

                // execute task
                $this->class = __NAMESPACE__.'\\Task\\'.$class;
                $this->task = new $this->class($this);

                $this->task->execute();

                if (!empty($this->config['debug'])) {
                    $this->console->out(
                        " - Finished Iteration.\n".
                        " - Took: ".number_format((microtime(true) - $loopStart), 3)." seconds.\n".
                        " - Sleeping for ".((int) $sleep_time)." seconds.\n".
                        " - Stops in: ".number_format(($stopTime-(microtime(true)+(microtime(true) - $loopStart))), 3)." seconds.\n".
                        " - Total running time ".number_format((microtime(true) - $this->constructBootTime), 3)." seconds."
                    );
                }
                
                //
                sleep((int) $sleep_time);

                // break if next task will overrun minute
                if ((microtime(true)+(microtime(true) - $loopStart)) >= $stopTime) {
                    if (!empty($this->config['debug'])) {
                        $this->console->out(
                            " - Daemon finished."
                        );
                    }
                    break;
                }

                //
                if (!empty($this->config['debug'])) {
                    $i++;
                }
            }
        }
    }
}
