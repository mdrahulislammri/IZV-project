<?php
require_once __DIR__ . '/../config.php';

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function currentUser(PDO $pdo): ?array
{
    if (!isLoggedIn()) {
        return null;
    }

    $stmt = $pdo->prepare('SELECT id, username, email, support_number, role, status FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function requireLogin(PDO $pdo): array
{
    if (!isLoggedIn()) {
        header('Location: /login');
        exit;
    }

    $user = currentUser($pdo);
    if (!$user || $user['status'] === 'banned') {
        session_destroy();
        header('Location: /login?error=banned');
        exit;
    }

    return $user;
}

function requireRole(PDO $pdo, string $role): array
{
    $user = requireLogin($pdo);
    if ($user['role'] !== $role) {
        header('Location: ' . ($user['role'] === 'developer' ? '/dev/dashboard' : '/user/dashboard'));
        exit;
    }
    return $user;
}

function redirectByRole(array $user): void
{
    header('Location: ' . ($user['role'] === 'developer' ? '/dev/dashboard' : '/user/dashboard'));
    exit;
}
