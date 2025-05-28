<?php

// Włączanie raportowania błędów podczas developmentu (w produkcji należy wyłączyć)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Dołączenie autoladera Composera
require_once __DIR__ . '/../vendor/autoload.php';

session_start();

use App\Core\Router;

$router = new Router();

// Strona główna
$router->add('/', function() {
    echo "<h1>Witaj na blogu!</h1>";
    echo "<p><a href='/login'>Zaloguj się</a> | <a href='/register'>Zarejestruj się</a></p>";
});

// Strona logowania
$router->add('/login', function() {
    echo "<h2>Strona logowania</h2>";
});

// Strona rejestracji
$router->add('/register', function() {
    echo "<h2>Strona rejestracji</h2>";

});

// Uruchomienie routera
$router->dispatch();

?>
