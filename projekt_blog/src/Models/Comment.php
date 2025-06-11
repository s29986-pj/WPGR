<?php

namespace App\Models;

use App\Core\Database;

class Comment
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Pobiera wszystkie komentarze dla danego posta.
    public function findByPostId(int $postId): array
    {
        // JOIN z tabelą users, aby pobrać `username` dla zalogowanych autorów
        $stmt = $this->db->prepare(
            "SELECT c.id, c.content, c.created_at, c.author_name, u.username 
             FROM comments c 
             LEFT JOIN users u ON c.user_id = u.id 
             WHERE c.post_id = ? 
             ORDER BY c.created_at ASC"
        );
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* Tworzy nowy komentarz w bazie danych.
     * ?int $userId ID użytkownika (jeśli zalogowany)
     * ?string $authorName Nazwa autora (jeśli gość)
     */

    public function create(int $postId, string $content, ?int $userId, ?string $authorName): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO comments (post_id, content, user_id, author_name) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("isis", $postId, $content, $userId, $authorName);
        return $stmt->execute();
    }
}
