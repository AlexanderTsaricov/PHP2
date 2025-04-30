<?php

namespace App\src\Queue;
use App\src\Queue\Queue;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Channel\AbstractChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ implements Queue {
    private AMQPMessage |null $lastMessage;
    private AbstractChannel | AMQPChannel $channel;
    private AMQPStreamConnection $connection;

    public function __construct(private string $queueName) {
        $this->lastMessage = null;
    }

    public function sendMessage($message): void {
        $this->open();

        $msg = new AMQPMessage($message, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $this->channel->basic_publish($msg, '', $this->queueName);
        // TODO: переделать var_dump на view
        //var_dump($msg);
        $this->close();
    }

    public function getMessage(): string|null {
        $this->open();

        $msg = $this->channel->basic_get($this->queueName);

        if ($msg) {
            $this->lastMessage = $msg;
            return $msg->getBody();
        }

        $this->close();
        return null;
    }

    public function ackLastMessage(): void {
        $this->lastMessage?->ack();
        $this->close();
    }

    private function open() {
        $this->connection = new AMQPStreamConnection('localhost', 5672, 'quest', 'quest');
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare($this->queueName, false, false, false, true);
    }

    private function close() {
        $this->channel->close();
        $this->connection->close();
    }



}