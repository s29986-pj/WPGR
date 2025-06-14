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
use App\Controllers\PostController;
use App\Controllers\AdminController; 
use App\Controllers\ContactController;


$router = new Router();
$authController = new AuthController();
$postController = new PostController();
$contactController = new ContactController();


// Strona główna
$router->add('/', [$postController, 'index']); 





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
        header("Location: " . BASE_PATH . "/forgot-password");
        exit();
    }
    view('auth/reset_password', ['pageTitle' => 'Ustaw nowe hasło', 'token' => $token]);
});// GET - wyświetl formularz
$router->add('/reset-password', [$authController, 'resetPassword'], 'POST');// POST - obsłuż reset


// Zmiana hasła przez zalogowanego użytkownika
$router->add('/change-password', [$authController, 'showChangePasswordForm']);
$router->add('/change-password', [$authController, 'handleChangePassword'], 'POST');




// Dodawanie posta
$router->add('/posts/create', [$postController, 'createForm']);
$router->add('/posts/create', [$postController, 'store'], 'POST');


// Wyświetl pojedynczy post
// ':id' to placeholder, który zostanie przekazany do metody show() w $params['id']
$router->add('/posts/:id', [$postController, 'show']);


// Edycja posta
$router->add('/posts/:id/edit', [$postController, 'editForm']);
$router->add('/posts/:id/edit', [$postController, 'update'], 'POST');


// Usuwanie posta
$router->add('/posts/:id/delete', [$postController, 'delete'], 'POST');


// Dodawanie komentarza
$router->add('/posts/:id/comments', [$postController, 'addComment'], 'POST');



// Panel admina
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (str_starts_with($requestUri, BASE_PATH . '/admin')) {
    $adminController = new AdminController();
    $router->add('/admin', [$adminController, 'dashboard']);
    $router->add('/admin/manage-posts', [$adminController, 'managePosts']); 
}



// Kontakt
$router->add('/contact', [$contactController, 'showContactForm']); // GET - wyświetl formularz
$router->add('/contact', [$contactController, 'handleContactForm'], 'POST'); // POST - obsłuż



// Uruchomienie routera
$router->dispatch();

?>
