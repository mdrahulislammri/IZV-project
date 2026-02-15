<?php
require_once __DIR__ . '/../includes/auth.php';
if (isLoggedIn()) {
    redirectByRole(currentUser($pdo));
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $support = trim($_POST['support_number'] ?? '');
    $role = ($_POST['role'] ?? 'buyer') === 'developer' ? 'developer' : 'buyer';

    if (!$username || !$email || !$password || !$support) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? OR username = ?');
        $stmt->execute([$email, $username]);
        if ($stmt->fetch()) {
            $error = 'Username or email already used.';
        } else {
            $insert = $pdo->prepare('INSERT INTO users (username, email, password, support_number, role, status) VALUES (?, ?, ?, ?, ?, ?)');
            $insert->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT), $support, $role, 'active']);
            header('Location: /login?registered=1');
            exit;
        }
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Register</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4"><form method="post" class="bg-white p-8 rounded-xl border shadow w-full max-w-md space-y-3">
<h1 class="text-2xl font-bold">Register</h1>
<?php if ($error): ?><p class="text-red-600 text-sm"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<input name="username" placeholder="Username" class="w-full border rounded p-2" required>
<input name="email" type="email" placeholder="Email" class="w-full border rounded p-2" required>
<input name="password" type="password" placeholder="Password" class="w-full border rounded p-2" required>
<input name="support_number" placeholder="Support Number" class="w-full border rounded p-2" required>
<select name="role" class="w-full border rounded p-2"><option value="buyer">Buyer</option><option value="developer">Developer</option></select>
<button class="w-full bg-blue-600 text-white rounded p-2">Create Account</button>
<p class="text-sm">Have account? <a class="text-blue-600" href="/login">Login</a></p>
</form></body></html>
