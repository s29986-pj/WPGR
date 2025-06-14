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
}
