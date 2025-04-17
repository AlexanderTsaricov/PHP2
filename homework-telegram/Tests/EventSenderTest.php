<?php
require_once 'vendor/autoload.php';
use App\src\Service\TelegramApi;
use App\src\Controllers\EventSender;
use App\src\View\ConsoleView;
use phpmock\MockBuilder;
use phpmock\mockery\PHPMockery;



class EventSenderTest extends PHPUnit\Framework\TestCase  {

    protected $dateMock;
    protected function tearDown(): void
    {
        \Mockery::close();
        if ($this->dateMock) {
            $this->dateMock->disable();
        }
        parent::tearDown();
    }
    public function testRunValidData() {
        $options = [
            'message'=> 'testMessage',
            'receiver'=> 12345,
        ];
        
        $stubApi = \Mockery::mock(TelegramApi::class);
        $stubApi->shouldReceive('sendMessage')->andReturn([
            'ok'=> true
        ]);

        $fixedDate = '01.01.23 12:34';
        $expectedMessage = $fixedDate . " Я отправил сообщение " . $options['message'] . " получателю с id " . $options['receiver'];

        $builder = new MockBuilder();
        $builder->setNamespace('App\src\Controllers') // пространство имён, где вызывается `date()`
                ->setName('date')
                ->setFunction(function ($format) use ($fixedDate) {
                    return $fixedDate;
                });
        $this->dateMock = $builder->build();
        $this->dateMock->enable();

        $stubView = \Mockery::mock(ConsoleView::class);
        $stubView->shouldReceive('send')
        ->once()
        ->with($expectedMessage);

        $sender = new EventSender($stubApi, $stubView);

        $sender->run($options);
        $this->assertTrue(true);
    }

    public function testRunInvalidData() {
        $options = [
            'message'=> 'testMessage',
            'receiver'=> 12345,
        ];
        
        $stubApi = \Mockery::mock(TelegramApi::class);
        $stubApi->shouldReceive('sendMessage')->andReturn([
            'ok'=> false,
            'error_code' => 400
        ]);

        $fixedDate = '01.01.23 12:34';
        $expectedMessage = "Error: 400";

        $stubView = \Mockery::mock(ConsoleView::class);
        $stubView->shouldReceive('send')
        ->once()
        ->with($expectedMessage);

        $sender = new EventSender($stubApi, $stubView);

        $sender->run($options);
        $this->assertTrue(true);
    }
}