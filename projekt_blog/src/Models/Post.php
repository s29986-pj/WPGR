<?php

namespace App\Models;

use App\Core\Database;

class Post
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Pobiera wszystkie posty
    public function getAllPosts(int $limit = 10, int $offset = 0): array
    {
        // Zapytanie z JOINem, aby pobrać nazwę użytkownika, który stworzył post
        $stmt = $this->db->prepare("SELECT p.*, u.username FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC); 
    }

    // Pobiera pojedynczy post po ID
    public function getPostById(int $id)
    {
        $stmt = $this->db->prepare("SELECT p.*, u.username FROM posts p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }


    // Dodaje nowy post do bazy danych
    public function createPost(int $userId, string $title, string $content, ?string $imagePath)
    {
        $stmt = $this->db->prepare("INSERT INTO posts (user_id, title, content, image_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $userId, $title, $content, $imagePath);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }


    // Aktualizuje istniejący post
    public function updatePost(int $id, string $title, string $content, ?string $imagePath): bool
    {
        if ($imagePath !== null) {
            $stmt = $this->db->prepare("UPDATE posts SET title = ?, content = ?, image_path = ? WHERE id = ?");
            $stmt->bind_param("sssi", $title, $content, $imagePath, $id);
        } else {
            // Jeśli $imagePath jest null, nie zmienia obrazka w bazie
            $stmt = $this->db->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
            $stmt->bind_param("ssi", $title, $content, $id);
        }
        return $stmt->execute();
    }


    // Usuwa post po ID
    public function deletePost(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }


    // Zlicza wszystkie posty
    public function countAllPosts(): int
    {
        $result = $this->db->query("SELECT COUNT(*) FROM posts");
        return $result->fetch_row()[0]; 
    }


    //  Znajduje następny post na podstawie daty publikacji
    public function getNextPost(string $currentPostDate): ?array
    {
        // Szuka posta z najbliższą datą, ale wcześniejszą niż aktualna
        $stmt = $this->db->prepare(
            "SELECT id FROM posts WHERE created_at < ? ORDER BY created_at DESC LIMIT 1"
        );
        $stmt->bind_param("s", $currentPostDate);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }


    // Znajduje poprzedni post na podstawie daty publikacji
    public function getPreviousPost(string $currentPostDate): ?array
    {
        // Szuka posta z najbliższą datą, ale późniejszą niż aktualna
        $stmt = $this->db->prepare(
            "SELECT id FROM posts WHERE created_at > ? ORDER BY created_at ASC LIMIT 1"
        );
        $stmt->bind_param("s", $currentPostDate);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
