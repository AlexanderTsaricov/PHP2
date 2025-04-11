<?php

require __DIR__ . '/vendor/autoload.php'; // ✅ самый прямой путь
use App\src\Controllers\Gt_messages;
use App\src\Controllers\Tg_controllers\GetEvents;
use App\src\Controllers\Tg_controllers\Start;
use App\src\Controllers\Tg_controllers\TimeSenderEvents;
use App\src\Logs\Logs;
use App\src\Service\TelegramApi;
use App\src\View\ConsoleView;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load(); 
use App\src\Storage\Database;

$timeEventSender = new TimeSenderEvents();
$timeEventSender->run();

$logger = new Logs();

$running = true;


while ($running) {
    $logger->write("Worker started with PID: " . getmypid());
    
    pcntl_signal(SIGTERM, function() use (&$running) {
        $running = false;
    });
    pcntl_signal(SIGINT, function() use (&$running) {
        $running = false;
    });
    pcntl_signal(SIGHUP, function() use (&$running) {
        $running = false;
    });

    $view = new ConsoleView();
    $telegram = new TelegramApi();
    $messagesResponse = $telegram->getMessages();

    $db = new Database();
    $connection = $db::connect();

    $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS events (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            receiver INTEGER,
            text TEXT,
            cron TEXT
        )
    SQL;
    $connection->exec($sql);

    if ($messagesResponse['ok']) {
        $messages = $messagesResponse['result'];
        try {
            $lastidMessageJSON = file_get_contents(__DIR__ . "/src/Database/lastidmessage.json");
            $lastIdMessage = json_decode($lastidMessageJSON);
        } catch (Exception $e) {
            try {
                file_put_contents(__DIR__ . "/src/Database/lastidmessage.json", '{"last": null}');
                $lastIdMessage = null;
            } catch (Exception $e) {
                $view->send("Error: " . $e->getMessage(), true);
                $lastIdMessage = null;
            }
        }
        $comanndMessage = getLastMatching($messages, function ($message)  {
            if ($message['message']['text']['0'] == "/") {
                return true;
            } else {
                return false;
            }
        });

        if ($comanndMessage != null) {
            if ((int)$comanndMessage['id'] != (int)$lastIdMessage->last) {
                file_put_contents(__DIR__ . "/src/Database/lastidmessage.json", '{"last": ' . $comanndMessage['id'] . "}");
                switch (substr($comanndMessage['message'], 1)) {
                    case "start":
                        $start = new Start();
                        $start->run($comanndMessage);
                        break;
                    case "get":
                        $getEvents = new GetEvents();
                        $getEvents->run($comanndMessage);
                        break;
                    default:
                        die("Error command - invalid command: " . $comanndMessage['message'] . "\n");
                }
            }
        }

    } else {
        $view->send("Error: " .$messagesResponse['error_code']);
    }



    sleep(1);
}

function getLastMatching(array $array, callable $condition) {
    for ($i = count($array) - 1; $i >= 0; $i--) {
        if ($condition($array[$i])) {
            return [
                'message' =>$array[$i]["message"]["text"],
                'id' => $array[$i]['message']['message_id'],
                'user_id' => $array[$i]['message']['from']['id']
            ];
        }
    }
    return null;
}