<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Config\Database;

try {
    $pdo = Database::getInstance();
    
    // Create settings table
    $sql = "CREATE TABLE IF NOT EXISTS settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company_name VARCHAR(100) NOT NULL DEFAULT 'My Company',
        company_address TEXT,
        company_phone VARCHAR(50),
        company_email VARCHAR(100),
        company_logo VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "Settings table created successfully.\n";

    // Insert default row if not exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM settings");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO settings (company_name) VALUES ('My Company')");
        echo "Default settings row inserted.\n";
    }
    
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>
