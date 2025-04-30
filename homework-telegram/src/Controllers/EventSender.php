<?php

namespace App\src\Controllers;

use App\src\Controllers\Handlers\EventHandler;
use App\src\Queue\Queue;
use App\src\Queue\Queueable;
use App\src\Service\TelegramApi;
use App\src\View\View;

class EventSender implements Queueable
{
    protected TelegramApi $telegram;
    protected string $receiver;
    protected string $message;

    protected Queue $queue;

    public function __construct(Queue $queue, TelegramApi $telegram) {
        $this->queue = $queue;
        $this->telegram = $telegram;
    }

    public function sendMessage(string $receiver, string $message) {
        $this->toQueue($receiver, $message);
    }

    public function handle(): void {
        $this->telegram->sendMessage($this->message, $this->receiver);
    }

    public function toQueue(...$args): void {
        $this->receiver = $args[0];
        $this->message = $args[1];

        $this->queue->sendMessage(serialize(['receiver' => $this->receiver, 'message' => $this->message]));
    }

    public function setReceiver(string $receiver) {
        $this->receiver = $receiver;
    }

    public function setMessage(string $message) {
        $this->message = $message;
    }
}