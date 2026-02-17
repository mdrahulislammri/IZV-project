<?php
require_once __DIR__ . '/../includes/auth.php';
$user = requireRole($pdo, 'buyer');
$orderId = (int)($_GET['order_id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM orders WHERE id=? AND user_id=?');
$stmt->execute([$orderId, $user['id']]);
$order = $stmt->fetch();
if (!$order) { exit('Order not found'); }
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Success</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-900 text-white"><div class="max-w-3xl mx-auto p-6">
<h1 class="text-3xl font-bold mb-4">Payment Success ✅</h1>
<div class="space-y-3 mb-6" id="steps"></div>
<div class="bg-white text-slate-900 rounded-xl p-4">
<p><b>Website URL:</b> https://<?= htmlspecialchars($order['deployed_url']) ?></p>
<p><b>Admin URL:</b> <?= htmlspecialchars($order['admin_url']) ?></p>
<p><b>Admin Username:</b> <?= htmlspecialchars($order['admin_username']) ?></p>
<p><b>Admin Password:</b> <?= htmlspecialchars($order['admin_password']) ?></p>
<a class="text-blue-600" href="/user/my-projects">Go My Projects</a>
</div></div>
<script>
const labels=['Website Domain Deployment','Website Script Setup','Website SQL Setup','Website AI Engine Check-in'];
const root=document.getElementById('steps');
labels.forEach(l=>{const d=document.createElement('div');d.className='bg-slate-800 p-3 rounded';d.innerHTML=`<p>${l}</p><div class="w-full bg-slate-700 h-2 rounded"><div class="h-2 bg-green-500 rounded bar" style="width:0%"></div></div>`;root.appendChild(d)});
let p=0;const bars=document.querySelectorAll('.bar');const t=setInterval(()=>{p+=5;bars.forEach(b=>b.style.width=p+'%');if(p>=100)clearInterval(t);},120);
</script>
<?= renderToastContainer() ?></body></html>
