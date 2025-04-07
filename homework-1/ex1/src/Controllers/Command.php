<?php

namespace App\src\Controllers;

abstract class Command {
    protected $name;
    protected $description;

    abstract public function run(array $options = []);

    
}