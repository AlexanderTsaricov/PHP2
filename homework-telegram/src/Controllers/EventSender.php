<?php

namespace App\src\Controllers;

use App\src\Service\TelegramApi;

class EventSender extends Command
{
    protected TelegramApi $telegramApi;
    protected $name = "event_sender";
    protected $description = "send event to user";

    public function __construct() {
        parent::__construct();
        $this->telegramApi = new TelegramApi();
        
    }
    public function run(array $options = [])
    {
        $sendIsOk = $this->telegramApi->sendMessage($options['message'], (int)$options['receiver']);
        if (!$sendIsOk[0]) {
            die('Error: '. $sendIsOk[1]);
        }
        $message = date('d.m.y H:i') . " Я отправил сообщение " . $options['message'] . " получателю с id " . $options['receiver'];
        $this->view->send($message);
    }
}