<?php
require_once __DIR__ . '/../config.php';

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}

function currentUser(PDO $pdo): ?array
{
    if (!isLoggedIn()) {
        return null;
    }

    $stmt = $pdo->prepare('SELECT id, name, email, role FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function requireDeveloper(PDO $pdo): void
{
    requireLogin();
    $user = currentUser($pdo);
    if (!$user || $user['role'] !== 'developer') {
        header('Location: /index.php?error=dev_only');
        exit;
    }
}
