<?php

namespace App\Core;

class AppLogger
{
    // Zapisuje wpis do pliku logów aplikacji
    public static function log(string $level, string $message, array $context = [])
    {
        // Tworzy folder, jeśli nie istnieje
        if (!is_dir(LOG_DIR)) {
            mkdir(LOG_DIR, 0775, true);
        }
        $logFile = LOG_DIR . 'app.log';

        // Format
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] [$level]: $message";

        // Jeśli jest dodatkowy kontekst, dołącza go w formacie JSON
        if (!empty($context)) {
            $logEntry .= " | " . json_encode($context);
        }

        // Znak nowej linii na końcu wpisu
        $logEntry .= PHP_EOL;

        // Zapisuje wpis do pliku, dopisując na końcu
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
}
