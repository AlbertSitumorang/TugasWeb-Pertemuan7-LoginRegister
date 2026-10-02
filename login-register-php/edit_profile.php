<?php
require_once __DIR__ . '/includes/auth.php';

requireLogin();

$currentEmail = $_SESSION['user_email'];
$errors = [];
$user = findUserByEmail($currentEmail);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama            = sanitize($_POST['nama'] ?? '');
    $newPassword     = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($nama === '' || strlen($nama) < 3) {
        $errors[] = 'Nama minimal 3 karakter.';
    }

    if ($newPassword !== '') {
        if (strlen($newPassword) < 6) {
            $errors[] = 'Password baru minimal 6 karakter.';
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = 'Konfirmasi password baru tidak cocok.';
        }
    }

    if (empty($errors)) {
        $users = loadUsers();
        foreach ($users as &$u) {
            if (strtolower($u['email']) === strtolower($currentEmail)) {
                $u['nama'] = $nama;
                if ($newPassword !== '') {
                    $u['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
                }
            }
        }
        unset($u);

        if (saveUsers($users)) {
            $_SESSION['user_name'] = $nama;
            setFlash('success', 'Profil berhasil diperbarui.');
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = 'Gagal menyimpan perubahan.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profil — Tugas Rutin 7</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="card">
    <h1>✏️ Edit Profil</h1>
    <p class="subtitle">Perbarui nama atau password Anda</p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $err): ?>
                <div><?= sanitize($err) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="edit_profile.php" novalidate>
        <div class="form-group">
            <label for="nama">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" value="<?= sanitize($user['nama'] ?? '') ?>" required minlength="3">
        </div>

        <div class="form-group">
            <label>Email (tidak dapat diubah)</label>
            <input type="text" value="<?= sanitize($user['email'] ?? '') ?>" disabled>
        </div>

        <div class="form-group">
            <label for="new_password">Password Baru (opsional)</label>
            <input type="password" id="new_password" name="new_password" placeholder="Kosongkan jika tidak diganti">
        </div>

        <div class="form-group">
            <label for="confirm_password">Konfirmasi Password Baru</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi password baru">
        </div>

        <button type="submit" class="btn-primary">Simpan Perubahan</button>
    </form>

    <p class="switch-link"><a href="dashboard.php">← Kembali ke Dashboard</a></p>
</div>
</body>
</html>
