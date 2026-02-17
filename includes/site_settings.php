<?php

function siteSetting(PDO $pdo, string $key, ?string $default = null): ?string
{
    $stmt = $pdo->prepare('SELECT setting_value FROM site_settings WHERE setting_key=? LIMIT 1');
    $stmt->execute([$key]);
    $val = $stmt->fetchColumn();
    if ($val === false || $val === null || $val === '') {
        return $default;
    }
    return (string)$val;
}

function siteSettings(PDO $pdo): array
{
    $rows = $pdo->query('SELECT setting_key, setting_value FROM site_settings')->fetchAll();
    $out = [];
    foreach ($rows as $row) {
        $out[$row['setting_key']] = $row['setting_value'];
    }
    return $out;
}

function saveSiteSetting(PDO $pdo, string $key, string $value): void
{
    $stmt = $pdo->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');
    $stmt->execute([$key, $value]);
}
