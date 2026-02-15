<?php
require_once __DIR__ . '/../includes/auth.php';
$user = requireRole($pdo, 'admin');

if (isset($_GET['toggle']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $t = ($_GET['toggle'] === 'ban') ? 'banned' : 'active';
    $stmt = $pdo->prepare("UPDATE users SET status=? WHERE id=? AND role IN ('buyer','developer')");
    $stmt->execute([$t, $id]);
    header('Location: /admin/users');
    exit;
}

$rows = $pdo->query("SELECT id, username, email, role, status, created_at FROM users ORDER BY id DESC")->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin Users</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-slate-100"><div class="max-w-7xl mx-auto p-4 sm:p-6"><div class="mb-4 flex justify-between"><h1 class="text-3xl font-black">Manage Users</h1><a class="text-blue-600" href="/admin/dashboard">Back</a></div><div class="bg-white border rounded-xl overflow-x-auto"><table class="w-full min-w-[760px] text-sm"><thead><tr class="bg-slate-50"><th class="p-3 text-left">Username</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th><th>Action</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr class="border-t"><td class="p-3"><?=htmlspecialchars($r['username'])?></td><td><?=htmlspecialchars($r['email'])?></td><td><?=htmlspecialchars($r['role'])?></td><td><?=htmlspecialchars($r['status'])?></td><td><?=htmlspecialchars($r['created_at'])?></td><td><?php if($r['role']!=='admin'): ?><?php if($r['status']==='active'): ?><a class="text-red-600" href="/admin/users?toggle=ban&id=<?=$r['id']?>">Ban</a><?php else: ?><a class="text-green-600" href="/admin/users?toggle=unban&id=<?=$r['id']?>">Unban</a><?php endif; ?><?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div></div></body></html>
