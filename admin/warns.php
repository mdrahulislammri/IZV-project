<?php
require_once __DIR__ . '/../includes/auth.php';
$user = requireRole($pdo, 'admin');
$error=null;
if($_SERVER['REQUEST_METHOD']==='POST'){
    $dev=(int)($_POST['developer_id']??0);
    $msg=trim($_POST['message']??'');
    if(!$dev||!$msg){$error='Developer and message required.';} else {
        $pdo->prepare('INSERT INTO warns (developer_id,message) VALUES (?,?)')->execute([$dev,$msg]);
        $countStmt=$pdo->prepare('SELECT COUNT(*) FROM warns WHERE developer_id=?');
        $countStmt->execute([$dev]);
        if((int)$countStmt->fetchColumn()>=3){
            $pdo->prepare("UPDATE users SET status='banned' WHERE id=? AND role='developer'")->execute([$dev]);
        }
    }
}
$devs=$pdo->query("SELECT id,username,status FROM users WHERE role='developer' ORDER BY username")->fetchAll();
$warns=$pdo->query('SELECT w.*,u.username FROM warns w JOIN users u ON u.id=w.developer_id ORDER BY w.id DESC')->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin Warns</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-slate-100"><div class="max-w-6xl mx-auto p-4 sm:p-6"><div class="mb-4 flex justify-between"><h1 class="text-3xl font-black">Warn Management</h1><a class="text-blue-600" href="/admin/dashboard">Back</a></div><?php if($error):?><p class="mb-3 rounded bg-red-100 p-2 text-red-700"><?=htmlspecialchars($error)?></p><?php endif;?><form method="post" class="bg-white border rounded-xl p-4 grid md:grid-cols-3 gap-2 mb-4"><select name="developer_id" class="border rounded p-2" required><option value="">Select developer</option><?php foreach($devs as $d):?><option value="<?=$d['id']?>"><?=htmlspecialchars($d['username'])?> (<?=htmlspecialchars($d['status'])?>)</option><?php endforeach;?></select><input name="message" class="border rounded p-2 md:col-span-2" placeholder="Warn message" required><button class="bg-red-600 text-white rounded p-2 md:col-span-3">Add Warn</button></form><div class="bg-white border rounded-xl overflow-x-auto"><table class="w-full min-w-[700px] text-sm"><thead><tr class="bg-slate-50"><th class="p-3 text-left">Developer</th><th>Message</th><th>Date</th></tr></thead><tbody><?php foreach($warns as $w):?><tr class="border-t"><td class="p-3"><?=htmlspecialchars($w['username'])?></td><td><?=htmlspecialchars($w['message'])?></td><td><?=htmlspecialchars($w['created_at'])?></td></tr><?php endforeach;?></tbody></table></div></div></body></html>
