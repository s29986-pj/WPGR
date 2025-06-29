<?php

namespace App\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Core\AppLogger;
use App\Core\Mailer;
use function App\Utils\view;

class PostController
{
    private $postModel;
    private $commentModel;

    public function __construct()
    {
        $this->postModel = new Post();
        $this->commentModel = new Comment();
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

        $comments = $this->commentModel->findByPostId($id); 

        $previousPost = $this->postModel->getPreviousPost($post['created_at']);
        $nextPost = $this->postModel->getNextPost($post['created_at']);

        view('posts/show', [
            'pageTitle' => $post['title'],
            'post' => $post,
            'comments' => $comments,
            'previousPost' => $previousPost,
            'nextPost' => $nextPost
        ]);
    }


    // Wyświetla formularz dodawania nowego posta
    public function createForm()
    {
        // Sprawdza, czy użytkownik jest zalogowany ORAZ czy ma rolę 'author' lub 'admin'
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['author', 'admin'])) {
            // Przekierowanie na stronę główną z błędem braku uprawnień
            header('Location: ' . BASE_PATH . '/?status=permission_denied');
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
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['author', 'admin']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $userId = $_SESSION['user_id'];
        $imagePath = null;

        $error = null;
        if (empty($title) || empty($content)) {
            $error = "Tytuł i treść posta są wymagane.";
        }

        // Obsługa uploadu obrazka
        if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = UPLOAD_DIR;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }
            $fileName = uniqid() . '-' . basename($_FILES['post_image']['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['post_image']['tmp_name'], $targetPath)) {
                // Ścieżka względna do użycia w HTML
                $imagePath = 'uploads/images/' . $fileName;
            } else {
                $error = "Wystąpił błąd podczas przesyłania obrazka.";
            }
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

        $postId = $this->postModel->createPost($userId, $title, $content, $imagePath);

        if ($postId) {
            // Zapis do logów
            AppLogger::log('INFO', 'Post created', ['user_id' => $userId, 'post_id' => $postId, 'title' => $title]);

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

        if (!$post || ($post['user_id'] !== $_SESSION['user_id'] && ($_SESSION['user_role'] ?? '') !== 'admin')) {
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

        if (!$existingPost || ($existingPost['user_id'] !== $_SESSION['user_id'] && ($_SESSION['user_role'] ?? '') !== 'admin')) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $error = null;
        $newImagePathForDb = null;

        if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) {
            
            if (!empty($existingPost['image_path'])) {
                $oldFilePath = __DIR__ . '/../../public/' . $existingPost['image_path'];
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $uploadDir = UPLOAD_DIR;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }
            $fileName = uniqid() . '-' . basename($_FILES['post_image']['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['post_image']['tmp_name'], $targetPath)) {
                $newImagePathForDb = 'uploads/images/' . $fileName;
            } else {
                $error = "Wystąpił błąd podczas przesyłania nowego obrazka.";
            }
        }

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

        if ($this->postModel->updatePost($id, $title, $content, $newImagePathForDb)) {
            // Zapis do logów
            AppLogger::log('INFO', 'Post updated', ['user_id' => $_SESSION['user_id'], 'post_id' => $id, 'title' => $title]);

            $redirectUrl = BASE_PATH . '/posts/' . $id . '?status=updated'; // Domyślnie

            // Sprawdza, czy formularz został wysłany ze źródłem 'admin'
            if (isset($_POST['source']) && $_POST['source'] === 'admin') {
                $redirectUrl = BASE_PATH . '/admin/manage-posts?status=updated';
            }

            header('Location: ' . $redirectUrl);
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

        if (!$post || $post['user_id'] !== $_SESSION['user_id'] && $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        if (!empty($post['image_path'])) {
            $filePath = __DIR__ . '/../../public/' . $post['image_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Zapis do logów
        AppLogger::log('WARNING', 'Post deleted', ['user_id' => $_SESSION['user_id'], 'post_id' => $id, 'title' => $post['title']]);

        if ($this->postModel->deletePost($id)) {
            $redirectUrl = BASE_PATH . '/?status=deleted'; // Domyślne przekierowanie

            if (isset($_SERVER['HTTP_REFERER']) && str_contains($_SERVER['HTTP_REFERER'], '/admin/manage-posts')) {
                $redirectUrl = BASE_PATH . '/admin/manage-posts?status=deleted';
            }

            header('Location: ' . $redirectUrl);
            exit();
        } else {
            header('Location: ' . BASE_PATH . '/posts/' . $id . '?status=delete_error');
            exit();
        }
    }


    // Obsługuje dodawanie nowego komentarza
    public function addComment(array $params)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $postId = $params['id'] ?? null;
        $content = $_POST['content'] ?? '';
        $userId = $_SESSION['user_id'] ?? null;
        $authorName = null;

        // Prosta walidacja
        if (!$postId || empty($content)) {
            header('Location: ' . BASE_PATH . '/posts/' . $postId . '?status=comment_error');
            exit();
        }

        // Ustawienie nazwy autora na "Gość" jeśli jest pusta i użytkownik nie jest zalogowany
        if ($userId === null) {
            $authorName = "Gość";
        } else {
            $authorName = $_SESSION['username'];
        }

        if ($this->commentModel->create((int)$postId, $content, $userId, $authorName)) {
            header('Location: ' . BASE_PATH . '/posts/' . $postId . '?status=comment_added#comments');
            exit();
        } else {
            header('Location: ' . BASE_PATH . '/posts/' . $postId . '?status=comment_error');
            exit();
        }
    }


    // Wyświetla formularz kontaktowy do autora konkretnego posta
    public function showPostContactForm(array $params)
    {
        $id = $params['id'] ?? null;
        $post = $this->postModel->getPostById($id);

        if (!$post) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        view('contact/contact', [
            'pageTitle' => 'Kontakt z autorem: ' . htmlspecialchars($post['username']),
            'post' => $post
        ]);
    }

    // Przetwarza formularz kontaktowy i wysyła e-mail do autora posta
    public function handlePostContactForm(array $params)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $id = $params['id'] ?? null;
        $post = $this->postModel->getPostById($id);

        if (!$post) {
            header('Location: ' . BASE_PATH . '/');
            exit();
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $message = $_POST['message'] ?? '';
        $subject = "Wiadomość dotycząca Twojego posta: '" . $post['title'] . "'";

        // Walidacja
        $error = null;
        if (empty($name) || empty($email) || empty($message)) {
            $error = 'Pola Imię, E-mail i Wiadomość są wymagane.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Proszę podać poprawny adres e-mail.';
        }

        if ($error) {
            view('contact/contact', ['pageTitle' => 'Kontakt z autorem', 'error' => $error, 'post' => $post, 'post_data' => $_POST]);
            return;
        }

        // Zapis do pliku logu
        $logFile = LOG_DIR . 'contact_messages.log';
        $logEntry = "[" . date('Y-m-d H:i:s') . "] To: " . $post['author_email'] . " | From: $name <$email>\nSubject: $subject\nMessage: $message\n---\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND);

        // Wysłanie e-maila
        $mailer = new Mailer();
        $emailSent = $mailer->sendEmail($post['author_email'], $subject, "Wiadomość od: $name ($email)<br><br>" . nl2br(htmlspecialchars($message)));

        if ($emailSent) {
            $success = 'Wiadomość do autora została wysłana!';
            view('contact/contact', ['pageTitle' => 'Wiadomość wysłana', 'success' => $success, 'post' => $post]);
        } else {
            $error = 'Wystąpił błąd podczas wysyłania wiadomości.';
            view('contact/contact', ['pageTitle' => 'Kontakt z autorem', 'error' => $error, 'post' => $post, 'post_data' => $_POST]);
        }
    }
}
