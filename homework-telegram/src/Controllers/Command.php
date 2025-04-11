<?php

namespace App\src\Controllers;

use App\src\Service\TelegramApi;
use App\src\View\ConsoleView;

abstract class Command {
    protected $name;
    protected $description;

    protected ConsoleView $view;

    protected TelegramApi $telegramApi;

    public function run(array $options = []) {
        echo "run command";
    }

    public function __construct() {
        $this->view = new ConsoleView();
        $this->telegramApi = new TelegramApi();
    }

    
}