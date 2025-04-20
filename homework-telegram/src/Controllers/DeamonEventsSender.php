<?php
namespace App\src\Controllers;
use App\src\Controllers\Handlers\EventHandler;
use App\src\Logs\Logs;
use App\src\Service\TelegramApi;
use App\src\View\View;

class DeamonEventsSender extends Command {
    protected $running = true;

    protected Logs $logger;

    protected EventHandler $eventHandler;

    protected $lastSendEvent;

    public function __construct(View $view, TelegramApi $api, EventHandler $eventHandler, Logs $logger) {
        parent::__construct($view, $api);
        $this->logger = $logger;
        $this->lastSendEvent = "";
        $this->eventHandler = $eventHandler;
    }

    protected function setupSignalHandlers() {
        pcntl_signal(SIGTERM, [$this, 'stopDaemon']);
        pcntl_signal(SIGINT,  [$this, 'stopDaemon']);
        pcntl_signal(SIGHUP,  [$this, 'stopDaemon']);
    }

    public function run($options = []) {
        $this->logger->write("Worker started with PID: " . getmypid());
    
        
        $this->setupSignalHandlers();
    
        while ($this->running) {
            pcntl_signal_dispatch();
    
            $events = $this->eventHandler->handleEvent();
            foreach ($events as $event) {
                if ($this->lastSendEvent != $event['receiver']) {
                    $this->logger->write(
                        date('d.m.y H:i') . " Я отправил сообщение " . $event['text'] . " получателю с id " . $event['receiver']
                    );
                    $this->lastSendEvent=$event['receiver'];
                }
            }
    
            sleep($options['sleep']);
        }
    
        $this->logger->write("Worker shutting down");
    }
    

    public function stopDaemon() {
        $this->running = false;
    }
}
