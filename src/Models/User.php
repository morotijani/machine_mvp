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
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }

    // Create a new user
    public function create($username, $password, $role) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
        return $stmt->execute([
            'username' => $username,
            'password' => $hash,
            'role' => $role
        ]);
    }

    // List all users
    public function getAll() {
        $stmt = $this->pdo->query("SELECT id, username, role, created_at FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
}
