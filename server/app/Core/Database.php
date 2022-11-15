<?php

namespace App\Core;

use Exception;
use PDO;
use PDOException;

class Database
{
    private string $driver;

    private string $host;

    private string $port;

    private string $database;

    private string $username;

    private string $password;


    /**
     * @var PDO
     */
    protected PDO $connection;

    public function __construct()
    {
        $this->connection();

        $dsn = "{$this->driver}:host={$this->host};port={$this->port};dbname={$this->database};charset=utf8mb4";

        $this->connection = new PDO($dsn, $this->username, $this->password, [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    /**
     * @throws Exception
     */
    public function query(string $sql, array $bindings = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);

            $stmt->execute($bindings);

            return $stmt;
        } catch (PDOException $exception) {
            throw new Exception($exception);
        }
    }

    protected function connection()
    {
        $this->driver = config('database.connection')['driver'];
        $this->host = config('database.connection')['host'];
        $this->port = config('database.connection')['port'];
        $this->database = config('database.connection')['database'];
        $this->username = config('database.connection')['username'];
        $this->password = config('database.connection')['password'];
    }

    public function db(): PDO
    {
        return $this->connection;
    }
}