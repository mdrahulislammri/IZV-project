<?php
require_once __DIR__ . '/../includes/auth.php';
$user = requireRole($pdo, 'developer');
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = (int)($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $preview = trim($_POST['preview_link'] ?? '');
    $price = (float)($_POST['base_price'] ?? 0);
    if (!$name || !$category || !$description || !$preview || $price <= 0) {
        $error = 'All fields required';
    } else {
        $script = isset($_FILES['script_file']) && $_FILES['script_file']['error']===UPLOAD_ERR_OK ? uniqid('script_').'_'.basename($_FILES['script_file']['name']) : null;
        $sqlFile = isset($_FILES['sql_file']) && $_FILES['sql_file']['error']===UPLOAD_ERR_OK ? uniqid('sql_').'_'.basename($_FILES['sql_file']['name']) : null;
        if ($script) { move_uploaded_file($_FILES['script_file']['tmp_name'], __DIR__.'/../uploads/'.$script); }
        if ($sqlFile) { move_uploaded_file($_FILES['sql_file']['tmp_name'], __DIR__.'/../uploads/'.$sqlFile); }
        $ins = $pdo->prepare('INSERT INTO projects (developer_id, category_id, name, description, preview_link, base_price, script_file, sql_file, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, "active")');
        $ins->execute([$user['id'], $category, $name, $description, $preview, $price, $script, $sqlFile]);
    }
}
$cats = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
$stmt = $pdo->prepare('SELECT p.*, c.name category_name FROM projects p JOIN categories c ON c.id=p.category_id WHERE p.developer_id=? ORDER BY p.id DESC');
$stmt->execute([$user['id']]);
$projects = $stmt->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Dev Projects</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-slate-100"><div class="max-w-7xl mx-auto p-4 sm:p-6">
<h1 class="text-3xl font-bold mb-4">My Projects</h1>
<?php if($error):?><p class="text-red-600"><?=htmlspecialchars($error)?></p><?php endif;?>
<form method="post" enctype="multipart/form-data" class="bg-white border rounded-xl p-4 grid md:grid-cols-2 gap-3 mb-4">
<input name="name" placeholder="Project Name" class="border rounded p-2" required>
<select name="category_id" class="border rounded p-2" required><option value="">Category</option><?php foreach($cats as $c):?><option value="<?=$c['id']?>"><?=htmlspecialchars($c['name'])?></option><?php endforeach;?></select>
<textarea name="description" class="border rounded p-2 md:col-span-2" placeholder="Description" required></textarea>
<input name="preview_link" placeholder="Preview Link" class="border rounded p-2" required>
<input name="base_price" type="number" step="0.01" placeholder="Price" class="border rounded p-2" required>
<label class="border rounded p-2">Script Upload <input type="file" name="script_file" required></label>
<label class="border rounded p-2">SQL Upload <input type="file" name="sql_file" required></label>
<button class="bg-blue-600 text-white rounded p-2 md:col-span-2">Upload Project</button>
</form>
<div class="bg-white border rounded-xl overflow-x-auto"><table class="w-full min-w-[640px] text-sm"><thead><tr class="bg-slate-50"><th class="p-3 text-left">Project</th><th>Category</th><th>Price</th><th>Status</th></tr></thead><tbody><?php foreach($projects as $p):?><tr class="border-t"><td class="p-3"><?=htmlspecialchars($p['name'])?></td><td><?=htmlspecialchars($p['category_name'])?></td><td>৳<?=number_format((float)$p['base_price'],2)?></td><td><?=htmlspecialchars($p['status'])?></td></tr><?php endforeach;?></tbody></table></div>
</div></body></html>
