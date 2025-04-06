<?php

namespace src\Controllers;
use src\Model\Event;
use src\Storage\Database;
use PDO;

class Get_events extends Command {
    protected $name = "get_events";
    protected $description = "Show all events in database";
    
    public function run(array $options = []) {
        $db = new Database();
        $connection = $db::connect();
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

        echo "События в базе:\n";

        foreach ($eventObjects as $event) {
            echo $event->getId() . ": " . $event->getName() . ' (CRON: ' . $event->getCron() . ")\n";
        }
    }
}