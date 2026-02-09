<?php
require_once __DIR__ . '/../includes/auth.php';
requireDeveloper($pdo);
$user = currentUser($pdo);
$message = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_brand') {
    $brand = trim($_POST['brand_name'] ?? '');
    $subdomain = strtolower(trim($_POST['subdomain'] ?? ''));

    if (!$brand || !$subdomain) {
        $error = 'Brand and subdomain are required.';
    } else {
        $check = $pdo->prepare('SELECT id FROM brands WHERE subdomain = ?');
        $check->execute([$subdomain]);
        if ($check->fetch()) {
            $error = 'Subdomain already taken.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO brands (developer_id, brand_name, subdomain) VALUES (?, ?, ?)');
            $stmt->execute([$user['id'], $brand, $subdomain]);
            $message = 'Brand created.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'upload_script') {
    $brandId = (int)($_POST['brand_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);

    if (!$brandId || !$title || !$description || $price <= 0) {
        $error = 'All script fields are required.';
    } elseif (!isset($_FILES['script_file']) || $_FILES['script_file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Script file upload failed.';
    } else {
        $thumbName = null;
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            $thumbName = uniqid('thumb_', true) . '_' . basename($_FILES['thumbnail']['name']);
            move_uploaded_file($_FILES['thumbnail']['tmp_name'], __DIR__ . '/../uploads/' . $thumbName);
        }

        $scriptName = uniqid('script_', true) . '_' . basename($_FILES['script_file']['name']);
        move_uploaded_file($_FILES['script_file']['tmp_name'], __DIR__ . '/../uploads/' . $scriptName);

        $stmt = $pdo->prepare('INSERT INTO scripts (developer_id, brand_id, title, description, price, file_path, thumbnail) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$user['id'], $brandId, $title, $description, $price, $scriptName, $thumbName]);
        $message = 'Script uploaded successfully.';
    }
}

$brands = $pdo->prepare('SELECT id, brand_name, subdomain FROM brands WHERE developer_id = ? ORDER BY id DESC');
$brands->execute([$user['id']]);
$brands = $brands->fetchAll();

$scripts = $pdo->prepare('SELECT s.*, b.brand_name, b.subdomain FROM scripts s JOIN brands b ON b.id = s.brand_id WHERE s.developer_id = ? ORDER BY s.id DESC');
$scripts->execute([$user['id']]);
$scripts = $scripts->fetchAll();
?>
<!doctype html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><script src="https://cdn.tailwindcss.com"></script><title>Developer Dashboard</title></head>
<body class="bg-slate-100">
<div class="max-w-6xl mx-auto p-6 space-y-8">
  <div class="flex justify-between items-center"><h1 class="text-3xl font-bold">Developer Dashboard</h1><a href="/" class="text-blue-600">Home</a></div>
  <?php if ($message): ?><p class="bg-green-100 text-green-700 p-3 rounded"><?= htmlspecialchars($message) ?></p><?php endif; ?>
  <?php if ($error): ?><p class="bg-red-100 text-red-700 p-3 rounded"><?= htmlspecialchars($error) ?></p><?php endif; ?>

  <section class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-xl mb-4">1) Create Brand + Subdomain</h2>
    <form method="post" class="grid md:grid-cols-3 gap-3">
      <input type="hidden" name="action" value="create_brand">
      <input name="brand_name" placeholder="Brand Name" class="border rounded p-2" required>
      <input name="subdomain" placeholder="Subdomain (example: mybrand)" class="border rounded p-2" required>
      <button class="bg-blue-600 text-white rounded px-4">Create Brand</button>
    </form>
  </section>

  <section class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-xl mb-4">2) Upload Script Package</h2>
    <form method="post" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-3">
      <input type="hidden" name="action" value="upload_script">
      <select name="brand_id" class="border rounded p-2" required>
        <option value="">Choose Brand</option>
        <?php foreach ($brands as $brand): ?>
          <option value="<?= (int)$brand['id'] ?>"><?= htmlspecialchars($brand['brand_name']) ?> (<?= htmlspecialchars($brand['subdomain']) ?>)</option>
        <?php endforeach; ?>
      </select>
      <input name="title" placeholder="Script Title" class="border rounded p-2" required>
      <textarea name="description" placeholder="Description" class="border rounded p-2 md:col-span-2" required></textarea>
      <input name="price" type="number" step="0.01" placeholder="Price" class="border rounded p-2" required>
      <label class="border rounded p-2">Script ZIP/PHP file: <input type="file" name="script_file" required></label>
      <label class="border rounded p-2 md:col-span-2">Thumbnail image (optional): <input type="file" name="thumbnail" accept="image/*"></label>
      <button class="bg-green-600 text-white rounded px-4 py-2 md:col-span-2">Upload Package</button>
    </form>
  </section>

  <section class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-xl mb-4">My Uploaded Packages</h2>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead><tr class="text-left border-b"><th>Title</th><th>Brand</th><th>Subdomain</th><th>Price</th></tr></thead>
        <tbody>
          <?php foreach ($scripts as $s): ?>
            <tr class="border-b"><td class="py-2"><?= htmlspecialchars($s['title']) ?></td><td><?= htmlspecialchars($s['brand_name']) ?></td><td><?= htmlspecialchars($s['subdomain']) ?></td><td>৳<?= number_format((float)$s['price'], 2) ?></td></tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
</body></html>
