<?php
require_once 'vendor/autoload.php';
use App\src\Service\TelegramApi;
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load(); 

$api = new TelegramApi();

$test_1 = $api->getMessages()['ok'];
$test_2 = $api->sendMessage('testMessage', '389495379')[0];

if ($test_1 == true) {
    echo "\nTest 1: pass\n";
} else {
    echo "\nTest 1: fail\n";
}

if ($test_2 == true) {
    echo "\nTest 2: pass\n";
} else {
    echo "\nTest 2: fail\n";
}