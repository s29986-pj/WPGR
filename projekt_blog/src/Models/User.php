<?php

namespace App\Models;

use App\Core\Database;

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Tworzy nowego użytkownika w bazie danych
    public function create(string $username, string $email, string $passwordHash, ?string $verificationToken = null, string $role = 'user')
    {
        // Prepared statement dla ochrony przed SQL Injection
        // Znak '?' jest zastępowany przez wartości, które są przekazywane oddzielnie.
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password_hash, role, verification_token) VALUES (?, ?, ?, ?, ?)");
        
        // 'sssss' oznacza, że wszystkie pięć parametrów to stringi.
        $stmt->bind_param("sssss", $username, $email, $passwordHash, $role, $verificationToken);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    // Znajduje użytkownika po adresie e-mail
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT id, username, email, password_hash, role, is_active FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Znajduje użytkownika po nazwie
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("SELECT id, username, email, password_hash, role, is_active FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Znajduje użytkownika po jego ID
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT id, username, email, password_hash, role, is_active FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Pobiera wszystkich użytkowników z bazy z paginacją
    public function getAllUsers(int $currentAdminId, int $limit = 10, int $offset = 0): array
    {
        $stmt = $this->db->prepare("SELECT id, username, email, role, is_active, created_at FROM users WHERE id != ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("iii", $currentAdminId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Zlicza wszystkich użytkowników (oprócz admina)
    public function countAllUsers(int $currentAdminId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(id) as total FROM users WHERE id != ?");
        $stmt->bind_param("i", $currentAdminId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['total'];
    }

    // Zmienia rolę użytkownika
    public function updateUserRole(int $userId, string $newRole): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $newRole, $userId);
        return $stmt->execute();
    }

    // Usuwa użytkownika z bazy danych
    public function deleteUser(int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }

    // Znajduje użytkownika po tokenie weryfikacyjnym
    public function findByVerificationToken(string $token): ?array
    {
        $stmt = $this->db->prepare("SELECT id, username, email FROM users WHERE verification_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Aktywuje konto użytkownika (ustawia is_active na true i czyści token weryfikacyjny)
    public function activateUser(int $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET is_active = TRUE, verification_token = NULL WHERE id = ?");
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }

    // Aktualizuje token resetowania hasła i czas jego ważności dla użytkownika
    public function updateResetToken(int $userId, string $token, string $expiry): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
        $stmt->bind_param("ssi", $token, $expiry, $userId); // 'ssi' -> string, string, integer
        return $stmt->execute();
    }

    // Znajduje użytkownika po tokenie resetowania hasła i sprawdza jego ważność
    public function findByResetToken(string $token): ?array
    {
        $stmt = $this->db->prepare("SELECT id, email FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Aktualizuje hasło użytkownika po pomyślnym zresetowaniu
    public function updatePassword(int $userId, string $newPasswordHash): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
        $stmt->bind_param("si", $newPasswordHash, $userId);
        return $stmt->execute();
    }
}
