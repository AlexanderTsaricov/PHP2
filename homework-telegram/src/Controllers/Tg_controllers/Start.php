<?php
namespace App\src\Controllers\Tg_controllers;
use App\src\Controllers\Command;
use App\src\Controllers\Save_event;

class Start extends Command {
    private $flag;

    public function __construct() {
        parent::__construct();
        $this->flag = true;
    }

    public function run(array $options = []) {
        $endmessage = "";
        $this->telegramApi->sendMessage("Please, write name event", $options["user_id"]);
        $newEventName = "";
        $newEventText = "";
        $newEventTime = "";
        while ($this->flag) {
            $messagesResponse = $this->telegramApi->getMessages();
            if ($messagesResponse['ok']) {
                $lastmessage = end($messagesResponse['result'])['message']['text'];
                if ($lastmessage != "/start" && $lastmessage != $endmessage) {
                    $this->telegramApi->sendMessage("Ok, your name event is '" . $lastmessage . "'", $options["user_id"]);
                    $newEventName = $lastmessage;
                    $endmessage = $lastmessage;
                    $this->flag = false;
                }
            }
            sleep(1);
        }
        $this->flag = true;
        $this->telegramApi->sendMessage("Please, write text event", $options["user_id"]);
        while ($this->flag) {
            $messagesResponse = $this->telegramApi->getMessages();
            if ($messagesResponse['ok']) {
                $lastmessage = end($messagesResponse['result'])['message']['text'];
                if ($lastmessage != "/start" && $lastmessage != $endmessage) {
                    $this->telegramApi->sendMessage("Ok, your text event is '" . $lastmessage . "'", $options["user_id"]);
                    $newEventText = $lastmessage;
                    $endmessage = $lastmessage;
                    $this->flag = false;
                }
            }
            sleep(1);
        }
        $this->flag = true;
        $this->telegramApi->sendMessage("Please, write time event in cron format (* * * * *)", $options["user_id"]);
        $pattern = '/^(\*|[0-9]+|\*\/[0-9]+|[0-9]+-[0-9]+|[0-9]+(,[0-9]+)*){1}( (\*|[0-9]+|\*\/[0-9]+|[0-9]+-[0-9]+|[0-9]+(,[0-9]+)*)){4}$/';
        while ($this->flag) {
            $messagesResponse = $this->telegramApi->getMessages();
            if ($messagesResponse['ok']) {
                $lastmessage = end($messagesResponse['result'])['message']['text'];
                if ($lastmessage != "/start" && $lastmessage != $endmessage) {
                    $this->telegramApi->sendMessage("Ok, your crone time event is '" . $lastmessage . "'", $options["user_id"]);
                    if (preg_match($pattern, $lastmessage)) {
                        $newEventTime = $lastmessage;
                        $endmessage = $lastmessage;
                        $this->flag = false;
                    } else {
                        $endmessage = $lastmessage;
                        $this->telegramApi->sendMessage("Invalid time format", $options["user_id"]);
                        $this->telegramApi->sendMessage("Please, write time event in cron format (* * * * *)", $options["user_id"]);
                    }
                    
                }
            }
            sleep(1);
        }
        $this->telegramApi->sendMessage("Enter event sussful. New event - '$newEventName', text - '$newEventText' evrytime in '$newEventTime'", $options["user_id"]);
        $save_event = new Save_event();
        $save_event->run(["name" => $newEventName, "receiver" => $options["user_id"], "text" => $newEventText, "cron" => $newEventTime]);
    }
}