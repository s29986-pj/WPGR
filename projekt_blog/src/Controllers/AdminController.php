<?php

namespace App\Controllers;

use function App\Utils\view;

class AdminController
{
    public function __construct()
    {
        // Sprawdza, czy użytkownik jest zalogowany i czy jego rola to 'admin'
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            // Jeśli nie, przekierowuje na stronę główną
            header('Location: ' . BASE_PATH . '/');
            exit();
        }
    }

    // Wyświetla główną stronę panelu administratora (dashboard).
    public function dashboard()
    {
        view('admin/dashboard', [
            'pageTitle' => 'Panel Administratora'
        ]);
    }
}
