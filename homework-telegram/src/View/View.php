<?php

namespace App\src\View;

abstract class View {
    abstract public function send(string $message);
}