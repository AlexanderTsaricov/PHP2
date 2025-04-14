<?php

namespace App\src\Controllers\Tg_controllers;

use App\src\Controllers\Command;
use App\src\Controllers\Handlers\EventHandler;
use App\src\Model\Event;
use App\src\Storage\Database;
use PDO;
use App\src\Logs\Logs;

class TimeSenderEvents extends Command {
    private Database $db;
    private $connection;
    private $running = true;
    private Logs $logger;
    private $lastSendEvent;
    private $childPid;

    public function __construct() {
        parent::__construct();
        $this->db = new Database();
        $this->connection = $this->db::connect();
        $this->logger = new Logs();
        $this->lastSendEvent = "";
    }

    public function run(array $options = []) {
        $pid = pcntl_fork();

        if ($pid == -1) {
            die("Ошибка при создании процесса\n");
        } elseif ($pid) {
            $this->childPid = $pid;
            echo "Родительский процесс продолжает работать, PID дочернего: $pid\n";
            $this->logger->write("Родительский процесс продолжает работать, PID дочернего: $pid");
            while ($this->running) {
                sleep(1);
            }
        } else {
            posix_setsid();
            file_put_contents(__DIR__ . '/daemon.pid', getmypid());

            while ($this->running) {
                pcntl_signal_dispatch();
                $events = EventHandler::handleEvent();

                foreach ($events as $event) {
                    if ($this->lastSendEvent !== $event['id']) {
                        $this->logger->write(
                            date('d.m.y H:i') . " Я отправил сообщение " . '"' .$event['text'] . '"' . " получателю с id " . $event['receiver']
                        );
                        $this->lastSendEvent = $event['receiver'];
                        $this->telegramApi->sendMessage(
                            "Event: " . $event['name'] . ", discription: " . $event['text'],
                            $event['receiver']
                        );
                    }
                }
                sleep(60);
            }

            exit(0);
        }
    }

    public function stopDaemon() {
        $this->running = false;
        $this->logger->write("Завершение работы дочернего процесса с PID: " . $this->childPid);
        if (!empty($this->childPid)) {
            posix_kill($this->childPid, SIGTERM);
            $this->logger->write("Остановлен фоновый процесс с PID: {$this->childPid}");
        }
    }
}
