<?php
namespace App\src\Controllers\Tg_controllers;
use App\src\Controllers\Command;
use App\src\Model\Event;
use App\src\Service\TelegramApi;
use App\src\Storage\Database;
use App\src\View\View;
use PDO;


class GetEvents extends Command {

    private Database $db;

    private $connection;

    public function __construct(View $view, Database $db, TelegramApi $api) {
        parent::__construct($view, $api);
        $this->db = $db;
        $this->connection = $this->db->connect();
    }

    public function run(array $options = []) {
        $message = "";
        $stmt = $this->connection->query("SELECT * FROM events WHERE receiver=" . $options['user_id']);
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
            $message .= $event->getId() . ": " . $event->getName() . ' (CRON: ' . $event->getCron() . ")\n";
        }

        $this->view->send($message);
        $this->telegramApi->sendMessage($message, $options["user_id"]);
    }
        
}