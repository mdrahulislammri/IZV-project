<?php
require_once __DIR__ . '/../includes/auth.php';
$user = requireRole($pdo, 'admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle'], $_POST['id'])) {
    verifyCsrfOrFail($_POST['csrf_token'] ?? null);

    $id = (int)$_POST['id'];
    $targetStatus = ($_POST['toggle'] === 'ban') ? 'banned' : 'active';

    $stmt = $pdo->prepare("UPDATE users SET status=? WHERE id=? AND role IN ('buyer','developer')");
    $stmt->execute([$targetStatus, $id]);

    header('Location: /admin/users');
    exit;
}

$rows = $pdo->query("SELECT id, username, email, role, status, created_at FROM users ORDER BY id DESC")->fetchAll();
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
      <table class="w-full min-w-[760px] text-sm">
        <thead>
          <tr class="bg-slate-50">
            <th class="p-3 text-left">Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Created</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr class="border-t">
              <td class="p-3"><?= htmlspecialchars($r['username']) ?></td>
              <td><?= htmlspecialchars($r['email']) ?></td>
              <td><?= htmlspecialchars($r['role']) ?></td>
              <td><?= htmlspecialchars($r['status']) ?></td>
              <td><?= htmlspecialchars($r['created_at']) ?></td>
              <td>
                <?php if ($r['role'] !== 'admin'): ?>
                  <form method="post" class="inline">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <?php if ($r['status'] === 'active'): ?>
                      <input type="hidden" name="toggle" value="ban">
                      <button class="text-red-600" type="submit">Ban</button>
                    <?php else: ?>
                      <input type="hidden" name="toggle" value="unban">
                      <button class="text-green-600" type="submit">Unban</button>
                    <?php endif; ?>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
