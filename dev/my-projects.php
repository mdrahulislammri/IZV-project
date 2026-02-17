<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/developer_package.php';
$user = requireRole($pdo, 'developer');
$error = null;
$sub = getActiveSubscription($pdo, (int)$user['id']);
$plan = $sub ? getPlanByCode($pdo, $sub['package_code']) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $brandId = (int)($_POST['brand_id'] ?? 0);
    $category = (int)($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $preview = trim($_POST['preview_link'] ?? '');
    $price = (float)($_POST['base_price'] ?? 0);

    if (!$plan) {
        $error = 'Please buy a developer package first.';
    } elseif (!$name || !$brandId || !$category || !$description || !$preview || $price <= 0) {
        $error = 'All fields required, including brand.';
    } else {
        if ((int)$plan['script_limit'] >= 0) {
            $cStmt = $pdo->prepare('SELECT COUNT(*) FROM projects WHERE developer_id=?');
            $cStmt->execute([$user['id']]);
            if ((int)$cStmt->fetchColumn() >= (int)$plan['script_limit']) {
                $error = 'Script upload limit reached for your package. Upgrade package.';
            }
        }

        if (!$error) {
        $script = isset($_FILES['script_file']) && $_FILES['script_file']['error'] === UPLOAD_ERR_OK ? uniqid('script_') . '_' . basename($_FILES['script_file']['name']) : null;
        $sqlFile = isset($_FILES['sql_file']) && $_FILES['sql_file']['error'] === UPLOAD_ERR_OK ? uniqid('sql_') . '_' . basename($_FILES['sql_file']['name']) : null;
        if ($script) { move_uploaded_file($_FILES['script_file']['tmp_name'], __DIR__ . '/../uploads/' . $script); }
        if ($sqlFile) { move_uploaded_file($_FILES['sql_file']['tmp_name'], __DIR__ . '/../uploads/' . $sqlFile); }

        $ins = $pdo->prepare('INSERT INTO projects (developer_id, brand_id, category_id, name, description, preview_link, base_price, script_file, sql_file, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, "active")');
        $ins->execute([$user['id'], $brandId, $category, $name, $description, $preview, $price, $script, $sqlFile]);
        }
    }
}

$cats = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
$brandsStmt = $pdo->prepare('SELECT id, brand_name, subdomain FROM brands WHERE developer_id=? ORDER BY id DESC');
$brandsStmt->execute([$user['id']]);
$brands = $brandsStmt->fetchAll();

$stmt = $pdo->prepare('SELECT p.*, c.name category_name, b.brand_name, b.subdomain FROM projects p JOIN categories c ON c.id=p.category_id JOIN brands b ON b.id=p.brand_id WHERE p.developer_id=? ORDER BY p.id DESC');
$stmt->execute([$user['id']]);
$projects = $stmt->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Dev Projects</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-slate-950 text-slate-100"><div class="max-w-7xl mx-auto p-4 sm:p-6">
<div class="flex flex-wrap justify-between items-center mb-4 gap-2"><h1 class="text-3xl font-bold">My Projects</h1><a href="/dev/brands" class="rounded border border-blue-500 px-3 py-2 text-blue-300">+ Create Brand</a></div>
<p class="mb-3 text-sm text-blue-300">Current Package: <?= htmlspecialchars(strtoupper($sub['package_code'] ?? 'NONE')) ?></p>
<?php if($error):?><p class="mb-3 rounded bg-red-950/70 border border-red-700 p-3 text-red-200"><?=htmlspecialchars($error)?></p><?php endif;?>
<?php if(!$brands):?><p class="mb-3 rounded bg-amber-950/70 border border-amber-700 p-3 text-amber-200">Create at least one brand before uploading a project.</p><?php endif;?>
<form method="post" enctype="multipart/form-data" class="bg-slate-900 border border-slate-800 rounded-xl p-4 grid md:grid-cols-2 gap-3 mb-4">
<input name="name" placeholder="Project Name" class="border border-slate-700 bg-slate-950 rounded p-2" required>
<select name="brand_id" class="border border-slate-700 bg-slate-950 rounded p-2" required><option value="">Brand</option><?php foreach($brands as $b):?><option value="<?=$b['id']?>"><?=htmlspecialchars($b['brand_name'])?> (<?=htmlspecialchars($b['subdomain'])?>)</option><?php endforeach;?></select>
<select name="category_id" class="border border-slate-700 bg-slate-950 rounded p-2" required><option value="">Category</option><?php foreach($cats as $c):?><option value="<?=$c['id']?>"><?=htmlspecialchars($c['name'])?></option><?php endforeach;?></select>
<input name="preview_link" placeholder="Preview Link" class="border border-slate-700 bg-slate-950 rounded p-2" required>
<textarea name="description" class="border border-slate-700 bg-slate-950 rounded p-2 md:col-span-2" placeholder="Description" required></textarea>
<input name="base_price" type="number" step="0.01" placeholder="Price" class="border border-slate-700 bg-slate-950 rounded p-2" required>
<label class="border border-slate-700 bg-slate-950 rounded p-2">Script Upload <input type="file" name="script_file" required></label>
<label class="border border-slate-700 bg-slate-950 rounded p-2">SQL Upload <input type="file" name="sql_file" required></label>
<button class="bg-blue-600 text-white rounded p-2 md:col-span-2" <?= !$brands ? 'disabled' : '' ?>>Upload Project</button>
</form>
<div class="bg-slate-900 border border-slate-800 rounded-xl overflow-x-auto"><table class="w-full min-w-[760px] text-sm"><thead><tr class="bg-slate-950"><th class="p-3 text-left">Project</th><th>Brand</th><th>Subdomain</th><th>Category</th><th>Price</th><th>Status</th></tr></thead><tbody><?php foreach($projects as $p):?><tr class="border-t border-slate-800"><td class="p-3"><?=htmlspecialchars($p['name'])?></td><td><?=htmlspecialchars($p['brand_name'])?></td><td><?=htmlspecialchars($p['subdomain'])?></td><td><?=htmlspecialchars($p['category_name'])?></td><td>৳<?=number_format((float)$p['base_price'],2)?></td><td><?=htmlspecialchars($p['status'])?></td></tr><?php endforeach;?></tbody></table></div>
</div></body></html>
