<?php

namespace App\src\View;

class ConsoleView extends View {
    public function send($message, $error=false) {
        if ($error) {
            echo "\033" . $message . "\033[0m" ."\n";
        } else {
            echo "". $message . "\n";
        }
    }
}