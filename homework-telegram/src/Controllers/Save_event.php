<?php

namespace App\src\Controllers;
use App\src\Controllers\Command;
use App\src\Model\Event;
use App\src\Service\TelegramApi;
use App\src\Storage\Database;
use App\src\View\View;

class Save_event extends Command {
    protected $name = "save_event";
    protected $description = "saves event to storage";

    protected $db;

    public function __construct(View $view, TelegramApi $api, Database $db) {
        parent::__construct($view, $api);
        $this->db = $db;
    }

    public function run (array $options = []) {
        $name = $options["name"] ? $options["name"] : null;
        $receiver = $options["receiver"] ? $options["receiver"] : null;
        $text = $options["text"] ? $options["text"] : null;
        $cron = $options["cron"] ? $options["cron"] : null;

        if ($name === null) {
            $this->view->send("Error name parameter: don't have parametr name. Please add parametr", true);
        } else if ($receiver === null) {
            $this->view->send("Error receiver parameter: don't have parametr receiver. Please add parametr", true);
        } else if (filter_var($receiver, FILTER_VALIDATE_INT) == false) {
            $this->view->send("Error receiver parameter: need by are number", true);
        } else if ($text === null) {
            $this->view->send("Error text parameter: don't have parametr text. Please add parametr", true);
        } else if ($cron === null) {
            $this->view->send("Error cron parameter: don't have parametr cron. Please add parametr", true);
        } else {
            $this->view->send("started command: " . $this->name);

            $connection = $this->db->connect();
            $stmt = $connection->prepare("INSERT INTO events (name, receiver, text, cron) VALUES (:name, :receiver, :text, :cron)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':receiver', $receiver);
            $stmt->bindParam(':text', $text);
            $stmt->bindParam(':cron', $cron);
            $stmt->execute();
            $this->view->send("command sussful");
        }        
    }
}