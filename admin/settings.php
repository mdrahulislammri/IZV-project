<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/site_settings.php';

$user = requireRole($pdo, 'admin');

$editableKeys = [
    'site_name',
    'site_meta_description',
    'hero_badge',
    'hero_title',
    'hero_description',
    'contact_email',
    'contact_hours',
    'nav_login_text',
    'nav_register_text',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfOrFail($_POST['csrf_token'] ?? null);
    foreach ($editableKeys as $key) {
        if (isset($_POST[$key])) {
            saveSiteSetting($pdo, $key, trim((string)$_POST[$key]));
        }
    }
    pushToast('success', 'Website settings updated successfully.');
    header('Location: /admin/settings');
    exit;
}

$settings = siteSettings($pdo);
$get = function (string $key, string $default = '') use ($settings): string {
    return (string)($settings[$key] ?? $default);
};
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Website Control</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100">
  <div class="max-w-5xl mx-auto p-4 sm:p-6">
    <div class="mb-4 flex items-center justify-between">
      <h1 class="text-3xl font-black">Website Control</h1>
      <a class="text-blue-600" href="/admin/dashboard">Back</a>
    </div>

    <form method="post" class="space-y-4 rounded-xl border bg-white p-4">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

      <div class="grid gap-2 sm:grid-cols-2">
        <label class="text-sm font-medium">Site Name
          <input name="site_name" value="<?= htmlspecialchars($get('site_name', 'ScriptDeploy')) ?>" class="mt-1 w-full rounded border p-2">
        </label>
        <label class="text-sm font-medium">Meta Description
          <input name="site_meta_description" value="<?= htmlspecialchars($get('site_meta_description', 'Premium script marketplace.')) ?>" class="mt-1 w-full rounded border p-2">
        </label>
      </div>

      <div class="grid gap-2 sm:grid-cols-2">
        <label class="text-sm font-medium">Hero Badge
          <input name="hero_badge" value="<?= htmlspecialchars($get('hero_badge', 'Premium SaaS Marketplace')) ?>" class="mt-1 w-full rounded border p-2">
        </label>
        <label class="text-sm font-medium">Hero Title
          <input name="hero_title" value="<?= htmlspecialchars($get('hero_title', 'Build, Sell & Launch Websites Faster')) ?>" class="mt-1 w-full rounded border p-2">
        </label>
      </div>

      <label class="text-sm font-medium block">Hero Description
        <textarea name="hero_description" class="mt-1 w-full rounded border p-2" rows="3"><?= htmlspecialchars($get('hero_description', 'One powerful platform for buyers, developers and admin operations.')) ?></textarea>
      </label>

      <div class="grid gap-2 sm:grid-cols-2">
        <label class="text-sm font-medium">Contact Email
          <input name="contact_email" value="<?= htmlspecialchars($get('contact_email', 'support@scriptdeploy.local')) ?>" class="mt-1 w-full rounded border p-2">
        </label>
        <label class="text-sm font-medium">Business Hours
          <input name="contact_hours" value="<?= htmlspecialchars($get('contact_hours', 'Sat–Thu, 10:00 AM - 8:00 PM')) ?>" class="mt-1 w-full rounded border p-2">
        </label>
      </div>

      <div class="grid gap-2 sm:grid-cols-2">
        <label class="text-sm font-medium">Navigation Login Label
          <input name="nav_login_text" value="<?= htmlspecialchars($get('nav_login_text', 'Login')) ?>" class="mt-1 w-full rounded border p-2">
        </label>
        <label class="text-sm font-medium">Navigation Register Label
          <input name="nav_register_text" value="<?= htmlspecialchars($get('nav_register_text', 'Register')) ?>" class="mt-1 w-full rounded border p-2">
        </label>
      </div>

      <button class="rounded bg-blue-600 px-4 py-2 text-white">Save Website Settings</button>
    </form>
  </div>
<?= renderToastContainer() ?></body>
</html>
