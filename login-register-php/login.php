<?php
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$old = ['email' => ''];
$flash = getFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember_me']);

    $old['email'] = $email;

    if ($email === '' || $password === '') {
        $errors[] = 'Email dan password wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    } else {
        $user = findUserByEmail($email);

        if ($user === null || !password_verify($password, $user['password'])) {
            $errors[] = 'Email atau password salah.';
        } else {
            // Login sukses -> buat session
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name']  = $user['nama'];

            // Bonus: Remember Me via cookie
            if ($remember) {
                $token = bin2hex(random_bytes(32));

                $users = loadUsers();
                foreach ($users as &$u) {
                    if (strtolower($u['email']) === strtolower($user['email'])) {
                        $u['remember_token'] = $token;
                        break;
                    }
                }
                unset($u);
                saveUsers($users);

                // Cookie berlaku 30 hari
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
            }

            setFlash('success', 'Selamat datang kembali, ' . $user['nama'] . '!');
            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — Tugas Rutin 7</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="card">
    <h1>🔐 Masuk</h1>
    <p class="subtitle">Login ke akun Anda</p>

    <?php if ($flash): ?>
        <div class="alert alert-<?= sanitize($flash['type']) ?>"><?= sanitize($flash['message']) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $err): ?>
                <div><?= sanitize($err) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php" novalidate>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= sanitize($old['email']) ?>" placeholder="nama@email.com" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Password Anda" required>
        </div>

        <div class="checkbox-row">
            <input type="checkbox" id="remember_me" name="remember_me">
            <label for="remember_me" style="margin:0;">Ingat saya (Remember Me)</label>
        </div>

        <button type="submit" class="btn-primary">Login</button>
    </form>

    <p class="switch-link">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
</div>
</body>
</html>
