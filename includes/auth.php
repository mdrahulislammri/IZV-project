<?php
require_once __DIR__ . '/../config.php';

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function dashboardPathByRole(string $role): string
{
    return match ($role) {
        'developer' => '/dev/dashboard',
        'admin' => '/admin/dashboard',
        default => '/user/dashboard',
    };
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfOrFail(?string $token): void
{
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    if (!$token || !$sessionToken || !hash_equals($sessionToken, $token)) {
        http_response_code(419);
        exit('CSRF token mismatch.');
    }
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
        header('Location: ' . dashboardPathByRole($user['role']));
        exit;
    }
    return $user;
}

function redirectByRole(array $user): void
{
    header('Location: ' . dashboardPathByRole($user['role']));
    exit;
}
