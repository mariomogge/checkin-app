<?php

namespace App\Database;

use PDO;

class Database
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (!self::$pdo) {
            $config = require __DIR__ . '/../Config/config.php';

            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                $config['db']['host'],
                $config['db']['port'],
                $config['db']['name']
            );

            self::$pdo = new PDO($dsn, $config['db']['user'], $config['db']['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        }

        return self::$pdo;
    }
}
