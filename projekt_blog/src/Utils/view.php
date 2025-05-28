<?php

namespace App\Utils;

require_once __DIR__ . '/../../config/app.php';

// Funkcja pomocnicza do renderowania widoków PHP z użyciem głównego layoutu.
function view(string $viewName, array $data = [], ?string $pageTitle = null)
{
    $data['basePath'] = BASE_PATH;

    // Wyodrębnianie zmiennych z tablicy $data
    // Np. $data['error'] stanie się $error, $data['user'] stanie się $user
    extract($data);

    // Buforowanie wyjścia
    ob_start();

    // Dołącza właściwy plik widoku
    $viewFile = __DIR__ . '/../Views/' . $viewName . '.php';
    if (file_exists($viewFile)) {
        require_once $viewFile;
    } else {
        // Jeśli plik widoku nie istnieje, zaloguj błąd i wyświetl coś podstawowego
        error_log("View file not found: " . $viewFile);
        echo "<h1>Błąd: Widok '" . htmlspecialchars($viewName) . "' nie został znaleziony.</h1>";
    }
    // Pobiera całą przechwyconą zawartość widoku i czyści bufor.
    $pageContent = ob_get_clean();

    // Dołącza główny layout
    require_once __DIR__ . '/../Views/layouts/main.php';
}

?>
