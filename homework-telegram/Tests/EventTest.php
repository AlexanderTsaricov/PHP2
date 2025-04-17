<?php

require_once 'vendor/autoload.php';
use App\src\Model\Event;
use PHPUnit\Framework\Attributes\TestWith;

class EventTest extends PHPUnit\Framework\TestCase {

    #[TestWith([1, 'Alex', 2, 'Text-1', '* * * * *'])]
    #[TestWith([3, 'Igor', 4, 'Text-2', '5 * * * *'])]
    #[TestWith([5, 'Sveta', 6, 'Text-1', '* * * * *'])]
    public function testGetWithArrays(int $id, string $name, int $receiver, string $text, string $crone) {
        $event = new Event($id, $name, $receiver, $text, $crone);
        $getResult = $event->get();
        $actualArray = [$getResult['name'], $getResult['receiver'], $getResult['text'], $getResult['cron']];
        $expectedArray = [$name, $receiver, $text, $crone];
        $this->assertSame($expectedArray, $actualArray);

    }
    
    #[TestWith([1, 'Alex', 2, 'Text-1', '* * * * *'])]
    #[TestWith([3, 'Igor', 4, 'Text-2', '5 * * * *'])]
    #[TestWith([5, 'Sveta', 6, 'Text-1', '* * * * *'])]
    public function testGetIdWithArrays(int $id, string $name, int $receiver, string $text, string $crone) {
        $event = new Event($id, $name, $receiver, $text, $crone);
        $getResult = $event->getId();
        $this->assertEquals($id, $getResult);
    }

    #[TestWith([1, 'Alex', 2, 'Text-1', '* * * * *'])]
    #[TestWith([3, 'Igor', 4, 'Text-2', '5 * * * *'])]
    #[TestWith([5, 'Sveta', 6, 'Text-1', '* * * * *'])]
    public function testGetNameWithArrays(int $id, string $name, int $receiver, string $text, string $crone) {
        $event = new Event($id, $name, $receiver, $text, $crone);
        $getResult = $event->getName();
        $this->assertEquals($name, $getResult);
    }

    #[TestWith([1, 'Alex', 2, 'Text-1', '* * * * *'])]
    #[TestWith([3, 'Igor', 4, 'Text-2', '5 * * * *'])]
    #[TestWith([5, 'Sveta', 6, 'Text-1', '* * * * *'])]
    public function testGetCronWithArrays(int $id, string $name, int $receiver, string $text, string $crone) {
        $event = new Event($id, $name, $receiver, $text, $crone);
        $getResult = $event->getCron();
        $this->assertEquals($crone, $getResult);
    }

    #[TestWith([1, 'Alex', 2, 'Text-1', '* * * * *'])]
    #[TestWith([3, 'Igor', 4, 'Text-2', '5 * * * *'])]
    #[TestWith([5, 'Sveta', 6, 'Text-1', '* * * * *'])]
    public function testGetTextWithArrays(int $id, string $name, int $receiver, string $text, string $crone) {
        $event = new Event($id, $name, $receiver, $text, $crone);
        $getResult = $event->getText();
        $this->assertEquals($text, $getResult);
    }

    #[TestWith([1, 'Alex', 2, 'Text-1', '* * * * *'])]
    #[TestWith([3, 'Igor', 4, 'Text-2', '5 * * * *'])]
    #[TestWith([5, 'Sveta', 6, 'Text-1', '* * * * *'])]
    public function testGetReceiverWithArrays(int $id, string $name, int $receiver, string $text, string $crone) {
        $event = new Event($id, $name, $receiver, $text, $crone);
        $getResult = $event->getReceiver();
        $this->assertEquals($receiver, $getResult);
    }
}