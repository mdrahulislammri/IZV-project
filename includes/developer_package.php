<?php

function packageCatalog(): array
{
    return [
        'starter' => [
            'name' => 'Starter',
            'price' => 999,
            'brand_limit' => 1,
            'script_limit' => 5,
            'install_limit' => 20,
            'allow_custom_domain' => 0,
        ],
        'pro' => [
            'name' => 'Pro',
            'price' => 2999,
            'brand_limit' => 3,
            'script_limit' => 20,
            'install_limit' => 200,
            'allow_custom_domain' => 0,
        ],
        'enterprise' => [
            'name' => 'Enterprise',
            'price' => 7999,
            'brand_limit' => -1,
            'script_limit' => -1,
            'install_limit' => -1,
            'allow_custom_domain' => 1,
        ],
    ];
}

function getActiveSubscription(PDO $pdo, int $developerId): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM developer_subscriptions WHERE developer_id=? AND status="active" AND expires_at >= CURDATE() ORDER BY id DESC LIMIT 1');
    $stmt->execute([$developerId]);
    $sub = $stmt->fetch();
    return $sub ?: null;
}

function getPlanByCode(PDO $pdo, string $code): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM developer_packages WHERE code=? LIMIT 1');
    $stmt->execute([$code]);
    $plan = $stmt->fetch();
    return $plan ?: null;
}
