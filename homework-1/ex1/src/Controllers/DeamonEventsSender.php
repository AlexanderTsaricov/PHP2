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
                $this->logger->write("Daemon loop running...");
                $events = EventHandler::handleEvent();
                $sender = new EventSender();
                foreach ($events as $event) {
                    $eventParametrs = [];
                    print_r($event);
                    $eventParametrs["message"] = $event['text'];
                    $eventParametrs["receiver"] = $event['receiver'];
                    $sender->run($eventParametrs);
                }
                sleep(1);
            }
        }
    }

    public function stopDaemon() {
        $this->running = false;
    }
}
