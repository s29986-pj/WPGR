<?php

namespace App\Controllers;

use function App\Utils\view;
use App\Models\Post;
use App\Models\User; 
use App\Core\AppLogger; 

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

    // Wyświetla listę użytkowników do zarządzania
    public function manageUsers()
    {
        $userModel = new User();
        $currentAdminId = $_SESSION['user_id'];

        $page = $_GET['page'] ?? 1;
        $usersPerPage = 10;
        $offset = ($page - 1) * $usersPerPage;

        $users = $userModel->getAllUsers($currentAdminId, $usersPerPage, $offset);
        $totalUsers = $userModel->countAllUsers($currentAdminId);
        $totalPages = ceil($totalUsers / $usersPerPage);

        view('admin/manage_users', [
            'pageTitle' => 'Zarządzaj Użytkownikami',
            'users' => $users,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    //  Przetwarza akcje na użytkownikach (zmiana roli, usunięcie)
    public function processUserAction()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_PATH . '/admin/manage-users');
            exit();
        }

        $userModel = new User();
        $userId = $_POST['user_id'] ?? null;
        $action = $_POST['action'] ?? null;
        $adminId = $_SESSION['user_id'];

        if ($action === 'change_role') {
            $newRole = $_POST['role'] ?? 'user';
            $allowedRoles = ['user', 'author', 'admin'];

            // Zmienia rolę tylko jeśli jest na liście dozwolonych
            if (in_array($newRole, $allowedRoles)) {
                if ($userModel->updateUserRole($userId, $newRole)) {
                    AppLogger::log('SECURITY', 'User role changed', [
                        'admin_id' => $adminId, 
                        'target_user_id' => $userId, 
                        'new_role' => $newRole
                    ]);
                }
            }
            header('Location: ' . BASE_PATH . '/admin/manage-users?status=role_changed');
            exit();
        }

        if ($action === 'delete_user') {
            // Dodatkowe zabezpieczenie: nie można usunąć samego siebie
            if ($userId == $adminId) {
                header('Location: ' . BASE_PATH . '/admin/manage-users?status=delete_self_error');
                exit();
            }

            if ($userModel->deleteUser($userId)) {
                AppLogger::log('SECURITY', 'User account deleted', [
                    'admin_id' => $adminId, 
                    'deleted_user_id' => $userId
                ]);
            }
            header('Location: ' . BASE_PATH . '/admin/manage-users?status=user_deleted');
            exit();
        }

        header('Location: ' . BASE_PATH . '/admin/manage-users');
        exit();
    }
}
