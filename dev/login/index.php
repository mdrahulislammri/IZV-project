<?php
require_once __DIR__ . '/../../includes/auth.php';
if (isLoggedIn()) {
    redirectByRole(currentUser($pdo));
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identity = trim($_POST['identity'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, username, email, password, role, status FROM users WHERE email = ? OR username = ?');
    $stmt->execute([$identity, $identity]);
    $account = $stmt->fetch();

    if ($account && password_verify($password, $account['password'])) {
        if ($account['status'] === 'banned') {
            $error = 'Account is banned due to warn policy.';
        } elseif ($account['role'] !== 'developer') {
            $error = 'This login is only for developer accounts.';
        } else {
            $_SESSION['user_id'] = $account['id'];
            pushToast('success', 'Welcome back, developer.');
            header('Location: /dev/dashboard');
            exit;
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
  <title>Developer Login | ScriptDeploy</title>
  <meta name="description" content="Developer login portal for ScriptDeploy.">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-950 to-blue-950 p-4 text-white">
  <div class="mx-auto flex min-h-screen max-w-5xl items-center justify-center">
    <div class="grid w-full overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/80 shadow-2xl md:grid-cols-2">
      <section class="hidden bg-slate-950 p-8 md:block">
        <p class="mb-3 inline-flex rounded-full border border-cyan-600/40 bg-cyan-500/10 px-3 py-1 text-xs font-semibold text-cyan-300">Developer Portal</p>
        <h1 class="text-3xl font-black">Developer Login</h1>
        <p class="mt-3 text-slate-300">Manage scripts, packages, wallet and clients from your dedicated panel.</p>
      </section>
      <section class="p-8">
        <h2 class="mb-1 text-2xl font-bold">Sign in as Developer</h2>
        <p class="mb-5 text-sm text-slate-400">Use your developer email/username and password.</p>
        <?php if ($error): ?><p class="mb-3 rounded bg-red-100 p-2 text-sm text-red-700"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <form method="post" class="space-y-3">
          <input name="identity" placeholder="Email or Username" class="w-full rounded-lg border border-slate-700 bg-slate-950 p-3" required>
          <input name="password" type="password" placeholder="Password" class="w-full rounded-lg border border-slate-700 bg-slate-950 p-3" required>
          <button class="w-full rounded-lg bg-cyan-600 p-3 font-semibold text-white hover:bg-cyan-500">Developer Login</button>
        </form>
        <p class="mt-4 text-sm text-slate-400">New developer? <a class="font-medium text-cyan-400" href="/dev/register">Create developer account</a></p>
        <p class="mt-2 text-sm text-slate-500">Buyer/Admin? <a class="text-blue-400" href="/login">Go to general login</a></p>
      </section>
    </div>
  </div>
<?= renderToastContainer() ?></body>
</html>
