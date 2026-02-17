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
        } elseif ($user['role'] === 'admin') {
            $error = 'Admin login is available only at /admin/login.';
        } elseif ($user['role'] !== 'buyer') {
            $error = 'Developer login is available only at /dev/login.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            redirectByRole($user);
        }
    } else {
        $error = 'Invalid credentials.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Login | ScriptDeploy</title>
  <meta name="description" content="Login to ScriptDeploy using email or username and password.">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-100 via-white to-blue-100 p-4">
  <div class="mx-auto flex min-h-screen max-w-5xl items-center justify-center">
    <div class="grid w-full overflow-hidden rounded-2xl border bg-white shadow-xl md:grid-cols-2">
      <section class="hidden bg-slate-900 p-8 text-white md:block">
        <h1 class="text-3xl font-black">Login</h1>
        <p class="mt-3 text-slate-300">Access your buyer workspace securely. Developers and admins use dedicated portals.</p>
      </section>
      <section class="p-8">
        <h2 class="mb-1 text-2xl font-bold">Login</h2>
        <p class="mb-5 text-sm text-slate-500">Use email or username + password.</p>
        <?php if ($error): ?><p class="mb-3 rounded bg-red-100 p-2 text-sm text-red-700"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <form method="post" class="space-y-3">
          <input name="identity" placeholder="Email or Username" class="w-full rounded-lg border p-3" required>
          <input name="password" type="password" placeholder="Password" class="w-full rounded-lg border p-3" required>
          <button class="w-full rounded-lg bg-blue-600 p-3 font-semibold text-white hover:bg-blue-700">Login</button>
        </form>
        <p class="mt-4 text-sm text-slate-600">No account? <a class="font-medium text-blue-600" href="/register">Register</a></p>
        <p class="mt-2 text-sm text-slate-600">Need another portal? <a class="font-medium text-cyan-600" href="/dev/login">Login</a></p>
        <p class="mt-2 text-sm text-slate-600">Need secure portal? <a class="font-medium text-red-600" href="/admin/login">Login</a></p>
      </section>
    </div>
  </div>
<?= renderToastContainer() ?></body>
</html>
