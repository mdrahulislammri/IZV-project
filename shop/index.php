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

$sql = 'SELECT p.*, c.name category_name, u.username developer_name, b.subdomain, b.brand_name FROM projects p JOIN categories c ON c.id=p.category_id JOIN users u ON u.id=p.developer_id JOIN brands b ON b.id=p.brand_id WHERE p.status="active"';
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
<body class="bg-slate-950 text-slate-100">
  <div class="mx-auto max-w-7xl p-4 sm:p-6">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-3xl font-black">Shop Marketplace</h1>
      <a href="/user/dashboard" class="rounded-lg border border-slate-700 px-3 py-2 text-blue-300 hover:bg-slate-900">← Back Dashboard</a>
    </div>

    <form class="mb-6 grid gap-3 rounded-2xl border border-slate-800 bg-slate-900 p-4 shadow-lg md:grid-cols-4">
      <select name="category" class="rounded-lg border border-slate-700 bg-slate-950 p-2">
        <option value="">Category</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= $c['id'] ?>" <?= (($_GET['category'] ?? '') == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <select name="developer" class="rounded-lg border border-slate-700 bg-slate-950 p-2">
        <option value="">Developer</option>
        <?php foreach ($developers as $d): ?>
          <option value="<?= $d['id'] ?>" <?= (($_GET['developer'] ?? '') == $d['id']) ? 'selected' : '' ?>><?= htmlspecialchars($d['username']) ?></option>
        <?php endforeach; ?>
      </select>
      <input type="number" step="0.01" name="max_price" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>" placeholder="Max Price" class="rounded-lg border border-slate-700 bg-slate-950 p-2">
      <button class="rounded-lg bg-blue-600 p-2 font-medium text-white hover:bg-blue-500">Apply Filters</button>
    </form>

    <?php if (!$projects): ?>
      <div class="rounded-2xl border border-slate-800 bg-slate-900 p-10 text-center text-slate-400">No projects found for selected filters.</div>
    <?php else: ?>
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <?php foreach ($projects as $p): ?>
          <article class="rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-lg shadow-slate-950/50 transition hover:-translate-y-1 hover:border-blue-600/50">
            <h2 class="text-lg font-bold"><?= htmlspecialchars($p['name']) ?></h2>
            <p class="text-sm text-slate-400">Developer: <?= htmlspecialchars($p['developer_name']) ?></p>
            <p class="text-sm text-slate-400">Category: <?= htmlspecialchars($p['category_name']) ?></p>
            <p class="text-sm text-slate-400">Brand: <?= htmlspecialchars($p['brand_name']) ?> (<?= htmlspecialchars($p['subdomain']) ?>)</p>
            <p class="mt-2 text-sm text-slate-300 line-clamp-3"><?= htmlspecialchars($p['description']) ?></p>
            <div class="mt-4 flex items-center justify-between">
              <div class="flex gap-3"><a class="text-sm font-medium text-blue-300 hover:text-blue-200" target="_blank" href="<?= htmlspecialchars($p['preview_link']) ?>">Preview</a><a class="text-sm font-medium text-cyan-300 hover:text-cyan-200" target="_blank" href="/store/<?= urlencode($p['subdomain']) ?>">Store</a></div>
              <span class="text-lg font-bold text-emerald-300">৳<?= number_format((float)$p['base_price'], 2) ?></span>
            </div>
            <a href="/shop/buy.php?project_id=<?= $p['id'] ?>" class="mt-4 block rounded-lg bg-emerald-600 px-3 py-2 text-center font-medium text-white hover:bg-emerald-500">Buy Now</a>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
<?= renderToastContainer() ?></body>
</html>
