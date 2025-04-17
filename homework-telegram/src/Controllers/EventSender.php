<?php

namespace App\src\Controllers;

use App\src\Service\TelegramApi;
use App\src\View\View;

class EventSender extends Command
{
    protected TelegramApi $telegramApi;
    protected $name = "event_sender";
    protected $description = "send event to user";

    public function __construct(TelegramApi $api, View $view) {
        parent::__construct();
        $this->telegramApi = $api;
        $this->view = $view;
        
    }
    public function run(array $options = [])
    {
        $sendIsOk = $this->telegramApi->sendMessage($options['message'], (int)$options['receiver']);
        if (!$sendIsOk['ok']) {
            $this->view->send('Error: '. $sendIsOk['error_code']);
        } else {
            $message = date('d.m.y H:i') . " Я отправил сообщение " . $options['message'] . " получателю с id " . $options['receiver'];
            $this->view->send($message);
        }
        
    }
}