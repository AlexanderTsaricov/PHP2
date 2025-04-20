<?php

use App\src\View\ConsoleView;
require_once 'vendor/autoload.php';


class ConsoleViewTest extends PHPUnit\Framework\TestCase {

    public function testSendWithErrorFalse() {
        $exensionMessage = 'test text';

        $view = new ConsoleView();

        ob_start();
        $view->send($exensionMessage);
        $output = ob_get_clean();

        $this->assertEquals($exensionMessage . "\n", $output);
    }

    public function testSendWithErrorTrue() {
        $exensionMessage = 'test text';

        $view = new ConsoleView();

        ob_start();
        $view->send($exensionMessage, true);
        $output = ob_get_clean();

        $this->assertEquals("\033" . $exensionMessage . "\033[0m\n", $output);
    }
}