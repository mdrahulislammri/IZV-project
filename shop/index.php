<?php
require_once __DIR__ . '/../includes/auth.php';
$user = requireRole($pdo, 'buyer');

$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
$developers = $pdo->query('SELECT id, username FROM users WHERE role="developer" ORDER BY username')->fetchAll();

$where = [];
$params = [];
if (!empty($_GET['category'])) {
    $where[] = 'p.category_id = ?';
    $params[] = (int)$_GET['category'];
}
if (!empty($_GET['developer'])) {
    $where[] = 'p.developer_id = ?';
    $params[] = (int)$_GET['developer'];
}
if (!empty($_GET['max_price'])) {
    $where[] = 'p.base_price <= ?';
    $params[] = (float)$_GET['max_price'];
}

$sql = 'SELECT p.*, c.name category_name, u.username developer_name FROM projects p JOIN categories c ON c.id=p.category_id JOIN users u ON u.id=p.developer_id WHERE p.status="active"';
if ($where) {
    $sql .= ' AND ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY p.id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Shop | ScriptDeploy</title>
  <meta name="description" content="Filter and buy premium projects by category, developer, and price.">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100">
  <div class="mx-auto max-w-7xl p-4 sm:p-6">
    <div class="mb-5 flex items-center justify-between">
      <h1 class="text-3xl font-black">Shop</h1>
      <a href="/user/dashboard" class="text-blue-600 hover:text-blue-700">← Back Dashboard</a>
    </div>

    <form class="mb-6 grid gap-3 rounded-xl border bg-white p-4 shadow-sm md:grid-cols-4">
      <select name="category" class="rounded-lg border p-2">
        <option value="">Category</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= $c['id'] ?>" <?= (($_GET['category'] ?? '') == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <select name="developer" class="rounded-lg border p-2">
        <option value="">Developer</option>
        <?php foreach ($developers as $d): ?>
          <option value="<?= $d['id'] ?>" <?= (($_GET['developer'] ?? '') == $d['id']) ? 'selected' : '' ?>><?= htmlspecialchars($d['username']) ?></option>
        <?php endforeach; ?>
      </select>
      <input type="number" step="0.01" name="max_price" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>" placeholder="Max Price" class="rounded-lg border p-2">
      <button class="rounded-lg bg-blue-600 p-2 font-medium text-white hover:bg-blue-700">Apply Filters</button>
    </form>

    <?php if (!$projects): ?>
      <div class="rounded-xl border bg-white p-8 text-center text-slate-500">No projects found for selected filters.</div>
    <?php else: ?>
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <?php foreach ($projects as $p): ?>
          <article class="rounded-xl border bg-white p-4 shadow-sm transition hover:shadow-md">
            <h2 class="text-lg font-bold"><?= htmlspecialchars($p['name']) ?></h2>
            <p class="text-sm text-slate-500">Developer: <?= htmlspecialchars($p['developer_name']) ?></p>
            <p class="text-sm text-slate-500">Category: <?= htmlspecialchars($p['category_name']) ?></p>
            <p class="mt-2 text-sm text-slate-700"><?= htmlspecialchars($p['description']) ?></p>
            <div class="mt-3 flex items-center justify-between">
              <a class="text-sm font-medium text-blue-600 hover:text-blue-700" target="_blank" href="<?= htmlspecialchars($p['preview_link']) ?>">Preview</a>
              <span class="text-lg font-bold">৳<?= number_format((float)$p['base_price'], 2) ?></span>
            </div>
            <a href="/shop/buy.php?project_id=<?= $p['id'] ?>" class="mt-3 block rounded-lg bg-green-600 px-3 py-2 text-center font-medium text-white hover:bg-green-700">Buy Now</a>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
