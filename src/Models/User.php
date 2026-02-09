<?php
namespace App\Models;

use PDO;

class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Find user by username
    public function findByUsername($username) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username AND is_deleted = 0 LIMIT 1");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }

    // Create a new user
    public function create($username, $password, $role, $fullname = null, $profileImage = null) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password, role, fullname, profile_image) VALUES (:username, :password, :role, :fullname, :image)");
        return $stmt->execute([
            'username' => $username,
            'password' => $hash,
            'role' => $role,
            'fullname' => $fullname,
            'image' => $profileImage
        ]);
    }

    // List all users
    public function getAll() {
        $stmt = $this->pdo->query("SELECT id, username, role, fullname, profile_image, is_active, created_at FROM users WHERE is_deleted = 0 ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id AND is_deleted = 0");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("UPDATE users SET is_deleted = 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function updatePassword($id, $newPassword) {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        return $stmt->execute(['password' => $hash, 'id' => $id]);
    }
    public function updateProfile($id, $fullname, $profileImage) {
        if ($profileImage) {
            $stmt = $this->pdo->prepare("UPDATE users SET fullname = :fullname, profile_image = :image WHERE id = :id");
            return $stmt->execute(['fullname' => $fullname, 'image' => $profileImage, 'id' => $id]);
        } else {
            $stmt = $this->pdo->prepare("UPDATE users SET fullname = :fullname WHERE id = :id");
            return $stmt->execute(['fullname' => $fullname, 'id' => $id]);
        }
    }
    public function updateStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE users SET is_active = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public function updateRole($id, $role) {
        $stmt = $this->pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
        return $stmt->execute(['role' => $role, 'id' => $id]);
    }

    public function getDeleted() {
        $stmt = $this->pdo->query("SELECT * FROM users WHERE is_deleted = 1 ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function restore($id) {
        $stmt = $this->pdo->prepare("UPDATE users SET is_deleted = 0 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function hardDelete($id) {
        // Block if user has recorded sales
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM sales WHERE user_id = :id");
        $stmt->execute(['id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            throw new \Exception("Cannot delete user who has recorded sales.");
        }

        // Block if user has recorded expenditures
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM expenditures WHERE recorded_by = :id");
        $stmt->execute(['id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            throw new \Exception("Cannot delete user who has recorded expenditures.");
        }

        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
