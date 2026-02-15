<?php
require_once __DIR__ . '/../includes/auth.php';
$user = requireRole($pdo, 'buyer');

$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
$developers = $pdo->query('SELECT id, username FROM users WHERE role="developer" ORDER BY username')->fetchAll();

$where = [];
$params = [];
if (!empty($_GET['category'])) { $where[] = 'p.category_id = ?'; $params[] = (int)$_GET['category']; }
if (!empty($_GET['developer'])) { $where[] = 'p.developer_id = ?'; $params[] = (int)$_GET['developer']; }
if (!empty($_GET['max_price'])) { $where[] = 'p.base_price <= ?'; $params[] = (float)$_GET['max_price']; }

$sql = 'SELECT p.*, c.name category_name, u.username developer_name FROM projects p JOIN categories c ON c.id=p.category_id JOIN users u ON u.id=p.developer_id WHERE p.status="active"';
if ($where) { $sql .= ' AND ' . implode(' AND ', $where); }
$sql .= ' ORDER BY p.id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Shop</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-slate-100">
<div class="max-w-7xl mx-auto p-4 sm:p-6">
<div class="flex justify-between items-center mb-4"><h1 class="text-3xl font-bold">Shop</h1><a href="/user/dashboard" class="text-blue-600">Back</a></div>
<form class="bg-white border rounded-xl p-4 grid md:grid-cols-4 gap-3 mb-6">
<select name="category" class="border rounded p-2"><option value="">Category</option><?php foreach($categories as $c):?><option value="<?=$c['id']?>" <?= (($_GET['category']??'')==$c['id'])?'selected':''?>><?=htmlspecialchars($c['name'])?></option><?php endforeach;?></select>
<select name="developer" class="border rounded p-2"><option value="">Developer</option><?php foreach($developers as $d):?><option value="<?=$d['id']?>" <?= (($_GET['developer']??'')==$d['id'])?'selected':''?>><?=htmlspecialchars($d['username'])?></option><?php endforeach;?></select>
<input type="number" step="0.01" name="max_price" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>" placeholder="Max Price" class="border rounded p-2">
<button class="bg-blue-600 text-white rounded p-2">Filter</button>
</form>
<div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
<?php foreach($projects as $p): ?>
<div class="bg-white border rounded-xl p-4">
<h3 class="font-bold text-lg"><?= htmlspecialchars($p['name']) ?></h3>
<p class="text-sm text-slate-500">Developer: <?= htmlspecialchars($p['developer_name']) ?></p>
<p class="text-sm text-slate-500">Category: <?= htmlspecialchars($p['category_name']) ?></p>
<p class="text-sm mt-2"><?= htmlspecialchars($p['description']) ?></p>
<a class="text-blue-600 text-sm" target="_blank" href="<?= htmlspecialchars($p['preview_link']) ?>">Preview</a>
<div class="flex justify-between items-center mt-3"><span class="font-bold">৳<?= number_format((float)$p['base_price'],2) ?></span><a href="/shop/buy.php?project_id=<?=$p['id']?>" class="bg-green-600 text-white px-3 py-2 rounded">Buy</a></div>
</div>
<?php endforeach; ?>
</div>
</div></body></html>
