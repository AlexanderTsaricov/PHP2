<?php

use App\src\Controllers\Handlers\EventHandler;
use App\src\Storage\Database;

require_once 'vendor/autoload.php';

class EventHandlerTest extends PHPUnit\Framework\TestCase {

    public function testhandleEvent() {
        $testEvent = [
            'name'=> 'testname',
            'receiver'=> 12345,
            'text'=> 'testtext',
            'cron'=> '* * * * *',
        ];
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
        $stmt = $pdo->prepare("
        INSERT INTO events (name, receiver, text, cron) 
        VALUES (:name, :receiver, :text, :cron)
        ");

        $stmt->bindParam(':name', $testEvent['name']);
        $stmt->bindParam(':receiver', $testEvent['receiver']);
        $stmt->bindParam(':text', $testEvent['text']);
        $stmt->bindParam(':cron', $testEvent['cron']);

        $stmt->execute();

        $mockDb = \Mockery::mock(Database::class);
        $mockDb->shouldReceive('connect')
        ->once()
        ->andReturn($pdo);

        $eventHandler = new EventHandler($mockDb);
        $result = $eventHandler->handleEvent();

        // Убираем id из возвращаемых данных для сравнения
        foreach ($result as &$row) {
            unset($row['id']);
        }

        // Приводим receiver к числу, если это нужно для сравнения
        foreach ($result as &$row) {
            $row['receiver'] = (int) $row['receiver'];  // Приводим к числу
        }

        $this->assertEquals($testEvent, $result[0]);
    }
}