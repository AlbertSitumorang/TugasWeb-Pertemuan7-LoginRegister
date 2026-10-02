<?php
require_once __DIR__ . '/includes/auth.php';

// Proteksi halaman: redirect ke login jika belum login
requireLogin();

$user  = findUserByEmail($_SESSION['user_email']);
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — Tugas Rutin 7</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="card dashboard-card">
    <div class="dashboard-header">
        <h1>👋 Dashboard</h1>
        <span class="badge">Login aktif</span>
    </div>

    <?php if ($flash): ?>
        <div class="alert alert-<?= sanitize($flash['type']) ?>"><?= sanitize($flash['message']) ?></div>
    <?php endif; ?>

    <div class="profile-row">
        <span>Nama</span>
        <span><?= sanitize($user['nama'] ?? '-') ?></span>
    </div>
    <div class="profile-row">
        <span>Email</span>
        <span><?= sanitize($user['email'] ?? '-') ?></span>
    </div>
    <div class="profile-row">
        <span>Terdaftar sejak</span>
        <span><?= sanitize($user['created_at'] ?? '-') ?></span>
    </div>

    <div class="actions">
        <a href="edit_profile.php" class="btn-secondary">✏️ Edit Profil</a>
        <a href="logout.php" class="btn-danger">🚪 Logout</a>
    </div>
</div>
</body>
</html>
