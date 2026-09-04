<?php

declare(strict_types=1);

final class Database
{
    private const HOST = '127.0.0.1';
    private const PORT = '3306';
    private const DATABASE = 'fine_line_api';
    private const USERNAME = 'root';
    private const PASSWORD = '';

    public static function connect(): PDO
    {
        $host = getenv('MYSQLHOST');
        $port = getenv('MYSQLPORT');
        $database = getenv('MYSQLDATABASE');
        $username = getenv('MYSQLUSER');
        $password = getenv('MYSQLPASSWORD');

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $host !== false ? $host : self::HOST,
            $port !== false ? $port : self::PORT,
            $database !== false ? $database : self::DATABASE
        );

        return new PDO(
            $dsn,
            $username !== false ? $username : self::USERNAME,
            $password !== false ? $password : self::PASSWORD,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }
}
