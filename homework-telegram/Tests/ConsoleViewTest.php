<?php

use App\src\Logs\Logs;
use App\src\View\ConsoleView;
require_once 'vendor/autoload.php';


class ConsoleViewTest extends PHPUnit\Framework\TestCase {

    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function testSendWithErrorFalse() {
        $exensionMessage = 'test text';
        $mockLogger = \Mockery::mock(Logs::class);

        $view = new ConsoleView($mockLogger);

        ob_start();
        $mockLogger->shouldReceive('write')->once()->with($exensionMessage);
        $view->send($exensionMessage);
        $output = ob_get_clean();

        $this->assertEquals($exensionMessage . "\n", $output);
    }

    public function testSendWithErrorTrue() {
        $testText = "test text";
        $exensionMessage = "\033[31m" . 'test text' . "\033[0m";
        $mockLogger = \Mockery::mock(Logs::class);

        $view = new ConsoleView($mockLogger);

        ob_start();
        $mockLogger->shouldReceive('write')->once()->with($exensionMessage);
        $view->send($testText, true);
        $output = ob_get_clean();        


        $this->assertEquals($exensionMessage . "\n", $output);
    }
}