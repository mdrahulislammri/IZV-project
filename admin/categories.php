<?php
require_once __DIR__ . '/../includes/auth.php';
$user = requireRole($pdo, 'admin');
$error=null;
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name=trim($_POST['name']??'');
    if(!$name){$error='Category required.';} else {
        $stmt=$pdo->prepare('INSERT INTO categories (name) VALUES (?)');
        try{$stmt->execute([$name]);}catch(Throwable $e){$error='Category exists or invalid.';}
    }
}
$cats=$pdo->query('SELECT id,name,created_at FROM categories ORDER BY id DESC')->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin Categories</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-slate-100"><div class="max-w-5xl mx-auto p-4 sm:p-6"><div class="mb-4 flex justify-between"><h1 class="text-3xl font-black">Manage Categories</h1><a class="text-blue-600" href="/admin/dashboard">Back</a></div><?php if($error):?><p class="mb-3 rounded bg-red-100 p-2 text-red-700"><?=htmlspecialchars($error)?></p><?php endif;?><form method="post" class="mb-4 flex gap-2"><input name="name" class="flex-1 border rounded p-2" placeholder="Category name" required><button class="bg-blue-600 text-white px-4 rounded">Add</button></form><div class="bg-white border rounded-xl overflow-hidden"><table class="w-full text-sm"><thead><tr class="bg-slate-50"><th class="p-3 text-left">Name</th><th>Created</th></tr></thead><tbody><?php foreach($cats as $c):?><tr class="border-t"><td class="p-3"><?=htmlspecialchars($c['name'])?></td><td><?=htmlspecialchars($c['created_at'])?></td></tr><?php endforeach;?></tbody></table></div></div><?= renderToastContainer() ?></body></html>
