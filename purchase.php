<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
$user = currentUser($pdo);
$scriptId = (int)($_GET['script_id'] ?? $_POST['script_id'] ?? 0);

$stmt = $pdo->prepare('SELECT s.*, b.brand_name FROM scripts s JOIN brands b ON b.id = s.brand_id WHERE s.id = ?');
$stmt->execute([$scriptId]);
$script = $stmt->fetch();

if (!$script) {
    exit('Script not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentTx = 'TXN-' . strtoupper(bin2hex(random_bytes(4)));
    $deliveryNote = "Auto-delivery complete. Download file from developer package: {$script['file_path']}";

    $order = $pdo->prepare('INSERT INTO orders (user_id, script_id, payment_status, payment_txn) VALUES (?, ?, ?, ?)');
    $order->execute([$user['id'], $scriptId, 'paid', $paymentTx]);
    $orderId = (int)$pdo->lastInsertId();

    $delivery = $pdo->prepare('INSERT INTO deliveries (order_id, user_id, script_id, delivery_note, delivered_url) VALUES (?, ?, ?, ?, ?)');
    $delivery->execute([$orderId, $user['id'], $scriptId, $deliveryNote, '/uploads/' . $script['file_path']]);

    header('Location: /user/deliveries.php?success=1');
    exit;
}
?>
<!doctype html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><script src="https://cdn.tailwindcss.com"></script><title>Checkout</title></head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
<div class="bg-white rounded-xl shadow p-8 w-full max-w-lg">
  <h1 class="text-2xl font-bold mb-4">Complete Payment</h1>
  <p class="mb-2">Product: <strong><?= htmlspecialchars($script['title']) ?></strong></p>
  <p class="mb-2">Brand: <strong><?= htmlspecialchars($script['brand_name']) ?></strong></p>
  <p class="mb-6">Amount: <strong>৳<?= number_format((float)$script['price'], 2) ?></strong></p>
  <p class="text-sm text-slate-500 mb-4">Demo payment flow: click button to simulate successful payment and auto website delivery.</p>
  <form method="post">
    <input type="hidden" name="script_id" value="<?= (int)$scriptId ?>">
    <button class="w-full bg-green-600 text-white rounded py-2">Pay Now</button>
  </form>
</div>
</body></html>
