<?php

namespace App\src\Controllers;
class EventSender extends Command
{
    protected $name = "event_sender";
    protected $description = "send event to user";
    public function run(array $options = [])
    {
        $message = date('d.m.y H:i') . " Я отправил сообщение " . $options['message'] . " получателю с id " . $options['receiver'];
        $this->view->send($message);
    }
}