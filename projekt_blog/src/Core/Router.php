<?php

namespace App\Core;

class Router
{
    // Osobne ścieżki dla GET i POST
    private $routes = [
        'GET' => [],
        'POST' => []
    ];

    // Dodaje nowe ścieżki
    public function add(string $uri, callable|array $callback, string $method = 'GET')
    {
        $uri = '/' . trim($uri, '/');
        if ($uri === '//') {
            $uri = '/';
        }
        $method = strtoupper($method);
        $this->routes[$method][$uri] = $callback;
    }

    // Dopasowuje adres URL do ścieżki
    public function dispatch()
    {
        // Pobierz i oczyść URI z REQUEST_URI
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

        // Usuń BASE_PATH z Request URI
        if (str_starts_with($requestUri, $basePath)) {
            $uri = substr($requestUri, strlen($basePath));
        } else {
            $uri = $requestUri;
        }

        $uri = '/' . trim($uri, '/');
        if ($uri === '//') {
            $uri = '/';
        }

        $method = strtoupper($_SERVER['REQUEST_METHOD']);

        // Sprawdza, czy ścieżka istnieje dla danej metody
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $routeUri => $callback) {
                $pattern = preg_replace('/:[a-zA-Z0-9_]+/', '([a-zA-Z0-9_]+)', $routeUri);
                $pattern = '#^' . $pattern . '$#';

                if (preg_match($pattern, $uri, $matches)) {
                    array_shift($matches); // Usuwa pełne dopasowanie

                    // Wydobywa nazwy parametrów z routeUri
                    $paramNames = [];
                    preg_match_all('/:([a-zA-Z0-9_]+)/', $routeUri, $paramNames);
                    
                    $params = [];
                    if (!empty($paramNames[1])) {
                        foreach ($paramNames[1] as $index => $name) {
                            if (isset($matches[$index])) {
                                $params[$name] = $matches[$index];
                            }
                        }
                    }

                    // Przekazuje parametry i wywołuje callback
                    if (is_array($callback) && count($callback) == 2 && is_object($callback[0])) {
                        call_user_func_array([$callback[0], $callback[1]], [$params]);
                    } elseif (is_callable($callback)) {
                        call_user_func_array($callback, [$params]);
                    }
                    return;
                }
            }
        }
        // Wyrzuca 404 gdy nie znajdzie ścieżki
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "<p>Strona nie została znaleziona.</p>";
    }
}
