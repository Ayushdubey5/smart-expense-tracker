<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: /smart-expense-tracker/auth/login.php');
        exit;
    }
}

function requireRole(string $role): void {
    requireLogin();
    if (($_SESSION['role'] ?? '') !== $role) {
        http_response_code(403);
        die('Access denied.');
    }
}

function currentUserId(): int {
    return (int)($_SESSION['user_id'] ?? 0);
}

function csrfToken(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verifyCsrf(?string $token): void {
    if (!$token || !hash_equals($_SESSION['csrf'] ?? '', $token)) {
        http_response_code(419);
        die('Invalid CSRF token.');
    }
}