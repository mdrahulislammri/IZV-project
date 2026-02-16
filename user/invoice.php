<?php
require_once __DIR__ . '/../includes/auth.php';
$user = requireRole($pdo, 'buyer');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['renew'])) {
    verifyCsrfOrFail($_POST['csrf_token'] ?? null);

    $invoiceId = (int)$_POST['renew'];
    $stmt = $pdo->prepare('SELECT i.*, o.user_id, o.expires_at, o.total_price FROM invoices i JOIN orders o ON o.id=i.order_id WHERE i.id=? AND o.user_id=?');
    $stmt->execute([$invoiceId, $user['id']]);
    $inv = $stmt->fetch();

    if ($inv) {
        $expiry = new DateTime($inv['due_date']);
        $now = new DateTime();
        $lateFee = 0;

        if ($now > (clone $expiry)->modify('+15 days')) {
            $lateFee = round((float)$inv['amount'] * 0.06, 2);
        }

        $newDue = (new DateTime())->modify('+1 month')->format('Y-m-d');
        $total = (float)$inv['amount'] + $lateFee;

        $upInv = $pdo->prepare('UPDATE invoices SET late_fee=?, total=?, due_date=?, status="paid" WHERE id=?');
        $upInv->execute([$lateFee, $total, $newDue, $invoiceId]);

        $upOrd = $pdo->prepare('UPDATE orders SET expires_at=?, status="active", late_fee=?, total_price=? WHERE id=?');
        $upOrd->execute([$newDue, $lateFee, $total, $inv['order_id']]);
    }

    header('Location: /user/invoice');
    exit;
}

$stmt = $pdo->prepare('SELECT i.*, o.website_name, o.domain_name, o.duration_months, o.status order_status FROM invoices i JOIN orders o ON o.id=i.order_id WHERE o.user_id=? ORDER BY i.id DESC');
$stmt->execute([$user['id']]);
$invoices = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Invoices</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100">
  <div class="max-w-7xl mx-auto p-4 sm:p-6">
    <h1 class="text-3xl font-bold mb-4">Invoice</h1>

    <div class="bg-white border rounded-xl overflow-x-auto">
      <table class="w-full min-w-[760px] text-sm">
        <thead>
          <tr class="bg-slate-50">
            <th class="p-3 text-left">Website</th>
            <th>Domain</th>
            <th>Duration</th>
            <th>Expiry</th>
            <th>Price</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($invoices as $i): ?>
            <tr class="border-t">
              <td class="p-3"><?= htmlspecialchars($i['website_name']) ?></td>
              <td><?= htmlspecialchars($i['domain_name']) ?></td>
              <td><?= (int)$i['duration_months'] ?> months</td>
              <td><?= htmlspecialchars($i['due_date']) ?></td>
              <td>৳<?= number_format((float)$i['total'], 2) ?></td>
              <td><?= htmlspecialchars($i['order_status']) ?></td>
              <td>
                <form method="post" class="inline">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                  <input type="hidden" name="renew" value="<?= (int)$i['id'] ?>">
                  <button type="submit" class="text-blue-600">Renew</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
