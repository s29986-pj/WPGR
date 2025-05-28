<?php

namespace App\Core;

class Router
{
    private $routes = [];

    // Dodaje nowe ścieżki
    public function add(string $uri, callable $callback)
    {
        $this->routes[$uri] = $callback;
    }

    // Dopasowuje adres URL do ścieżki
    public function dispatch()
    {
        $requestUri = $_SERVER['REQUEST_URI']; // Pełny adres URL żądany przez przeglądarkę

        $scriptName = $_SERVER['SCRIPT_NAME']; // Ścieżka do pliku index.php

        // Ścieżka bazowa
        $basePath = str_replace('\\', '/', dirname($scriptName));
        $basePath = rtrim($basePath, '/');

        // Odcinanie $basePath od $requestUri, żeby została tylko czysta ścieżka
        if (strpos($requestUri, $basePath) === 0) {
            $uri = substr($requestUri, strlen($basePath));
        } else {
            $uri = $requestUri;
        }

        // Czyszczenie ścieżki ze zbędnych znaków
        $uri = strtok($uri, '?');
        $uri = rtrim($uri, '/');

        // Wejście na stroną główną z folderu /public
        if (empty($uri)) {
            $uri = '/';
        }

        // Sprawdzanie, czy dana ścieżka istnieje
        if (array_key_exists($uri, $this->routes)) {
            call_user_func($this->routes[$uri]);
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "<h1>404 Not Found</h1><p>Strona nie istnieje.</p>";
        }
    }
}
