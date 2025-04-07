<?php

namespace App\src\Controllers;
class EventSender
{
public function sendMessage(string $receiver, string $message)
{
echo date('d.m.y H:i') . " Я отправил сообщение $message получателю с id
$receiver\n";
}
}