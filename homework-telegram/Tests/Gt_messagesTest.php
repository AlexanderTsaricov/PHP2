<?php

use App\src\Controllers\Gt_messages;
use App\src\Service\TelegramApi;
use App\src\View\View;

require_once 'vendor/autoload.php';

class Gt_messagesTest extends PHPUnit\Framework\TestCase {

    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function testRunWithGoodResponse() {
        $responseData = [
            
            'ok'=> true,
            'result' => [
                [
                    'message' => [
                        'message_id' => 1,
                        'from' => [
                            'username' => 'testuserName',
                            'id'=> 123,
                        ],
                        'text'=> 'test text'
                    ]
                ]
            ]
            
        ];

        $message = $responseData['result'][0];
        $sendToViewMessage = '';
        $sendToViewMessage .= "Message id: " . $message['message']['message_id'] . "\n";
        $sendToViewMessage .= "From user: ". $message['message']["from"]["username"] . " with id: " . $message['message']["from"]["id"] . "\n";
        $sendToViewMessage .= "Text: ". $message['message']["text"];

        $mockApi = \Mockery::mock(TelegramApi::class);
        $mockView = \Mockery::mock(View::class);


        $mockApi->shouldReceive('getMessages')
        ->once()
        ->andReturn($responseData);
        $mockView->shouldReceive('send')
        ->once()
        ->with($sendToViewMessage);


        $gt_messages = new Gt_messages($mockView, $mockApi);
        $gt_messages->run();

        $this->assertTrue(true);
    }

    public function testRunWithBadResponse () {
        $responseData = [
            
            'ok'=> false,
            'error_code' => 404
            
        ];

        $mockApi = \Mockery::mock(TelegramApi::class);
        $mockView = \Mockery::mock(View::class);


        $mockApi->shouldReceive('getMessages')
        ->once()
        ->andReturn($responseData);
        $mockView->shouldReceive('send')
        ->once()
        ->with('Error: 404');


        $gt_messages = new Gt_messages($mockView, $mockApi);
        $gt_messages->run();

        $this->assertTrue(true);
    }
}