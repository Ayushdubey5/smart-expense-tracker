<?php
// config/db.php
// Central PDO connection - used everywhere
declare(strict_types=1);

$DB_HOST = 'localhost';
$DB_NAME = 'expense_tracker';
$DB_USER = 'root';
$DB_PASS = '';   // XAMPP default

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed. Please check config/db.php');
}