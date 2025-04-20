<?php

use App\src\Controllers\DeamonEventsSender;
use App\src\Controllers\Handlers\EventHandler;
use App\src\Logs\Logs;
use App\src\Service\TelegramApi;
use App\src\View\View;
use App\Tests\TestChildObjects\TestableDaemon;

require_once 'vendor/autoload.php';

class DeamonEventSenderTest extends PHPUnit\Framework\TestCase {
    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function testRun() {
        $testEvents = [
            [
                'id'=> 1,
                'name'=> 'testName1',
                'text'=> 'test text 1',
                'receiver'=> 12345,
                'cron'=> '* * * * *',
            ],
            [
                'id'=> 2,
                'name'=> 'testName2',
                'text'=> 'test text 2',
                'receiver'=> 67891,
                'cron'=> '* * * * *',
            ],
        ];

        $stubView = \Mockery::mock(View::class);
        $mockApi = \Mockery::mock(TelegramApi::class);
        $mockLogger = \Mockery::mock(Logs::class);
        $mockEventHandler = \Mockery::mock(EventHandler::class);

        $mockLogger->shouldReceive("write")->once()->with("Worker started with PID: " . getmypid());
        $mockLogger->shouldReceive('write')->once()->with(
            date('d.m.y H:i') . " Я отправил сообщение " . $testEvents[0]['text'] . " получателю с id " . $testEvents[0]['receiver']
        );
        $mockLogger->shouldReceive('write')->once()->with(
            date('d.m.y H:i') . " Я отправил сообщение " . $testEvents[1]['text'] . " получателю с id " . $testEvents[1]['receiver']
        );
        $mockLogger->shouldReceive('write')->once()->with("Worker shutting down");

        $daemon = new TestableDaemon($stubView, $mockApi, $mockEventHandler, $mockLogger);
        $mockEventHandler->shouldReceive('handleEvent')->once()->andReturnUsing(function () use ($daemon, $testEvents) {
            // останавливаем после первой итерации
            $reflection = new ReflectionClass($daemon);
            $prop = $reflection->getProperty('running');
            $prop->setAccessible(true);
            $prop->setValue($daemon, false);
        
            return $testEvents;
        });

        $daemon->run(['sleep' => 0]);
        $this->assertTrue(true);

    }
}