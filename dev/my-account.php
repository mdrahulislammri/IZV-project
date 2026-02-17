<?php
require_once __DIR__ . '/../includes/auth.php';
$user = requireRole($pdo, 'developer');
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>My Account</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-slate-100"><div class="max-w-3xl mx-auto p-4 sm:p-6"><h1 class="text-3xl font-bold mb-4">My Account</h1><div class="bg-white border rounded-xl p-4"><p><b>Username:</b> <?=htmlspecialchars($user['username'])?></p><p><b>Email:</b> <?=htmlspecialchars($user['email'])?></p><p><b>Support Number:</b> <?=htmlspecialchars($user['support_number'])?></p><p><b>Role:</b> <?=htmlspecialchars($user['role'])?></p></div></div><?= renderToastContainer() ?></body></html>
