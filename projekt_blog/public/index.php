<?php

// Włączanie raportowania błędów podczas developmentu
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Dołączenie autoladera Composera, configu i funkcji widoków
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../src/Utils/view.php'; 

session_start();

// Globalna zmienna ze ścieżką
$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$basePath = rtrim($basePath, '/');
define('BASE_PATH', $basePath);

use App\Core\Router;
use function App\Utils\view;
use App\Controllers\AuthController;

$router = new Router();
$authController = new AuthController();


// Strona główna
$router->add('/', function() {
    view('home', ['pageTitle' => 'Strona główna']);
});


// Strona rejestracji
$router->add('/register', function() {
    view('auth/register', ['pageTitle' => 'Rejestracja']);
});// GET - wyświetl formularz
$router->add('/register', [$authController, 'register'], 'POST'); // POST - obsłuż formularz


// Strona logowania
$router->add('/login', function() {
    $status_message = $_GET['status'] ?? '';
    view('auth/login', ['pageTitle' => 'Logowanie', 'status_message' => $status_message]);
}); // GET - wyświetl formularz
$router->add('/login', [$authController, 'login'], 'POST'); // POST - obsłuż formularz


// Wylogowanie
$router->add('/logout', [$authController, 'logout']);


// Weryfikacja e-maila
$router->add('/verify-email', [$authController, 'verifyEmail']);


// Resetowanie hasła
$router->add('/forgot-password', function() {
    view('auth/forgot_password', ['pageTitle' => 'Resetowanie hasła']);
}); // GET - wyświetl formularz
$router->add('/forgot-password', [$authController, 'sendResetLink'], 'POST'); // POST - wyślij link
$router->add('/reset-password', function() {
    $token = $_GET['token'] ?? '';
    if (empty($token)) {
        // Jeśli brak tokena, przekierowuje na stronę "zapomniałem hasła".
        $basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $basePath = rtrim($basePath, '/');
        header("Location: " . $basePath . "/forgot-password");
        exit();
    }
    view('auth/reset_password', ['pageTitle' => 'Ustaw nowe hasło', 'token' => $token]);
});// GET - wyświetl formularz
$router->add('/reset-password', [$authController, 'resetPassword'], 'POST');// POST - obsłuż reset



// Uruchomienie routera
$router->dispatch();

?>
