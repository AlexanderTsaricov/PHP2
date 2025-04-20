<?php

require_once 'vendor/autoload.php';
use App\src\Controllers\Tg_controllers\GetEvents;
use PHPUnit\Framework\Attributes\TestWith;
use App\src\View\View;
use App\src\Service\TelegramApi;
use App\src\Storage\Database;
class GetEventsTest extends PHPUnit\Framework\TestCase {

    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[TestWith([1])]
    #[TestWith([2])]
    #[TestWith([3])]
    public function testRunWithArray(int $id) {
        $dbTestData = [
            [
                'user_id'=> 1,
                'name'=> 'User1',
                'receiver'=> 12345,
                'text' => 'test 1',
                'cron'=> '* * * * *',
            ],
            [
                'user_id'=> 2,
                'name'=> 'User2',
                'receiver'=> 23456,
                'text' => 'test 2',
                'cron'=> '* * * * *',
            ],
            [
                'user_id'=> 3,
                'name'=> 'User3',
                'receiver'=> 34567,
                'text' => 'test 3',
                'cron'=> '* * * * *',
            ],
        ];

        $mockApi = \Mockery::mock(TelegramApi::class);
        $mockDb = \Mockery::mock(Database::class);
        $mockView = \Mockery::mock(View::class);
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
            VALUES (:user_id, :name, :receiver, :text, :cron)
        ");

        foreach ($dbTestData as $row) {
            $stmt->execute($row);
        }

        $message = $id . ": " . $dbTestData[$id-1]['name'] . ' (CRON: ' . $dbTestData[$id-1]['cron'] . ")\n";


        $mockDb->shouldReceive("connect")->once()->andReturn($pdo);
        $mockView->shouldReceive("send")->once()->with("События в базе:\n");
        $mockView->shouldReceive("send")->once()->with($message);
        $mockApi->shouldReceive("sendMessage")->once()->with($message, $dbTestData[$id-1]['receiver']);


        $getEvents = new GetEvents($mockView, $mockDb, $mockApi);
        $getEvents->run(["user_id" => $dbTestData[$id-1]['receiver']]);

        $this->assertTrue(true);
    }
}