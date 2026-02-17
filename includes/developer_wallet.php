<?php

function walletRow(PDO $pdo, int $developerId): array
{
    $stmt = $pdo->prepare('SELECT * FROM developer_wallets WHERE developer_id=? LIMIT 1');
    $stmt->execute([$developerId]);
    $row = $stmt->fetch();

    if (!$row) {
        $pdo->prepare('INSERT INTO developer_wallets (developer_id, balance) VALUES (?, 0)')->execute([$developerId]);
        $stmt->execute([$developerId]);
        $row = $stmt->fetch();
    }

    return $row;
}

function walletDeposit(PDO $pdo, int $developerId, float $amount, string $note = 'Manual deposit'): void
{
    if ($amount <= 0) {
        return;
    }

    $wallet = walletRow($pdo, $developerId);
    $newBalance = (float)$wallet['balance'] + $amount;

    $pdo->prepare('UPDATE developer_wallets SET balance=? WHERE id=?')->execute([$newBalance, $wallet['id']]);
    $pdo->prepare('INSERT INTO developer_wallet_transactions (developer_id, type, amount, note) VALUES (?, "deposit", ?, ?)')
        ->execute([$developerId, $amount, $note]);
}

function walletChargeInstall(PDO $pdo, int $developerId, float $amount, string $note = 'Install processing charge'): bool
{
    $wallet = walletRow($pdo, $developerId);
    if ((float)$wallet['balance'] < $amount) {
        return false;
    }

    $newBalance = (float)$wallet['balance'] - $amount;
    $pdo->prepare('UPDATE developer_wallets SET balance=? WHERE id=?')->execute([$newBalance, $wallet['id']]);
    $pdo->prepare('INSERT INTO developer_wallet_transactions (developer_id, type, amount, note) VALUES (?, "charge", ?, ?)')
        ->execute([$developerId, $amount, $note]);

    return true;
}
