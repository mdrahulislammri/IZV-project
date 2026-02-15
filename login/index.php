<?php
require_once __DIR__ . '/../includes/auth.php';
if (isLoggedIn()) {
    redirectByRole(currentUser($pdo));
}
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identity = trim($_POST['identity'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, username, email, password, role, status FROM users WHERE email = ? OR username = ?');
    $stmt->execute([$identity, $identity]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['status'] === 'banned') {
            $error = 'Account is banned due to warn policy.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            redirectByRole($user);
        }
    } else {
        $error = 'Invalid credentials.';
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Login</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4"><form method="post" class="bg-white p-8 rounded-xl border shadow w-full max-w-md space-y-3">
<h1 class="text-2xl font-bold">Login</h1>
<?php if (isset($_GET['registered'])): ?><p class="text-green-700 text-sm">Registration complete. Please login.</p><?php endif; ?>
<?php if ($error): ?><p class="text-red-600 text-sm"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<input name="identity" placeholder="Email or Username" class="w-full border rounded p-2" required>
<input name="password" type="password" placeholder="Password" class="w-full border rounded p-2" required>
<button class="w-full bg-blue-600 text-white rounded p-2">Login</button>
<p class="text-sm">No account? <a class="text-blue-600" href="/register">Register</a></p>
</form></body></html>
