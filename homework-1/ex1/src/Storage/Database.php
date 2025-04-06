<?php
namespace src\Storage;

use PDO;
use PDOException;

class Database {
    private static ?PDO $connection = null;

    public static function connect(): PDO {
        if (self::$connection === null) {
            try {
                self::$connection = new PDO('sqlite:' . __DIR__ . '/../Database/bd.sqlite');
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("SQLite connection failed: " . $e->getMessage());
            }
        }

        return self::$connection;
    }
}
