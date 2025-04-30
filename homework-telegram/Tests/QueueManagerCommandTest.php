<?php

use App\src\Controllers\QueueManagerCommand;
use App\src\Queue\Queue;
use App\src\Service\TelegramApi;
use App\src\View\View;

require_once 'vendor/autoload.php';

class QueueManagerCommandTest extends PHPUnit\Framework\TestCase {
    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function testRun() {
        $viewStub = \Mockery::mock(View::class);
        $apiMock = \Mockery::mock(TelegramApi::class);
        $queueMock = \Mockery::mock(Queue::class);

        $extendMessage = 'test message';
        $extendReceiver = '0';

        $extendData = serialize(['receiver' => $extendReceiver, 'message' => $extendMessage]);

        $queueMock->shouldReceive('getMessage')
        ->once()
        ->andReturn($extendData);

        $apiMock->shouldReceive('sendMessage')
        ->with($extendMessage, $extendReceiver)
        ->once();

        $queueMock->shouldReceive('ackLastMessage')
        ->once();

        $manager = new QueueManagerCommand($viewStub, $apiMock, $queueMock, true);
        $manager->run();

        $this->assertTrue(true);
    }
}