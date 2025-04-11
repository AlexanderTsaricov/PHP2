<?php

namespace App\src\View;

use App\src\Logs\Logs;

class ConsoleView extends View {
    public function send($message, $error=false) {
        $logger = new Logs();
        if ($error) {
            $outMessage = "\033" . $message . "\033[0m" ."\n";
        } else {
            $outMessage = "". $message . "\n";
        }
        $logger->write($outMessage);
        echo $outMessage;
    }
}