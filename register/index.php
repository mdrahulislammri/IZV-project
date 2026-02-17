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
            $insert->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT), $support, 'buyer', 'active']);
            pushToast('success', 'Registration complete. Please login.');
            header('Location: /login');
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
  <meta name="description" content="Create a buyer account on ScriptDeploy.">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-100 via-white to-purple-100 p-4">
  <div class="mx-auto flex min-h-screen max-w-5xl items-center justify-center">
    <div class="grid w-full overflow-hidden rounded-2xl border bg-white shadow-xl md:grid-cols-2">
      <section class="hidden bg-slate-900 p-8 text-white md:block">
        <h1 class="text-3xl font-black">Create Account</h1>
        <p class="mt-3 text-slate-300">Create your buyer account. Developers have a dedicated registration portal.</p>
      </section>
      <section class="p-8">
        <h2 class="mb-1 text-2xl font-bold">Register</h2>
        <p class="mb-5 text-sm text-slate-500">All fields are required.</p>
        <?php if ($error): ?><p class="mb-3 rounded bg-red-100 p-2 text-sm text-red-700"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <form method="post" class="space-y-3">
          <input name="username" placeholder="Username" class="w-full rounded-lg border p-3" required>
          <input name="email" type="email" placeholder="Email" class="w-full rounded-lg border p-3" required>
          <input name="password" type="password" placeholder="Password" class="w-full rounded-lg border p-3" required>
          <input name="support_number" placeholder="Support Number" class="w-full rounded-lg border p-3" required>
          <button class="w-full rounded-lg bg-blue-600 p-3 font-semibold text-white hover:bg-blue-700">Create Account</button>
        </form>
        <p class="mt-4 text-sm text-slate-600">Already have account? <a class="font-medium text-blue-600" href="/login">Login</a></p>
        <p class="mt-2 text-sm text-slate-600">Need another portal? <a class="font-medium text-indigo-600" href="/dev/register">Register</a></p>
      </section>
    </div>
  </div>
<?= renderToastContainer() ?></body>
</html>
