<?php

namespace App\src\Controllers;

use App\src\Service\TelegramApi;
use App\src\View\ConsoleView;
use App\src\View\View;

abstract class Command {
    protected $name;
    protected $description;

    protected ConsoleView $view;

    protected TelegramApi $telegramApi;

    public function run(array $options = []) {
        echo "run command";
    }

    public function __construct(View $view, TelegramApi $telegramApi) {
        $this->view = $view;
        $this->telegramApi = $telegramApi;
    }

    
}