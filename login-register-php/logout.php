<?php
require_once __DIR__ . '/includes/auth.php';

// Hapus remember_token dari data user (jika ada) agar cookie lama tidak bisa dipakai lagi
if (isset($_SESSION['user_email'])) {
    $users = loadUsers();
    foreach ($users as &$u) {
        if (strtolower($u['email']) === strtolower($_SESSION['user_email'])) {
            $u['remember_token'] = null;
        }
    }
    unset($u);
    saveUsers($users);
}

// Hapus cookie remember_me
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Hapus semua data session dan hancurkan session
$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;
