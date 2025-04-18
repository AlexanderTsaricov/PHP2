<?php

namespace App\src\Controllers;
use App\src\Model\Event;
use App\src\Service\TelegramApi;
use App\src\Storage\Database;
use App\src\View\View;
use PDO;

class Get_events extends Command {
    protected $name = "get_events";
    protected $description = "Show all events in database";

    protected Database $db;

    public function __construct(View $view, TelegramApi $telegramApi, Database $db) {
        parent::__construct($view, $telegramApi);
        $this->db = $db;
    }
    
    public function run(array $options = []) {
        $connection = $this->db::connect();
        $stmt = $connection->query("SELECT * FROM events");
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        
        $eventObjects = [];
        foreach ($events as $eventData) {
            $eventObjects[] = new Event(
                $eventData['id'],
                $eventData['name'],
                (int)$eventData['receiver'],
                $eventData['text'],
                $eventData['cron']
            );
        }

        $this->view->send("События в базе:\n");

        foreach ($eventObjects as $event) {
            $message = $event->getId() . ": " . $event->getName() . ' (CRON: ' . $event->getCron() . ")";
            $this->view->send($message);
        }
    }
}