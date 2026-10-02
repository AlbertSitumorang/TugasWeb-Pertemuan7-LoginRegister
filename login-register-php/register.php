<?php
require_once __DIR__ . '/includes/auth.php';

// Jika sudah login, tidak perlu registrasi lagi
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$old = ['nama' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = sanitize($_POST['nama'] ?? '');
    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? ''; // password tidak di-sanitize dengan htmlspecialchars agar tidak merusak karakter khusus
    $confirm  = $_POST['confirm_password'] ?? '';

    $old['nama']  = $nama;
    $old['email'] = $email;

    // 1. Validasi nama
    if ($nama === '') {
        $errors[] = 'Nama tidak boleh kosong.';
    } elseif (strlen($nama) < 3) {
        $errors[] = 'Nama minimal 3 karakter.';
    }

    // 2. Validasi email dengan filter_var()
    if ($email === '') {
        $errors[] = 'Email tidak boleh kosong.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }

    // 3. Validasi password
    if ($password === '') {
        $errors[] = 'Password tidak boleh kosong.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    } elseif ($password !== $confirm) {
        $errors[] = 'Konfirmasi password tidak cocok.';
    }

    // 4. Cek duplikasi email (hanya jika email valid untuk menghindari pesan ganda)
    if (empty($errors) && findUserByEmail($email) !== null) {
        $errors[] = 'Email sudah terdaftar. Silakan gunakan email lain atau login.';
    }

    // 5. Jika semua validasi lolos, simpan user baru
    if (empty($errors)) {
        $users = loadUsers();

        $newUser = [
            'id'             => uniqid('u_', true),
            'nama'           => $nama,
            'email'          => $email,
            'password'       => password_hash($password, PASSWORD_DEFAULT), // hash password
            'remember_token' => null,
            'created_at'     => date('Y-m-d H:i:s'),
        ];

        $users[] = $newUser;

        if (saveUsers($users)) {
            setFlash('success', 'Registrasi berhasil! Silakan login dengan akun Anda.');
            header('Location: login.php');
            exit;
        } else {
            $errors[] = 'Gagal menyimpan data. Coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register — Tugas Rutin 7</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="card">
    <h1>📝 Buat Akun</h1>
    <p class="subtitle">Daftar untuk mulai menggunakan sistem</p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $err): ?>
                <div><?= sanitize($err) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="register.php" novalidate>
        <div class="form-group">
            <label for="nama">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" value="<?= sanitize($old['nama']) ?>" placeholder="Nama Anda" required minlength="3">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= sanitize($old['email']) ?>" placeholder="nama@email.com" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required minlength="6">
        </div>

        <div class="form-group">
            <label for="confirm_password">Konfirmasi Password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi password" required minlength="6">
        </div>

        <button type="submit" class="btn-primary">Daftar</button>
    </form>

    <p class="switch-link">Sudah punya akun? <a href="login.php">Login di sini</a></p>
</div>
</body>
</html>
