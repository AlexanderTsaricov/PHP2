<?php
require_once 'vendor/autoload.php';

use App\src\Cache\Redis;
use App\src\Service\TelegramApi;
use phpmock\mockery\PHPMockery;
use PHPUnit\Framework\TestCase;

class TelegramApiTest extends PHPUnit\Framework\TestCase {

    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function testGetMessages () {
        $data = [
            "ok" => true,
            "result" => [
                [
                    "update_id" => 734066032,
                    "message" => [
                        "message_id" => 406,
                        "from" => [
                            "id" => 12345,
                            "is_bot" => false,
                            "first_name" => "Test",
                            "username" => "testuser",
                            "language_code" => "ru"
                        ],
                        "chat" => [
                            "id" => 389495379,
                            "first_name" => "Test",
                            "username" => "testuser",
                            "type" => "private"
                        ],
                        "date" => 1744890830,
                        "text" => "/test",
                        "entities" => [
                            [
                                "offset" => 0,
                                "length" => 5,
                                "type" => "bot_command"
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $redisMock = \Mockery::mock(Redis::class);
        $redisMock->shouldReceive('get')
            ->with('telegramBot:offset', 0)
            ->andReturn(1);
        $redisMock->shouldReceive('get')
            ->with('telegramBot:oldMessages', false)
            ->andReturn(false);
        $redisMock->shouldReceive('set')
            ->with('telegramBot:oldMessages', \Mockery::type('string'))
            ->once();
        $redisMock->shouldReceive('set')
            ->with('telegramBot:offset', 734066032)
            ->once();
        $jsonResponse = json_encode($data);
        $fileGetContentsMock = PHPMockery::mock('App\src\Service', "file_get_contents")
            ->once()
            ->with("https://api.telegram.org/botTEST_TOKEN/getUpdates?offset="."2")
            ->andReturn($jsonResponse);
        $api = new TelegramApi(null, "TEST_TOKEN", $redisMock);
        $result = $api->getMessages();
        $this->assertEquals($data, $result);
    }

    public function testSendMessageWithValidData () {
        $validResponse = [
            "ok" => true,
            "result" => [
                "message_id" => 408,
                "from" => [
                    "id" => 8079517018,
                    "is_bot" => true,
                    "first_name" => "GB-student-bot",
                    "username" => "GBHomeworksBot"
                ],
                "chat" => [
                    "id" => 389495379,
                    "first_name" => "Александр",
                    "username" => "salispiligrim",
                    "type" => "private"
                ],
                "date" => 1744893713,
                "text" => "Hello World"
            ]
        ];
        $invalidResponse = [
            "ok" => false,
            "error_code" => 400,
            "description" => "Bad Request: chat_id is empty"
        ];
        PHPMockery::mock('App\src\Service', "file_get_contents")
            ->zeroOrMoreTimes()
            ->andReturnUsing(function ($url, $use_include_path = false, $context = null) use ($validResponse, $invalidResponse) {
                // Для функций, вызываемых для отправки сообщения, убеждаемся, что URL содержит "sendMessage"
                if (strpos($url, "sendMessage") === false) {
                    // Если это не sendMessage, можно вернуть что угодно, например, пустую строку:
                    return '';
                }
                // Проверяем, что контекст является ресурсом и содержит HTTP-опции
                if (!is_resource($context)) {
                    return '';
                }
                $options = stream_context_get_options($context);
                if (!isset($options['http']['content'])) {
                    return '';
                }
                // Разбираем строку POST-данных в массив
                parse_str($options['http']['content'], $postData);
                
                // Если chat_id пустой — вернуть ответ с ошибкой
                if (isset($postData['chat_id']) && $postData['chat_id'] === '') {
                    return json_encode($invalidResponse);
                }
                
                // Если chat_id заполнен — вернуть успешный ответ
                return json_encode($validResponse);
        });


        $api = new TelegramApi();
        $result = $api->sendMessage("Hello", 12345);

        $this->assertEquals($validResponse, $result);

    }

    public function testSendMessageWithInvalidData () {
        $validResponse = [
            "ok" => true,
            "result" => [
                "message_id" => 408,
                "from" => [
                    "id" => 8079517018,
                    "is_bot" => true,
                    "first_name" => "GB-student-bot",
                    "username" => "GBHomeworksBot"
                ],
                "chat" => [
                    "id" => 389495379,
                    "first_name" => "Александр",
                    "username" => "salispiligrim",
                    "type" => "private"
                ],
                "date" => 1744893713,
                "text" => "Hello World"
            ]
        ];
        $invalidResponse = [
            "ok" => false,
            "error_code" => 400,
            "description" => "Bad Request: chat_id is empty"
        ];
        PHPMockery::mock('App\src\Service', "file_get_contents")
            ->zeroOrMoreTimes()
            ->andReturnUsing(function ($url, $use_include_path = false, $context = null) use ($validResponse, $invalidResponse) {
                // Для функций, вызываемых для отправки сообщения, убеждаемся, что URL содержит "sendMessage"
                if (strpos($url, "sendMessage") === false) {
                    // Если это не sendMessage, можно вернуть что угодно, например, пустую строку:
                    return '';
                }
                // Проверяем, что контекст является ресурсом и содержит HTTP-опции
                if (!is_resource($context)) {
                    return '';
                }
                $options = stream_context_get_options($context);
                if (!isset($options['http']['content'])) {
                    return '';
                }
                // Разбираем строку POST-данных в массив
                parse_str($options['http']['content'], $postData);
                
                // Если chat_id пустой — вернуть ответ с ошибкой
                if (isset($postData['chat_id']) && $postData['chat_id'] === '') {
                    return json_encode($invalidResponse);
                }
                
                // Если chat_id заполнен — вернуть успешный ответ
                return json_encode($validResponse);
        });
        $api = new TelegramApi(null, "TEST_TOKEN");
        $result = $api->sendMessage("Hello World", "");
        $this->assertEquals($invalidResponse, $result);
    }
}