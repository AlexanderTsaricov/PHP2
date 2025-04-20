<?php

require_once 'vendor/autoload.php';

use App\src\Controllers\Save_event;
use App\src\Controllers\Tg_controllers\Start;
use PHPUnit\Framework\Attributes\TestWith;
use App\src\Service\TelegramApi;
use App\src\View\View;

class StartTest extends PHPUnit\Framework\TestCase {

    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[TestWith([12345])]
    #[TestWith([23456])]
    #[TestWith([34567])]
    public function testRunWithArrayWithValidCron(int $user_id) {
        $testResponseName = [
            'ok'=> true,
            'result' => [
                [
                    'message' => [
                        'text' => '/start'
                    ]
                ],
                [
                    'message'=> [
                        'text'=> 'testName'
                    ]
                ]
            ],
        ];
        $testResponseText = [
            'ok'=> true,
            'result' => [
                [
                    'message' => [
                        'text' => '/start'
                    ]
                ],
                [
                    'message'=> [
                        'text'=> 'testName'
                    ]
                ],
                [
                    'message'=> [
                        'text'=> 'test text'
                    ]
                ]

            ],
        ];
        $testResponseInvlidCron = [
            'ok'=> true,
            'result' => [
                [
                    'message' => [
                        'text' => '/start'
                    ]
                ],
                [
                    'message'=> [
                        'text'=> 'testName'
                    ]
                ],
                [
                    'message'=> [
                        'text'=> 'test text'
                    ]
                ],
                [
                    'message'=> [
                        'text'=> 'invalid'
                    ]
                ]
            ],
        ];
        $testResponseValidCron = [
            'ok'=> true,
            'result' => [
                [
                    'message' => [
                        'text' => '/start'
                    ]
                ],
                [
                    'message'=> [
                        'text'=> 'testName'
                    ]
                ],
                [
                    'message'=> [
                        'text'=> 'test text'
                    ]
                ],
                [
                    'message'=> [
                        'text'=> 'invalid'
                    ]
                ],
                [
                    'message'=> [
                        'text'=> '* * * * *'
                    ]
                ]
            ],
        ];
        

        $mockApi = \Mockery::mock(TelegramApi::class);
        $subView = \Mockery::mock(View::class);
        $mockSaveEvent = \Mockery::mock(Save_event::class);



        $mockApi->shouldReceive('sendMessage')->once()->with("Please, write name event", $user_id);
        $mockApi->shouldReceive('getMessages')->once()->andReturn($testResponseName);
        $name = $testResponseName['result'][1]['message']['text'];
        $mockApi->shouldReceive('sendMessage')->once()->with("Ok, your name event is '" . $name . "'", $user_id);
        $mockApi->shouldReceive('sendMessage')->once()->with("Please, write text event", $user_id);
        $mockApi->shouldReceive('getMessages')->once()->andReturn($testResponseText);
        $text = $testResponseText['result'][2]['message']['text'];
        $mockApi->shouldReceive('sendMessage')->once()->with("Ok, your text event is '" . $text . "'", $user_id);
        $mockApi->shouldReceive('sendMessage')->once()->with("Please, write time event in cron format (* * * * *)", $user_id);
        $mockApi->shouldReceive('getMessages')->once()->andReturn($testResponseInvlidCron);
        $cron = $testResponseInvlidCron['result'][3]['message']['text'];
        $mockApi->shouldReceive('sendMessage')->once()->with("Invalid time format", $user_id);
        $mockApi->shouldReceive('sendMessage')->once()->with("Please, write time event in cron format (* * * * *)", $user_id);
        $mockApi->shouldReceive('getMessages')->once()->andReturn($testResponseValidCron);
        $cron = $testResponseValidCron['result'][4]['message']['text'];
        $mockApi->shouldReceive('sendMessage')->once()->with("Ok, your crone time event is '" . $cron . "'", $user_id);
        $mockApi->shouldReceive('sendMessage')->once()->with("Enter event sussful. New event - '$name', text - '$text' evrytime in '$cron'", $user_id);
        $mockSaveEvent->shouldReceive('run')->once()->with(["name" => $name, "receiver" => $user_id, "text" => $text, "cron" => $cron]);

        $start = new Start($subView, $mockApi, $mockSaveEvent);
        $start->run(["user_id" => $user_id, 'sleep' => 0]);

        $this->assertTrue(true);

    }
}