<?php
namespace App\src\Controllers;
use App\Queue\Queueable;
use App\src\Queue\Queue;
use App\src\Service\TelegramApi;
use App\src\View\View;

class QueueManagerCommand extends Command
{
    protected Queue $queue;
    public function __construct(View $view, TelegramApi $api, Queue $queue)
    {
        parent::__construct($view, $api);
        $this->queue = $queue;
    }
    public function run(array $options = []): void
    {
        while (true) {
            
            $message = $this->queue->getMessage();

            if ($message) {
                $data = unserialize($message);
                $class = new EventSender($data['reveiver'], $data['message']);
                $class->handle();
                $this->queue->ackLastMessage();
            }

            sleep(10);
        }
    }
}