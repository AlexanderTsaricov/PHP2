<?php
namespace App\src\Controllers;
use App\Queue\Queueable;
use App\src\Queue\Queue;
use App\src\Service\TelegramApi;
use App\src\View\View;

class QueueManagerCommand extends Command
{
    protected Queue $queue;

    private bool $oneTime;
    public function __construct(View $view, TelegramApi $api, Queue $queue, bool $oneTime = false)
    {
        parent::__construct($view, $api);
        $this->queue = $queue;
        $this->oneTime = $oneTime;
    }
    public function run(array $options = []): void
    {
        $flag = true;
        while ($flag) {
            
            $message = $this->queue->getMessage();

            if ($message) {
                $data = unserialize($message);
                $class = new EventSender($this->queue, $this->telegramApi);
                $class->setMessage($data['message']);
                $class->setReceiver($data['receiver']);
                $class->handle();
                $this->queue->ackLastMessage();
            }
            if ($this->oneTime) {
                $flag = false;
            } else {
                sleep(10);
            }
        }
    }
}