<?php

use App\src\Controllers\Get_events;
use App\src\Storage\Database;
use App\src\View\ConsoleView;
use App\src\Service\TelegramApi;

require_once 'vendor/autoload.php';

class Get_eventsTest extends PHPUnit\Framework\TestCase {
    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function testRunValidData () {
        $dbTestData = [
            [
                'id'=> 1,
                'name'=> 'User1',
                'receiver'=> 12345,
                'text' => 'test 1',
                'cron'=> '* * * * *',
            ],
            [
                'id'=> 2,
                'name'=> 'User2',
                'receiver'=> 23456,
                'text' => 'test 2',
                'cron'=> '* * * * *',
            ],
        ];


        $stubApi = \Mockery::mock(TelegramApi::class);
        $stubDb = \Mockery::mock(Database::class);
        $mockView = \Mockery::mock(ConsoleView::class);
        $pdo = new \PDO('sqlite::memory:');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->exec("
            CREATE TABLE events (
                id INTEGER PRIMARY KEY,
                name TEXT,
                receiver INTEGER,
                text TEXT,
                cron TEXT
            )
        ");
        $stmt = $pdo->prepare("
            INSERT INTO events (id, name, receiver, text, cron) 
            VALUES (:id, :name, :receiver, :text, :cron)
        ");
        foreach ($dbTestData as $row) {
            $stmt->execute($row);
        }
        $stubDb->shouldReceive('connect')->once()->andReturn($pdo);
        
        
        $mockView->shouldReceive('send')->with("События в базе:\n")->ordered();
        $mockView->shouldReceive('send')->with($dbTestData[0]['id'] . ": " . $dbTestData[0]['name'] . ' (CRON: ' . $dbTestData[0]['cron'] . ")")->ordered();
        $mockView->shouldReceive('send')->with($dbTestData[1]['id'] . ": " . $dbTestData[1]['name'] . ' (CRON: ' . $dbTestData[1]['cron'] . ")")->ordered();

        $get_events = new Get_events($mockView, $stubApi, $stubDb);
        $get_events->run();

        $this->assertTrue(true);
    }
}