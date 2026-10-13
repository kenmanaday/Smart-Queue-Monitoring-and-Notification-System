<?php
// XAMPP default: username=root, password=''. Change these if your MySQL credentials differ.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'smart_queue_db';

try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS);
    $conn->set_charset('utf8mb4');
    $conn->query("CREATE DATABASE IF NOT EXISTS `$DB_NAME` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conn->select_db($DB_NAME);

    $conn->query("CREATE TABLE IF NOT EXISTS users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(150) NOT NULL,
        student_id VARCHAR(20) NULL UNIQUE,
        email VARCHAR(190) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('student','admin') NOT NULL DEFAULT 'student',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");

    $conn->query("CREATE TABLE IF NOT EXISTS registrar_windows (
        id TINYINT UNSIGNED PRIMARY KEY,
        is_open TINYINT(1) NOT NULL DEFAULT 1,
        serving_queue_id INT UNSIGNED NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");

    $conn->query("CREATE TABLE IF NOT EXISTS queue_numbers (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        queue_number INT UNSIGNED NOT NULL UNIQUE,
        user_id INT UNSIGNED NOT NULL,
        window_id TINYINT UNSIGNED NULL,
        status ENUM('Waiting','Processing','Completed','Cancelled') NOT NULL DEFAULT 'Waiting',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        completed_at TIMESTAMP NULL DEFAULT NULL,
        INDEX idx_queue_status_number (status, queue_number),
        INDEX idx_user_status (user_id, status),
        CONSTRAINT fk_queue_user FOREIGN KEY (user_id) REFERENCES users(id),
        CONSTRAINT fk_queue_window FOREIGN KEY (window_id) REFERENCES registrar_windows(id)
    ) ENGINE=InnoDB");

    $conn->query("CREATE TABLE IF NOT EXISTS notifications (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        message VARCHAR(500) NOT NULL,
        is_read TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_notification_user (user_id, is_read),
        CONSTRAINT fk_notification_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");

    // Initialize the three registrar windows once. Existing window settings are preserved.
    $stmt = $conn->prepare('INSERT IGNORE INTO registrar_windows (id, is_open) VALUES (1,1),(2,1),(3,1)');
    $stmt->execute();
} catch (Throwable $e) {
    http_response_code(500);
    exit('Database connection/setup failed. Check MySQL is running and db.php credentials are correct.');
}
