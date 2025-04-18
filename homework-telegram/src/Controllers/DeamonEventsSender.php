<?php
namespace App\src\Controllers;
use App\src\Controllers\Handlers\EventHandler;
use App\src\Logs\Logs;

class DeamonEventsSender extends Command {
    private $running = true;

    private Logs $logger;

    private $lastSendEvent;

    public function __construct() {
        parent::__construct();
        $this->logger = new Logs();
        $this->lastSendEvent = "";
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
                if ($this->lastSendEvent != $event['receiver']) {
                    $this->logger->write(
                        date('d.m.y H:i') . " Я отправил сообщение " . $event['text'] . " получателю с id " . $event['receiver']
                    );
                    $this->lastSendEvent=$event['receiver'];
                }
            }
    
            sleep(1);
        }
    
        $this->logger->write("Worker shutting down");
    }
    

    public function stopDaemon() {
        $this->running = false;
    }
}
