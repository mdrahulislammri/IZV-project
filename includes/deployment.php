<?php

function queueBuildJob(PDO $pdo, int $orderId): int
{
    $stmt = $pdo->prepare('INSERT INTO build_jobs (order_id, status, progress, message) VALUES (?, "queued", 0, "Queued for deployment")');
    $stmt->execute([$orderId]);
    return (int)$pdo->lastInsertId();
}

function processBuildJobImmediately(PDO $pdo, int $orderId): void
{
    $pdo->prepare('UPDATE build_jobs SET status="building", progress=40, message="Preparing files" WHERE order_id=?')->execute([$orderId]);

    $stmt = $pdo->prepare('SELECT o.*, p.brand_id, b.subdomain FROM orders o JOIN projects p ON p.id=o.project_id JOIN brands b ON b.id=p.brand_id WHERE o.id=?');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    if (!$order) {
        return;
    }

    $domain = $order['domain_name'];
    if ($order['domain_type'] === 'subdomain') {
        $domain = strtolower(preg_replace('/[^a-z0-9-]+/', '', $domain));
        $domain = $domain . '.platform.com';
    }

    $adminUrl = 'https://' . $domain . '/admin';
    $deliveryNote = 'Auto deployment completed successfully for source brand: ' . ($order['source_subdomain'] ?? $order['subdomain']);

    $pdo->prepare('UPDATE orders SET build_status="delivered", deployed_url=?, admin_url=?, delivery_note=? WHERE id=?')
        ->execute([$domain, $adminUrl, $deliveryNote, $orderId]);

    $pdo->prepare('UPDATE build_jobs SET status="done", progress=100, message="Deployment completed" WHERE order_id=?')
        ->execute([$orderId]);
}
