<?php
require_once __DIR__ . '/../includes/auth.php';
$user = requireRole($pdo, 'admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfOrFail($_POST['csrf_token'] ?? null);

    if (isset($_POST['toggle'], $_POST['id'])) {
        $id = (int)$_POST['id'];
        $targetStatus = ($_POST['toggle'] === 'ban') ? 'banned' : 'active';
        $stmt = $pdo->prepare("UPDATE users SET status=? WHERE id=? AND role IN ('buyer','developer')");
        $stmt->execute([$targetStatus, $id]);
        pushToast('success', $targetStatus === 'banned' ? 'User banned successfully.' : 'User unbanned successfully.');
        header('Location: /admin/users');
        exit;
    }

    if (isset($_POST['edit_user_id'])) {
        $editId = (int)$_POST['edit_user_id'];
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $support = trim($_POST['support_number'] ?? '');
        $status = ($_POST['status'] ?? 'active') === 'banned' ? 'banned' : 'active';

        if ($username && $email && $support) {
            $stmt = $pdo->prepare('UPDATE users SET username=?, email=?, support_number=?, status=? WHERE id=?');
            $stmt->execute([$username, $email, $support, $status, $editId]);
            pushToast('success', 'User profile updated successfully.');
        } else {
            pushToast('error', 'All user fields are required.');
        }
        header('Location: /admin/users');
        exit;
    }

    if (isset($_POST['create_user'])) {
        $username = trim($_POST['new_username'] ?? '');
        $email = trim($_POST['new_email'] ?? '');
        $password = $_POST['new_password'] ?? '';
        $support = trim($_POST['new_support_number'] ?? '');
        $roleInput = $_POST['new_role'] ?? 'buyer';
        $role = in_array($roleInput, ['buyer', 'developer', 'admin'], true) ? $roleInput : 'buyer';

        if (!$username || !$email || !$password || !$support) {
            pushToast('error', 'All create-user fields are required.');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            pushToast('error', 'Invalid email format.');
        } else {
            $exists = $pdo->prepare('SELECT id FROM users WHERE email=? OR username=? LIMIT 1');
            $exists->execute([$email, $username]);
            if ($exists->fetch()) {
                pushToast('error', 'Username or email already exists.');
            } else {
                $ins = $pdo->prepare('INSERT INTO users (username,email,password,support_number,role,status) VALUES (?,?,?,?,?,"active")');
                $ins->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT), $support, $role]);
                pushToast('success', strtoupper($role) . ' account created successfully.');
            }
        }
        header('Location: /admin/users');
        exit;
    }

    if (isset($_POST['add_ip'])) {
        $ip = trim($_POST['ip_address'] ?? '');
        $label = trim($_POST['ip_label'] ?? '');
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            pushToast('error', 'Invalid IP address.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO admin_ip_whitelist (ip_address,label,status) VALUES (?,?,"active") ON DUPLICATE KEY UPDATE label=VALUES(label), status="active"');
            $stmt->execute([$ip, $label ?: null]);
            pushToast('success', 'Admin whitelist IP saved.');
        }
        header('Location: /admin/users');
        exit;
    }

    if (isset($_POST['remove_ip_id'])) {
        $id = (int)$_POST['remove_ip_id'];
        $stmt = $pdo->prepare('DELETE FROM admin_ip_whitelist WHERE id=?');
        $stmt->execute([$id]);
        pushToast('success', 'Whitelist IP removed.');
        header('Location: /admin/users');
        exit;
    }
}

$rows = $pdo->query("SELECT id, username, email, support_number, role, status, created_at FROM users ORDER BY id DESC")->fetchAll();
$ips = $pdo->query('SELECT id, ip_address, label, status, created_at FROM admin_ip_whitelist ORDER BY id DESC')->fetchAll();
$currentIp = getClientIp();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Users</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100">
  <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-5">
    <div class="mb-1 flex justify-between">
      <h1 class="text-3xl font-black">Manage Users</h1>
      <a class="text-blue-600" href="/admin/dashboard">Back</a>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
      <section class="rounded-xl border bg-white p-4">
        <h2 class="mb-2 text-xl font-bold">Add New User/Admin</h2>
        <form method="post" class="grid gap-2 sm:grid-cols-2">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
          <input type="hidden" name="create_user" value="1">
          <input name="new_username" placeholder="Username" class="rounded border p-2" required>
          <input name="new_email" type="email" placeholder="Email" class="rounded border p-2" required>
          <input name="new_password" type="password" placeholder="Password" class="rounded border p-2" required>
          <input name="new_support_number" placeholder="Support Number" class="rounded border p-2" required>
          <select name="new_role" class="rounded border p-2">
            <option value="buyer">Buyer</option>
            <option value="developer">Developer</option>
            <option value="admin">Admin</option>
          </select>
          <button class="rounded bg-blue-600 p-2 text-white">Create Account</button>
        </form>
      </section>

      <section class="rounded-xl border bg-white p-4">
        <h2 class="mb-2 text-xl font-bold">Admin IP Whitelist</h2>
        <p class="mb-2 text-sm text-slate-500">Current IP: <b><?= htmlspecialchars($currentIp) ?></b></p>
        <form method="post" class="grid gap-2 sm:grid-cols-3">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
          <input type="hidden" name="add_ip" value="1">
          <input name="ip_address" placeholder="IP address" class="rounded border p-2" required>
          <input name="ip_label" placeholder="Label (optional)" class="rounded border p-2">
          <button class="rounded bg-emerald-600 p-2 text-white">Add / Activate</button>
        </form>
        <div class="mt-3 overflow-x-auto">
          <table class="w-full min-w-[420px] text-sm">
            <thead><tr class="bg-slate-50"><th class="p-2 text-left">IP</th><th>Label</th><th>Added</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($ips as $ip): ?>
              <tr class="border-t">
                <td class="p-2 font-mono"><?= htmlspecialchars($ip['ip_address']) ?></td>
                <td><?= htmlspecialchars((string)$ip['label']) ?></td>
                <td><?= htmlspecialchars($ip['created_at']) ?></td>
                <td>
                  <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                    <input type="hidden" name="remove_ip_id" value="<?= (int)$ip['id'] ?>">
                    <button class="text-red-600">Remove</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    </div>

    <div class="bg-white border rounded-xl overflow-x-auto">
      <table class="w-full min-w-[980px] text-sm">
        <thead>
          <tr class="bg-slate-50">
            <th class="p-3 text-left">Username</th>
            <th>Email</th>
            <th>Support</th>
            <th>Role</th>
            <th>Status</th>
            <th>Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr class="border-t align-top">
              <td class="p-3"><?= htmlspecialchars($r['username']) ?></td>
              <td><?= htmlspecialchars($r['email']) ?></td>
              <td><?= htmlspecialchars($r['support_number']) ?></td>
              <td><?= htmlspecialchars($r['role']) ?></td>
              <td><?= htmlspecialchars($r['status']) ?></td>
              <td><?= htmlspecialchars($r['created_at']) ?></td>
              <td class="space-y-2 p-2">
                <?php if ($r['role'] !== 'admin'): ?>
                  <form method="post" class="inline">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <?php if ($r['status'] === 'active'): ?>
                      <input type="hidden" name="toggle" value="ban">
                      <button class="rounded bg-red-100 px-2 py-1 text-red-700" type="submit">Ban</button>
                    <?php else: ?>
                      <input type="hidden" name="toggle" value="unban">
                      <button class="rounded bg-green-100 px-2 py-1 text-green-700" type="submit">Unban</button>
                    <?php endif; ?>
                  </form>
                <?php endif; ?>

                <details>
                  <summary class="cursor-pointer text-blue-600">Edit</summary>
                  <form method="post" class="mt-2 space-y-1 rounded border p-2">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                    <input type="hidden" name="edit_user_id" value="<?= (int)$r['id'] ?>">
                    <input name="username" value="<?= htmlspecialchars($r['username']) ?>" class="w-full rounded border p-1" required>
                    <input name="email" type="email" value="<?= htmlspecialchars($r['email']) ?>" class="w-full rounded border p-1" required>
                    <input name="support_number" value="<?= htmlspecialchars($r['support_number']) ?>" class="w-full rounded border p-1" required>
                    <select name="status" class="w-full rounded border p-1">
                      <option value="active" <?= $r['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                      <option value="banned" <?= $r['status'] === 'banned' ? 'selected' : '' ?>>Banned</option>
                    </select>
                    <button class="w-full rounded bg-blue-600 py-1 text-white">Save</button>
                  </form>
                </details>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
<?= renderToastContainer() ?></body>
</html>
