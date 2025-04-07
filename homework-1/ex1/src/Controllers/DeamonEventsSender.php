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

    public function run($options = []) {
        $this->logger->write("Worker started with PID: " . getmypid());
    
        
        pcntl_signal(SIGTERM, [$this, 'stopDaemon']);
        pcntl_signal(SIGINT, [$this, 'stopDaemon']);
        pcntl_signal(SIGHUP, [$this, 'stopDaemon']);
    
        while ($this->running) {
            pcntl_signal_dispatch();
    
            $events = EventHandler::handleEvent();
            foreach ($events as $event) {
                $this->logger->write(
                    date('d.m.y H:i') . " Я отправил сообщение " . $event['text'] . " получателю с id " . $event['receiver']
                );
            }
    
            sleep(60);
        }
    
        $this->logger->write("Worker shutting down");
    }
    

    public function stopDaemon() {
        $this->running = false;
    }
}
