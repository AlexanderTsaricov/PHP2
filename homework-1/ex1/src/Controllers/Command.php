<?php

namespace App\src\Controllers;
use App\src\View\ConsoleView;

abstract class Command {
    protected $name;
    protected $description;

    protected ConsoleView $view;

    public function run(array $options = []) {
        echo "run command";
    }

    public function __construct() {
        $this->view = new ConsoleView();
    }

    
}