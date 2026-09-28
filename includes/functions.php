<?php
function e(?string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function inr(float $n): string {
    return '₹' . number_format($n, 0, '.', ',');
}

function monthName(int $m): string {
    return date('F', mktime(0, 0, 0, $m, 1));
}

function monthlyTotal(PDO $pdo, int $userId, int $m, int $y): float {
    $stmt = $pdo->prepare(
        "SELECT COALESCE(SUM(amount),0) FROM expenses
         WHERE user_id=? AND MONTH(expense_date)=? AND YEAR(expense_date)=?"
    );
    $stmt->execute([$userId, $m, $y]);
    return (float)$stmt->fetchColumn();
}

function monthlyBudget(PDO $pdo, int $userId, int $m, int $y): float {
    $stmt = $pdo->prepare(
        "SELECT amount FROM budgets
         WHERE user_id=? AND budget_month=? AND budget_year=?"
    );
    $stmt->execute([$userId, $m, $y]);
    return (float)($stmt->fetchColumn() ?: 0);
}