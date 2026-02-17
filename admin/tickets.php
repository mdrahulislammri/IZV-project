<?php
require_once __DIR__ . '/../includes/auth.php';
$user = requireRole($pdo, 'admin');
if($_SERVER['REQUEST_METHOD']==='POST'){
    $id=(int)($_POST['ticket_id']??0);
    $reply=trim($_POST['reply']??'');
    if($id&&$reply){
        $stmt=$pdo->prepare("UPDATE tickets SET admin_reply=?, status='closed' WHERE id=?");
        $stmt->execute([$reply,$id]);
    }
}
$tickets=$pdo->query('SELECT t.*,u.username FROM tickets t JOIN users u ON u.id=t.user_id ORDER BY t.id DESC')->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin Tickets</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-slate-100"><div class="max-w-6xl mx-auto p-4 sm:p-6"><div class="mb-4 flex justify-between"><h1 class="text-3xl font-black">Support Tickets</h1><a class="text-blue-600" href="/admin/dashboard">Back</a></div><div class="space-y-3"><?php foreach($tickets as $t):?><div class="bg-white border rounded-xl p-4"><p class="font-bold"><?=htmlspecialchars($t['subject'])?> <span class="text-xs text-slate-500">by <?=htmlspecialchars($t['username'])?> (<?=htmlspecialchars($t['role'])?>)</span></p><p class="text-sm my-1"><?=htmlspecialchars($t['message'])?></p><p class="text-sm text-slate-500">Status: <?=htmlspecialchars($t['status'])?></p><?php if($t['admin_reply']):?><p class="text-sm text-green-700">Reply: <?=htmlspecialchars($t['admin_reply'])?></p><?php else:?><form method="post" class="mt-2 flex gap-2"><input type="hidden" name="ticket_id" value="<?=$t['id']?>"><input name="reply" class="flex-1 border rounded p-2" placeholder="Write admin reply" required><button class="bg-blue-600 text-white rounded px-3">Reply & Close</button></form><?php endif;?></div><?php endforeach;?></div></div><?= renderToastContainer() ?></body></html>
