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
        }
        header('Location: /admin/users');
        exit;
    }
}

$rows = $pdo->query("SELECT id, username, email, support_number, role, status, created_at FROM users ORDER BY id DESC")->fetchAll();
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
  <div class="max-w-7xl mx-auto p-4 sm:p-6">
    <div class="mb-4 flex justify-between">
      <h1 class="text-3xl font-black">Manage Users</h1>
      <a class="text-blue-600" href="/admin/dashboard">Back</a>
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
                      <option value="active" <?= $r['status']==='active'?'selected':'' ?>>Active</option>
                      <option value="banned" <?= $r['status']==='banned'?'selected':'' ?>>Banned</option>
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
</body>
</html>
