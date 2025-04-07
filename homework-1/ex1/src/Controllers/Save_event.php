<?php

namespace App\src\Controllers;
use App\src\Controllers\Command;
use App\src\Model\Event;
use App\src\Storage\Database;

class Save_event extends Command {
    protected $name = "save_event";
    protected $description = "saves event to storage";

    public function run_test (array $options) {
        echo "started command: " . $this->name . "\n";
        $event = new Event(null, $options['name'], $options['receiver'], $options['text'], $options['cron']);
        print_r($event->get());
    }

    public function run (array $options = []) {
        $name = $options["name"] ? $options["name"] : null;
        $receiver = $options["receiver"] ? $options["receiver"] : null;
        $text = $options["text"] ? $options["text"] : null;
        $cron = $options["cron"] ? $options["cron"] : null;

        if ($name === null) {
            $name = 'Noname event';
        }
        
        if ($receiver === null) {
            $this->view->send("Error receiver parametr: don't have parametr receiver. Please add parametr", true);
            exit();
        } else if (filter_var($receiver, FILTER_VALIDATE_INT) == false) {
            $this->view->send("Error receiver parametr: need by are number", true);
            exit();
        }
        
        if ($text === null) {
            $text = "Text message";
        }
        
        if ($cron === null) {
            $cron = "* * 0 * *";
        }

        $this->view->send("started command: " . $this->name);

        $connection = Database::connect();
        $stmt = $connection->prepare("INSERT INTO events (name, receiver, text, cron) VALUES (:name, :receiver, :text, :cron)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':receiver', $receiver);
        $stmt->bindParam(':text', $text);
        $stmt->bindParam(':cron', $cron);
        $stmt->execute();
        $this->view->send("command sussful");
    }
}