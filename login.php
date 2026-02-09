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
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | DevScript Market</title>
  <meta name="description" content="Login to DevScript Market.">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
<form method="post" class="bg-white p-6 sm:p-8 rounded-2xl shadow w-full max-w-md space-y-4 border">
  <h1 class="text-2xl font-bold">Login</h1>
  <?php if (isset($_GET['registered'])): ?><p class="text-green-700 text-sm">Registration successful. Please login.</p><?php endif; ?>
  <?php if ($error): ?><p class="text-red-600 text-sm"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <input name="email" type="email" placeholder="Email" class="w-full border rounded-lg p-3" required>
  <input name="password" type="password" placeholder="Password" class="w-full border rounded-lg p-3" required>
  <button class="w-full bg-blue-600 text-white rounded-lg p-3 hover:bg-blue-700">Login</button>
  <p class="text-sm text-slate-600">No account? <a class="text-blue-600" href="/register.php">Register</a></p>
</form>
</body></html>
