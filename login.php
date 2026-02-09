<?php
require_once __DIR__ . '/includes/auth.php';

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, password FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: /index.php');
        exit;
    }

    $error = 'Invalid credentials.';
}
?>
<!doctype html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><script src="https://cdn.tailwindcss.com"></script><title>Login</title></head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
<form method="post" class="bg-white p-8 rounded-xl shadow w-full max-w-md space-y-4">
  <h1 class="text-2xl font-bold">Login</h1>
  <?php if ($error): ?><p class="text-red-600 text-sm"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <input name="email" type="email" placeholder="Email" class="w-full border rounded p-2" required>
  <input name="password" type="password" placeholder="Password" class="w-full border rounded p-2" required>
  <button class="w-full bg-blue-600 text-white rounded p-2">Login</button>
  <p class="text-sm">No account? <a class="text-blue-600" href="/register.php">Register</a></p>
</form>
</body></html>
