<?php
require_once __DIR__ . '/../includes/auth.php';
if (isLoggedIn()) {
    redirectByRole(currentUser($pdo));
}

$error = null;
$ipAddress = getClientIp();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identity = trim($_POST['identity'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!isAdminIpAllowed($pdo, $ipAddress)) {
        $error = 'Your IP is not whitelisted for admin login. Contact super admin.';
    } else {
        $stmt = $pdo->prepare('SELECT id, username, email, password, role, status FROM users WHERE (email = ? OR username = ?) AND role="admin" LIMIT 1');
        $stmt->execute([$identity, $identity]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            if ($admin['status'] === 'banned') {
                $error = 'Admin account is blocked.';
            } else {
                $_SESSION['user_id'] = $admin['id'];
                pushToast('success', 'Admin login successful.');
                header('Location: /admin/dashboard');
                exit;
            }
        } else {
            $error = 'Invalid admin credentials.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Login | ScriptDeploy</title>
  <meta name="description" content="Secure admin login with IP whitelist protection.">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-black to-slate-900 p-4 text-white">
  <div class="mx-auto flex min-h-screen max-w-4xl items-center justify-center">
    <div class="grid w-full overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/85 shadow-2xl md:grid-cols-2">
      <section class="hidden bg-black/60 p-8 md:block">
        <p class="mb-3 inline-flex rounded-full border border-red-600/40 bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-300">Restricted Zone</p>
        <h1 class="text-3xl font-black">Login</h1>
        <p class="mt-3 text-slate-300">Only whitelisted IP addresses can access this panel.</p>
        <p class="mt-2 text-xs text-slate-400">Current IP: <?= htmlspecialchars($ipAddress) ?></p>
      </section>
      <section class="p-8">
        <h2 class="mb-1 text-2xl font-bold">Login</h2>
        <p class="mb-5 text-sm text-slate-400">Authorized administrators only.</p>
        <?php if ($error): ?><p class="mb-3 rounded bg-red-100 p-2 text-sm text-red-700"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <form method="post" class="space-y-3">
          <input name="identity" placeholder="Admin Email or Username" class="w-full rounded-lg border border-slate-700 bg-slate-950 p-3" required>
          <input name="password" type="password" placeholder="Password" class="w-full rounded-lg border border-slate-700 bg-slate-950 p-3" required>
          <button class="w-full rounded-lg bg-red-600 p-3 font-semibold text-white hover:bg-red-500">Login</button>
        </form>
        <p class="mt-4 text-sm text-slate-500">Other portals: <a class="text-blue-400" href="/login">Login</a> · <a class="text-cyan-400" href="/dev/login">Login</a></p>
      </section>
    </div>
  </div>
<?= renderToastContainer() ?></body>
</html>
