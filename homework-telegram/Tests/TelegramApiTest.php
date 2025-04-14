<?php
require_once 'vendor/autoload.php';
use App\src\Service\TelegramApi;
use Dotenv\Dotenv;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

class TelegramApiTest extends PHPUnit\Framework\TestCase {
    public function setUp(): void {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load(); 
    }

    public function testGetMessages () {
        $api = new TelegramApi();
        $messages = $api->getMessages();
        $this->assertTrue($messages['ok']);
    }

    public function testSendMessage () {
        $api = new TelegramApi();
        $resultSend = $api->sendMessage('testMessage', '389495379')['ok'];
        $this->assertTrue($resultSend);
    }

    public function testInvalidValueSendMessage () {
        $api = new TelegramApi();
        $resultSend = $api->sendMessage('testMessage', '');
        $this->assertFalse($resultSend['ok']);
    }
}