<?php

namespace App\src\Controllers;

use App\src\Service\TelegramApi;
use App\src\View\ConsoleView;

class Gt_messages extends Command {

    private TelegramApi $telegram;
    private ConsoleView $consoleView;
    public function __construct() {
        parent::__construct();

        $this->telegram = new TelegramApi();
        $this->consoleView = new ConsoleView();
    }

    public function run (array $options = []) {
        $response = $this->telegram->getMessages();

        if ($response['ok']) {
            $messages = $response['result'];
            print_r($messages);

            foreach ($messages as $message) {
                $sendToViewMessage = '';
                $sendToViewMessage .= "Message id: " . $message['message']['message_id'] . "\n";
                $sendToViewMessage .= "From user: ". $message['message']["from"]["useername"] . " with id: " . $message['message']["from"]["id"] . "\n";
                $sendToViewMessage .= "Text: ". $message['message']["text"];

                $this->consoleView->send($sendToViewMessage);
            }
        }
    }
}