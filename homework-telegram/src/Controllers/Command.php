<?php

namespace App\src\Controllers;

use App\src\Service\TelegramApi;
use App\src\View\ConsoleView;
use App\src\View\View;

abstract class Command {
    protected $name;
    protected $description;

    protected View $view;

    protected TelegramApi $telegramApi;

    abstract public function run(array $options = []);

    public function __construct(View $view, TelegramApi $telegramApi) {
        $this->view = $view;
        $this->telegramApi = $telegramApi;
    }

    
}