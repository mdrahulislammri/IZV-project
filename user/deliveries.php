<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
$user = currentUser($pdo);

$stmt = $pdo->prepare('SELECT d.id, d.delivery_note, d.delivered_url, d.created_at, s.title, o.payment_txn FROM deliveries d JOIN scripts s ON s.id = d.script_id JOIN orders o ON o.id = d.order_id WHERE d.user_id = ? ORDER BY d.id DESC');
$stmt->execute([$user['id']]);
$deliveries = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><script src="https://cdn.tailwindcss.com"></script><title>My Deliveries</title></head>
<body class="bg-slate-100 min-h-screen">
<div class="max-w-5xl mx-auto p-6">
  <div class="flex justify-between items-center mb-6"><h1 class="text-3xl font-bold">My Website Deliveries</h1><a href="/" class="text-blue-600">Home</a></div>
  <?php if (isset($_GET['success'])): ?><p class="bg-green-100 text-green-700 p-3 rounded mb-4">Payment successful! Website package delivered.</p><?php endif; ?>
  <div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-slate-50"><tr class="text-left"><th class="p-3">Script</th><th>Payment TXN</th><th>Delivery Note</th><th>Package</th><th>Date</th></tr></thead>
      <tbody>
        <?php foreach ($deliveries as $d): ?>
          <tr class="border-t">
            <td class="p-3"><?= htmlspecialchars($d['title']) ?></td>
            <td><?= htmlspecialchars($d['payment_txn']) ?></td>
            <td><?= htmlspecialchars($d['delivery_note']) ?></td>
            <td><a class="text-blue-600" href="<?= htmlspecialchars($d['delivered_url']) ?>">Download</a></td>
            <td><?= htmlspecialchars($d['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body></html>
