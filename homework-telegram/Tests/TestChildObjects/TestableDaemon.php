<?php

namespace App\Tests\TestChildObjects;
use App\src\Controllers\DeamonEventsSender;

class TestableDaemon extends DeamonEventsSender {
    public function __construct($view, $api, $eventHandler, $logger) {
        parent::__construct($view, $api, $eventHandler, $logger);
    }

    protected function setupSignalHandlers() {}

    public function stopDaemon() {
        $this->running = false;
    }
}