<?php
require_once __DIR__ . '/includes/auth.php';

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = ($_POST['role'] ?? 'buyer') === 'developer' ? 'developer' : 'buyer';

    if (!$name || !$email || !$password) {
        $error = 'All fields are required.';
    } else {
        $exists = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $exists->execute([$email]);
        if ($exists->fetch()) {
            $error = 'Email already exists.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
            header('Location: /login.php?registered=1');
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><script src="https://cdn.tailwindcss.com"></script><title>Register</title></head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
<form method="post" class="bg-white p-8 rounded-xl shadow w-full max-w-md space-y-4">
  <h1 class="text-2xl font-bold">Register</h1>
  <?php if ($error): ?><p class="text-red-600 text-sm"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <input name="name" placeholder="Name" class="w-full border rounded p-2" required>
  <input name="email" type="email" placeholder="Email" class="w-full border rounded p-2" required>
  <input name="password" type="password" placeholder="Password" class="w-full border rounded p-2" required>
  <select name="role" class="w-full border rounded p-2">
    <option value="buyer">Buyer</option>
    <option value="developer">Developer</option>
  </select>
  <button class="w-full bg-blue-600 text-white rounded p-2">Create Account</button>
  <p class="text-sm">Already have account? <a class="text-blue-600" href="/login.php">Login</a></p>
</form>
</body></html>
