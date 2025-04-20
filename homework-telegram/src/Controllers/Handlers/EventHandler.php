<?php

namespace App\src\Controllers\Handlers;
use App\src\Storage\Database;
use PDO;
use Cron\CronExpression;

class EventHandler {
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    /**
     * Summary of handleEvent
     * 
     * @return array return enets where event corn parameter corresponds to time in event
     */
    public function handleEvent():array
    {
        $result = [];
        $connection = $this->db->connect();
        $stmt = $connection->query("SELECT * FROM events");
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $thisTime = date("Y-m-d H:i");
        foreach ($events as $event) {
            $expression = new CronExpression($event["cron"]);
            $nextRun = $expression->getNextRunDate();
            $nextRun->modify('-1 minutes'); 
            $eventTime = $nextRun->format('Y-m-d H:i');
            if ($thisTime == $eventTime) {
                $result[] = $event;
            }
        }
        return $result;
    }
}