<?php
namespace App\src\Controllers\Tg_controllers;
use App\src\Controllers\Command;
use App\src\Controllers\Save_event;
use App\src\Service\TelegramApi;
use App\src\Storage\Database;
use App\src\View\View;

class Start extends Command {
    private $flag;

    private Save_event $save_event;

    public function __construct(View $view, TelegramApi $api, Save_event $save_event) {
        parent::__construct($view, $api);
        $this->flag = true;
        $this->save_event = $save_event;
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
            sleep($options['sleep']);
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
            sleep($options['sleep']);
        }
        $this->flag = true;
        $this->telegramApi->sendMessage("Please, write time event in cron format (* * * * *)", $options["user_id"]);
        $pattern = '/^(\*|[0-9]+|\*\/[0-9]+|[0-9]+-[0-9]+|([0-9]+,)*[0-9]+)' . '( (\*|[0-9]+|\*\/[0-9]+|[0-9]+-[0-9]+|([0-9]+,)*[0-9]+)){4}$/';
        while ($this->flag) {
            $messagesResponse = $this->telegramApi->getMessages();
            if ($messagesResponse['ok']) {
                $lastmessage = end($messagesResponse['result'])['message']['text'];
                if ($lastmessage != "/start" && $lastmessage != $endmessage) {
                    if (preg_match($pattern, $lastmessage)) {
                        $this->telegramApi->sendMessage("Ok, your crone time event is '" . $lastmessage . "'", $options["user_id"]);
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
            sleep($options['sleep']);
        }
        $this->telegramApi->sendMessage("Enter event sussful. New event - '$newEventName', text - '$newEventText' evrytime in '$newEventTime'", $options["user_id"]);
        $this->save_event->run(["name" => $newEventName, "receiver" => $options["user_id"], "text" => $newEventText, "cron" => $newEventTime]);
    }
}