ALTER TABLE users MODIFY COLUMN role ENUM('admin','sales','cashier') NOT NULL DEFAULT 'sales';

CREATE TABLE IF NOT EXISTS payment_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('sale', 'debt_bulk', 'debt_single') NOT NULL,
    reference_id INT NOT NULL COMMENT 'sale_id or customer_id or debtor_id depending on type',
    customer_id INT NULL,
    amount_due DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_by INT NOT NULL,
    cashier_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (cashier_id) REFERENCES users(id) ON DELETE SET NULL
);
