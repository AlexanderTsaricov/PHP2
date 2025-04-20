<?php
require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;
use App\src\Storage\Database;
use App\src\View\View;

class DatabaseTest extends TestCase {
    public function tearDown(): void {
        \Mockery::close();
        parent::tearDown();
    }

    public function testConnect() {
        $mockView = \Mockery::mock(View::class);
        $tempDir = sys_get_temp_dir();
        $dbFile = $tempDir . '/bd.sqlite';
        touch($dbFile);
        
        $db = new Database($mockView);
        $connect = $db->connect($dbFile);
        $this->assertInstanceOf(PDO::class, $connect);
        
        unlink($dbFile);
    }
}
