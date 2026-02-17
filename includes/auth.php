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
        pushToast('error', 'Account is banned due to warn policy.');
        session_destroy();
        header('Location: /login');
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

function pushToast(string $type, string $message): void
{
    $allowed = ['success', 'error', 'info', 'warning'];
    if (!in_array($type, $allowed, true)) {
        $type = 'info';
    }

    if (!isset($_SESSION['toasts']) || !is_array($_SESSION['toasts'])) {
        $_SESSION['toasts'] = [];
    }

    $_SESSION['toasts'][] = [
        'type' => $type,
        'message' => trim($message),
    ];
}

function consumeToasts(): array
{
    $toasts = $_SESSION['toasts'] ?? [];
    unset($_SESSION['toasts']);
    return is_array($toasts) ? $toasts : [];
}

function renderToastContainer(): string
{
    $toasts = array_values(array_filter(consumeToasts(), function ($toast) {
        return is_array($toast) && !empty($toast['message']);
    }));

    if (!$toasts) {
        return '';
    }

    $json = json_encode($toasts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return '';
    }

    return '<div id="toast-root" class="fixed right-4 top-4 z-[9999] flex w-[92vw] max-w-sm flex-col gap-2"></div>'
        . '<script>(function(){'
        . 'const data=' . $json . ';'
        . 'const root=document.getElementById("toast-root");'
        . 'if(!root||!Array.isArray(data)){return;}'
        . 'const tone={success:"border-emerald-500 bg-emerald-950/95 text-emerald-100",error:"border-red-500 bg-red-950/95 text-red-100",warning:"border-amber-500 bg-amber-950/95 text-amber-100",info:"border-blue-500 bg-slate-900/95 text-slate-100"};'
        . 'data.forEach((t,idx)=>{'
        . 'const item=document.createElement("div");'
        . 'item.className="pointer-events-auto translate-x-6 opacity-0 transition-all duration-300 rounded-lg border px-3 py-2 text-sm shadow-xl backdrop-blur "+(tone[t.type]||tone.info);'
        . 'item.textContent=t.message;'
        . 'root.appendChild(item);'
        . 'setTimeout(()=>{item.classList.remove("translate-x-6","opacity-0");},50+idx*120);'
        . 'setTimeout(()=>{item.classList.add("translate-x-6","opacity-0");setTimeout(()=>item.remove(),250);},3600+idx*220);'
        . '});'
        . '})();</script>';
}
