<?php

namespace App\Core;

require_once __DIR__ . '/../../config/app.php';

class Database
{
    private static $instance = null;
    private $mysqli;

    private function __construct()
    {
        $this->mysqli = new \mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($this->mysqli->connect_errno) {
            error_log("Błąd połączenia z bazą danych: " . $this->mysqli->connect_error);
            die("Błąd połączenia z bazą danych. Spróbuj później.");
        }

        $this->mysqli->set_charset("utf8mb4");
    }

    // Metoda do pobierania instancji bazy danych
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->mysqli;
    }

}
