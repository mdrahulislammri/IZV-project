<?php
require_once __DIR__ . '/../includes/auth.php';
session_destroy();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
pushToast('info', 'You have been logged out successfully.');
header('Location: /login');
exit;
