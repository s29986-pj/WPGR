<?php

// Wczytywanie zmiennych środowiskowych z pliku .env
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
            $_SERVER[trim($key)] = trim($value);
        }
    }
}

// Zmienne środowiskowe do bazy
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'blog_db');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? 'root');

// Ścieżki do folderów
define('UPLOAD_DIR', __DIR__ . '/../public/uploads/images/');
define('LOG_DIR', __DIR__ . '/../logs/');

// Nazwa aplikacji i autor
define('APP_NAME', 'Projekt_Blog');
define('AUTHOR', 's29986');

?>
