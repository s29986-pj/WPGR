<?php

namespace App\Controllers;

use App\Models\Post;
use function App\Utils\view;

class PostController
{
    private $postModel;

    public function __construct()
    {
        $this->postModel = new Post();
    }


    // Wyświetla listę wszystkich postów (strona główna)
    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $postsPerPage = 5;
        $offset = ($page - 1) * $postsPerPage;

        $posts = $this->postModel->getAllPosts($postsPerPage, $offset);
        $totalPosts = $this->postModel->countAllPosts();
        $totalPages = ceil($totalPosts / $postsPerPage);

        view('posts/index', [
            'pageTitle' => 'Blog - Strona Główna',
            'posts' => $posts,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }


    // Wyświetla pojedynczy post
    public function show(array $params)
    {
        $id = $params['id'] ?? null;

        if (!$id) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $post = $this->postModel->getPostById($id);

        if (!$post) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        view('posts/show', [
            'pageTitle' => $post['title'],
            'post' => $post
        ]);
    }


    // Wyświetla formularz dodawania nowego posta
    public function createForm()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/login?status=not_logged_in_post');
            exit();
        }

        view('posts/create_edit', [
            'pageTitle' => 'Dodaj nowy post',
            'isEditing' => false,
            'post' => []
        ]);
    }


    // Obsługuje dodawanie nowego posta
    public function store()
    {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $userId = $_SESSION['user_id'];

        $error = null;
        if (empty($title) || empty($content)) {
            $error = "Tytuł i treść posta są wymagane.";
        }

        if ($error) {
            view('posts/create_edit', [
                'pageTitle' => 'Dodaj nowy post',
                'isEditing' => false,
                'post' => ['title' => $title, 'content' => $content],
                'error' => $error
            ]);
            return;
        }

        $postId = $this->postModel->createPost($userId, $title, $content);

        if ($postId) {
            header('Location: ' . BASE_PATH . '/posts/' . $postId . '?status=added');
            exit();
        } else {
            view('posts/create_edit', [
                'pageTitle' => 'Dodaj nowy post',
                'isEditing' => false,
                'post' => ['title' => $title, 'content' => $content],
                'error' => "Wystąpił błąd podczas dodawania posta."
            ]);
        }
    }


    // Wyświetla formularz edycji istniejącego posta
    public function editForm(array $params)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/login?status=not_logged_in_post_edit');
            exit();
        }

        $id = $params['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $post = $this->postModel->getPostById($id);

        if (!$post || $post['user_id'] !== $_SESSION['user_id']) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        view('posts/create_edit', [
            'pageTitle' => 'Edytuj post: ' . $post['title'],
            'isEditing' => true,
            'post' => $post
        ]);
    }


    // Obsługuje aktualizację istniejącego posta
    public function update(array $params)
    {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $id = $params['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $existingPost = $this->postModel->getPostById($id);

        if (!$existingPost || $existingPost['user_id'] !== $_SESSION['user_id']) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';

        $error = null;
        if (empty($title) || empty($content)) {
            $error = "Tytuł i treść posta są wymagane.";
        }

        if ($error) {
            view('posts/create_edit', [
                'pageTitle' => 'Edytuj post: ' . $title,
                'isEditing' => true,
                'post' => ['id' => $id, 'title' => $title, 'content' => $content],
                'error' => $error
            ]);
            return;
        }

        if ($this->postModel->updatePost($id, $title, $content)) {
            header('Location: ' . BASE_PATH . '/posts/' . $id . '?status=updated');
            exit();
        } else {
            view('posts/create_edit', [
                'pageTitle' => 'Edytuj post: ' . $title,
                'isEditing' => true,
                'post' => ['id' => $id, 'title' => $title, 'content' => $content],
                'error' => "Wystąpił błąd podczas aktualizacji posta."
            ]);
        }
    }


    //  Obsługuje usuwanie posta
    public function delete(array $params)
    {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $id = $params['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $post = $this->postModel->getPostById($id);

        if (!$post || $post['user_id'] !== $_SESSION['user_id']) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        if ($this->postModel->deletePost($id)) {
            header('Location: ' . BASE_PATH . '/?status=deleted');
            exit();
        } else {
            header('Location: ' . BASE_PATH . '/posts/' . $id . '?status=delete_error');
            exit();
        }
    }
}
