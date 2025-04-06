<?php

namespace src\Model;

class Event {
    private $id;
    private $name = null;
    private $receiver = null;
    private $text = null;
    private $cron = null;

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getCron() {
        return $this->cron;
    }

    public function __construct($id = null , string $name, int $receiver,  string $text, string $cron) {
        $this->name = $name;
        $this->receiver = $receiver;
        $this->text = $text;
        $this->cron = $cron;
        $this->id = $id;
    }

    /**
     * Summary of get
     * @return array with events parametrs
     */
    public function get(): array {
        $result = [];

        $result["name"] = $this->name;
        $result["receiver"] = $this->receiver;
        $result["text"] = $this->text;
        $result["cron"] = $this->cron;

        return $result;
    }

}