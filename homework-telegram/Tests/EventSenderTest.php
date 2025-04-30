<?php
require_once 'vendor/autoload.php';
use App\src\Service\TelegramApi;
use App\src\Controllers\EventSender;
use App\src\Queue\Queue;



class EventSenderTest extends PHPUnit\Framework\TestCase  {

    protected $dateMock;
    protected function tearDown(): void
    {
        \Mockery::close();
        if ($this->dateMock) {
            $this->dateMock->disable();
        }
        parent::tearDown();
    }
    
    public function testSendMessageAndToQueue() {
        $queueMock = \Mockery::mock(Queue::class);
        $telegramStub = \Mockery::mock(TelegramApi::class);

        $extendMessage = 'message';
        $extendReceiver = '6546544';

        $queueMock->shouldReceive('sendMessage')
        ->with(serialize(['receiver' => $extendReceiver, 'message' => $extendMessage]))
        ->once();

        $sender = new EventSender($queueMock, $telegramStub);
        $sender->sendMessage($extendReceiver, $extendMessage);

        $this->assertTrue(true);
    }

    public function testHandle() {
        $queueStub = \Mockery::mock(Queue::class);
        $telegramMock = \Mockery::mock(TelegramApi::class);

        $extendMessage = 'message';
        $extendReceiver = '6546544';

        $telegramMock->shouldReceive('sendMessage')
        ->with($extendMessage, $extendReceiver)
        ->once();

        $sender = new EventSender($queueStub, $telegramMock);
        $sender->setReceiver($extendReceiver);
        $sender->setMessage($extendMessage);
        $sender->handle();

        $this->assertTrue(true);
    }
}