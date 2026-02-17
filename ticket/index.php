<?php
require_once __DIR__ . '/../includes/auth.php';
$user = requireLogin($pdo);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($subject && $message) {
        $stmt = $pdo->prepare('INSERT INTO tickets (user_id, role, subject, message, status, admin_reply) VALUES (?, ?, ?, ?, "open", NULL)');
        $stmt->execute([$user['id'], $user['role'], $subject, $message]);
    }
}
$list = $pdo->prepare('SELECT * FROM tickets WHERE user_id=? ORDER BY id DESC');
$list->execute([$user['id']]);
$tickets = $list->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Ticket</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-100"><div class="max-w-5xl mx-auto p-4 sm:p-6"><h1 class="text-3xl font-bold mb-4">Support Tickets</h1>
<form method="post" class="bg-white border rounded-xl p-4 grid gap-2 mb-4"><input name="subject" class="border rounded p-2" placeholder="Subject" required><textarea name="message" class="border rounded p-2" placeholder="Message" required></textarea><button class="bg-blue-600 text-white rounded p-2">Create Ticket</button></form>
<div class="space-y-3"><?php foreach($tickets as $t): ?><div class="bg-white border rounded-xl p-3"><p class="font-semibold"><?= htmlspecialchars($t['subject']) ?></p><p><?= htmlspecialchars($t['message']) ?></p><p class="text-sm text-slate-500">Status: <?= htmlspecialchars($t['status']) ?></p><p class="text-sm">Admin Reply: <?= htmlspecialchars($t['admin_reply'] ?? 'Pending') ?></p></div><?php endforeach; ?></div>
</div><?= renderToastContainer() ?></body></html>
