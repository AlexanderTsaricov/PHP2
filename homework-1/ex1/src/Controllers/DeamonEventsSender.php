<?php
namespace App\src\Controllers;
use App\src\Controllers\Handlers\EventHandler;
use App\src\Logs\Logs;

class DeamonEventsSender extends Command {
    private $running = true;

    private Logs $logger;

    public function __construct() {
        parent::__construct();
        $this->logger = new Logs();
    }

    public function run ($options = []) {
        $pid = pcntl_fork();
        if ($pid == -1) {
            die("Could not fork");
        } elseif ($pid) {
            $this->view->send("Parent process exiting");
            exit();
        } else {
            if (posix_setsid() == -1) {
                die("Could not set session id");
            }
            echo "Child PID: " . getmypid() . "\n";
            $this->view->send("Daemon started");

            // Выполняем код демона
            chdir("/");

            fclose(STDOUT);
            fclose(STDERR);
            fclose(STDIN);

            pcntl_signal(SIGTERM, [$this, 'stopDaemon']);
            pcntl_signal(SIGINT, [$this, 'stopDaemon']);
            pcntl_signal(SIGHUP, [$this, 'stopDaemon']);

            while ($this->running) {
                pcntl_signal_dispatch();
                $events = EventHandler::handleEvent();
                foreach ($events as $event) {
                    
                    $this->logger->write(date('d.m.y H:i') . " Я отправил сообщение " . $event['text'] . " получателю с id " . $event['receiver']);
                }
                sleep(1);
            }
        }
    }

    public function stopDaemon() {
        $this->running = false;
    }
}
