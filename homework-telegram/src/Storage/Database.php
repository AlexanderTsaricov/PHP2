<?php
namespace App\src\Storage;

use App\src\View\View;
use PDO;

class Database {
    private static ?PDO $connection = null;

    private View $view;

    public function __construct(View $view) {
        $this->view = $view;
    }

    public function connect($pathToBd = null): PDO | null {
        if (self::$connection === null) {
            $pathToDb = __DIR__ . '/../Database/bd.sqlite';
            self::$connection = new PDO('sqlite:' . $pathToDb);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        }

        return self::$connection;
    }
}
