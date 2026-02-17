<?php

function userWalletRow(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare('SELECT * FROM user_wallets WHERE user_id=? LIMIT 1');
    $stmt->execute([$userId]);
    $row = $stmt->fetch();

    if (!$row) {
        $pdo->prepare('INSERT INTO user_wallets (user_id, balance) VALUES (?, 0)')->execute([$userId]);
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
    }

    return $row;
}

function userWalletDeposit(PDO $pdo, int $userId, float $amount, string $note = 'Manual deposit'): void
{
    if ($amount <= 0) {
        return;
    }

    $wallet = userWalletRow($pdo, $userId);
    $newBalance = (float)$wallet['balance'] + $amount;

    $pdo->prepare('UPDATE user_wallets SET balance=? WHERE id=?')->execute([$newBalance, $wallet['id']]);
    $pdo->prepare('INSERT INTO user_wallet_transactions (user_id, type, amount, note) VALUES (?, "deposit", ?, ?)')
        ->execute([$userId, $amount, $note]);
}

function userWalletCharge(PDO $pdo, int $userId, float $amount, string $note = 'Purchase charge'): bool
{
    $wallet = userWalletRow($pdo, $userId);
    if ((float)$wallet['balance'] < $amount) {
        return false;
    }

    $newBalance = (float)$wallet['balance'] - $amount;
    $pdo->prepare('UPDATE user_wallets SET balance=? WHERE id=?')->execute([$newBalance, $wallet['id']]);
    $pdo->prepare('INSERT INTO user_wallet_transactions (user_id, type, amount, note) VALUES (?, "charge", ?, ?)')
        ->execute([$userId, $amount, $note]);

    return true;
}
