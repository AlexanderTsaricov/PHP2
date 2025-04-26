<?php

namespace App\src\Controllers;

use App\src\Service\TelegramApi;
use App\src\View\ConsoleView;
use App\src\View\View;

class Gt_messages extends Command {
    public function __construct(View $view, TelegramApi $api) {
        parent::__construct($view, $api);
    }

    public function run (array $options = []) {
        try {
            $messages = $this->telegramApi->getMessages();
        } catch (\Exception $e) {
            $this->view->send($e);
        }
        

        foreach ($messages as $message) {
            $sendToViewMessage = '';
            $sendToViewMessage .= "Message id: " . $message['message']['message_id'] . "\n";
            $sendToViewMessage .= "From user: ". $message['message']["from"]["username"] . " with id: " . $message['message']["from"]["id"] . "\n";
            $sendToViewMessage .= "Text: ". $message['message']["text"];

            $this->view->send($sendToViewMessage);
        }
        
    }
}