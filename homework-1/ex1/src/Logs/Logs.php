<?php

namespace App\src\Logs;

class Logs {
    public function write($message) {
        $stout = fopen(__DIR__ . "/output.log","ab");
        fwrite($stout, $message . "\n");
        fclose($stout);
    }
}