<?php
require_once __DIR__ . '/../../includes/auth.php';
if (isLoggedIn()) {
    redirectByRole(currentUser($pdo));
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $support = trim($_POST['support_number'] ?? '');

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
            $insert->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT), $support, 'developer', 'active']);
            pushToast('success', 'Developer registration complete. Please login.');
            header('Location: /dev/login');
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Register | ScriptDeploy</title>
  <meta name="description" content="Create your developer account on ScriptDeploy.">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-950 to-indigo-950 p-4 text-white">
  <div class="mx-auto flex min-h-screen max-w-5xl items-center justify-center">
    <div class="grid w-full overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/80 shadow-2xl md:grid-cols-2">
      <section class="hidden bg-slate-950 p-8 md:block">
        <p class="mb-3 inline-flex rounded-full border border-indigo-600/40 bg-indigo-500/10 px-3 py-1 text-xs font-semibold text-indigo-300">Developer Onboarding</p>
        <h1 class="text-3xl font-black">Create Account</h1>
        <p class="mt-3 text-slate-300">Launch your own brand storefront and sell scripts professionally.</p>
      </section>
      <section class="p-8">
        <h2 class="mb-1 text-2xl font-bold">Register</h2>
        <p class="mb-5 text-sm text-slate-400">All fields are required.</p>
        <?php if ($error): ?><p class="mb-3 rounded bg-red-100 p-2 text-sm text-red-700"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <form method="post" class="space-y-3">
          <input name="username" placeholder="Username" class="w-full rounded-lg border border-slate-700 bg-slate-950 p-3" required>
          <input name="email" type="email" placeholder="Email" class="w-full rounded-lg border border-slate-700 bg-slate-950 p-3" required>
          <input name="password" type="password" placeholder="Password" class="w-full rounded-lg border border-slate-700 bg-slate-950 p-3" required>
          <input name="support_number" placeholder="Support Number" class="w-full rounded-lg border border-slate-700 bg-slate-950 p-3" required>
          <button class="w-full rounded-lg bg-indigo-600 p-3 font-semibold text-white hover:bg-indigo-500">Create Account</button>
        </form>
        <p class="mt-4 text-sm text-slate-400">Already registered? <a class="font-medium text-indigo-300" href="/dev/login">Login</a></p>
        <p class="mt-2 text-sm text-slate-500">Need another portal? <a class="text-blue-400" href="/register">Register</a></p>
      </section>
    </div>
  </div>
<?= renderToastContainer() ?></body>
</html>
