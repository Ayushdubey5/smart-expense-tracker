<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole('student');
verifyCsrf($_GET['csrf'] ?? null);

$id = (int)($_GET['id'] ?? 0);
$pdo->prepare("DELETE FROM expenses WHERE expense_id=? AND user_id=?")
    ->execute([$id, currentUserId()]);
header('Location: expenses.php?msg=Expense+deleted');