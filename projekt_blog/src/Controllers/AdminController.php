<?php

namespace App\Controllers;

use function App\Utils\view;
use App\Models\Post; 

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

    // Wyświetla listę wszystkich postów do zarządzania
    public function managePosts()
    {
        $postModel = new Post();

        $page = $_GET['page'] ?? 1;
        $postsPerPage = 10;
        $offset = ($page - 1) * $postsPerPage;

        $posts = $postModel->getAllPosts($postsPerPage, $offset);
        $totalPosts = $postModel->countAllPosts();
        $totalPages = ceil($totalPosts / $postsPerPage);

        view('admin/manage_posts', [
            'pageTitle' => 'Zarządzaj Postami',
            'posts' => $posts,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    // Wyświetla logi formularza kontaktowego
    public function viewContactLogs()
    {
        $logFile = LOG_DIR . 'contact_messages.log';
        $logContent = '';

        if (file_exists($logFile)) {
            $logContent = file_get_contents($logFile);
        } else {
            $logContent = 'Plik logów (contact_messages.log) nie został jeszcze utworzony lub jest pusty.';
        }

        view('admin/view_logs', [
            'pageTitle' => 'Logi Formularza Kontaktowego',
            'logTitle' => 'Logi Formularza Kontaktowego',
            'logDescription' => 'Poniżej znajduje się lista logów z pliku',
            'logFileName' => 'contact_messages.log',
            'logContent' => $logContent
        ]);
    }

    // Wyświetla logi zdarzeń aplikacji
    public function viewAppLogs()
    {
        $logFile = LOG_DIR . 'app.log';
        $logContent = '';

        if (file_exists($logFile)) {
            $logContent = file_get_contents($logFile);
        } else {
            $logContent = 'Plik logów aplikacji (app.log) jest pusty.';
        }

        view('admin/view_logs', [
            'pageTitle' => 'Logi Aplikacji',
            'logTitle' => 'Logi Zdarzeń Aplikacji',
            'logDescription' => 'Poniżej znajduje się lista zdarzeń zarejestrowanych w aplikacji z pliku',
            'logFileName' => 'app_events.log',
            'logContent' => $logContent
        ]);
    }
}
