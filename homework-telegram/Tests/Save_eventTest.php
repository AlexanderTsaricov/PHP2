<?php

use App\src\Controllers\Save_event;
use App\src\Service\TelegramApi;
use App\src\Storage\Database;
use App\src\View\View;

require_once 'vendor/autoload.php';

class Save_eventTest extends PHPUnit\Framework\TestCase {
    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function testRunWithValidData() {
        $testOptions = [
            'name' => 'testname',
            'receiver' => 12345,
            'text' => 'testtext',
            'cron' => '* * * * *'
        ];

        $mockDb = \Mockery::mock(Database::class);
        $mockView = \Mockery::mock(View::class);
        $mockApi = \Mockery::mock(TelegramApi::class);


        $pdo = new \PDO('sqlite::memory:');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->exec("
            CREATE TABLE events (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT,
                receiver INTEGER,
                text TEXT,
                cron TEXT
            )
        ");

        $mockView->shouldReceive('send')
        ->once()
        ->with("started command: save_event");

        $mockDb->shouldReceive('connect')
        ->once()
        ->with()
        ->andReturn($pdo);

        $mockView->shouldReceive('send')
        ->once()
        ->with("command sussful");


        $save_event = new Save_event($mockView, $mockApi, $mockDb);
        $save_event->run($testOptions);

        $rows = $pdo->query("SELECT name, receiver, text, cron FROM events")->fetchAll(PDO::FETCH_ASSOC);
        $this->assertSame([
            [
                'name' => $testOptions['name'],
                'receiver' => $testOptions['receiver'],
                'text' => $testOptions['text'],
                'cron' => $testOptions['cron'],
            ]
        ], $rows);

    }

    public function testRunWithInvalidNameParameter() {
        $testOptions = [
            'name' => null,
            'receiver' => 12345,
            'text' => 'testtext',
            'cron' => '* * * * *'
        ];

        $mockDb = \Mockery::mock(Database::class);
        $mockView = \Mockery::mock(View::class);
        $mockApi = \Mockery::mock(TelegramApi::class);

        $mockView->shouldReceive('send')
        ->once()
        ->with("Error name parameter: don't have parametr name. Please add parametr", true);


        $save_event = new Save_event($mockView, $mockApi, $mockDb);
        $save_event->run($testOptions);

        $this->assertTrue(true);
    }

    public function testRunWithInvalidReceiverParameterNull() {
        $testOptions = [
            'name' => 'testname',
            'receiver' => null,
            'text' => 'testtext',
            'cron' => '* * * * *'
        ];

        $mockDb = \Mockery::mock(Database::class);
        $mockView = \Mockery::mock(View::class);
        $mockApi = \Mockery::mock(TelegramApi::class);

        $mockView->shouldReceive('send')
        ->once()
        ->with("Error receiver parameter: don't have parametr receiver. Please add parametr", true);


        $save_event = new Save_event($mockView, $mockApi, $mockDb);
        $save_event->run($testOptions);

        $this->assertTrue(true);
    }

    public function testRunWithInvalidReceiverParameterString() {
        $testOptions = [
            'name' => 'testname',
            'receiver' => 'gfgfgg',
            'text' => 'testtext',
            'cron' => '* * * * *'
        ];

        $mockDb = \Mockery::mock(Database::class);
        $mockView = \Mockery::mock(View::class);
        $mockApi = \Mockery::mock(TelegramApi::class);

        $mockView->shouldReceive('send')
        ->once()
        ->with("Error receiver parameter: need by are number", true);


        $save_event = new Save_event($mockView, $mockApi, $mockDb);
        $save_event->run($testOptions);

        $this->assertTrue(true);
    }

    public function testRunWithInvalidTextParameter() {
        $testOptions = [
            'name' => 'testname',
            'receiver' => 12345,
            'text' => null,
            'cron' => '* * * * *'
        ];

        $mockDb = \Mockery::mock(Database::class);
        $mockView = \Mockery::mock(View::class);
        $mockApi = \Mockery::mock(TelegramApi::class);

        $mockView->shouldReceive('send')
        ->once()
        ->with("Error text parameter: don't have parametr text. Please add parametr", true);


        $save_event = new Save_event($mockView, $mockApi, $mockDb);
        $save_event->run($testOptions);

        $this->assertTrue(true);
    }

    public function testRunWithInvalidCronParameter() {
        $testOptions = [
            'name' => 'testname',
            'receiver' => 12345,
            'text' => 'test text',
            'cron' => null
        ];

        $mockDb = \Mockery::mock(Database::class);
        $mockView = \Mockery::mock(View::class);
        $mockApi = \Mockery::mock(TelegramApi::class);

        $mockView->shouldReceive('send')
        ->once()
        ->with("Error cron parameter: don't have parametr cron. Please add parametr", true);


        $save_event = new Save_event($mockView, $mockApi, $mockDb);
        $save_event->run($testOptions);

        $this->assertTrue(true);
    }
}