<?php

namespace App\src\View;

use App\src\Logs\Logs;

class ConsoleView extends View {

    protected $logger;

    public function __construct(Logs $logs) {
        $this->logger = $logs;
    }
    public function send($message, $error=false) {
        if ($error) {
            $outMessage = "\033[31m" . $message . "\033[0m";
        } else {
            $outMessage = $message;
        }
        $this->logger->write($outMessage);
        echo $outMessage . "\n";
    }
}