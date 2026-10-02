<?php
/**
 * includes/auth.php
 * Kumpulan fungsi bantu untuk sistem Login/Register (Tugas Rutin 7)
 * Semua data pengguna disimpan di data/users.json
 */

session_start();

define('USERS_FILE', __DIR__ . '/../data/users.json');

/**
 * Membaca seluruh data user dari file JSON.
 * Mengembalikan array asosiatif (list of users).
 */
function loadUsers(): array
{
    if (!file_exists(USERS_FILE)) {
        file_put_contents(USERS_FILE, json_encode([]));
    }

    $json = file_get_contents(USERS_FILE);
    $data = json_decode($json, true);

    return is_array($data) ? $data : [];
}

/**
 * Menyimpan seluruh data user ke file JSON.
 * Menggunakan LOCK_EX agar aman dari race condition sederhana.
 */
function saveUsers(array $users): bool
{
    $json = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents(USERS_FILE, $json, LOCK_EX) !== false;
}

/**
 * Mencari user berdasarkan email. Mengembalikan array user atau null.
 */
function findUserByEmail(string $email): ?array
{
    $users = loadUsers();
    foreach ($users as $user) {
        if (strtolower($user['email']) === strtolower($email)) {
            return $user;
        }
    }
    return null;
}

/**
 * Membersihkan input dari user (sanitasi dasar).
 */
function sanitize(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Mengecek apakah user sedang login.
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_email']);
}

/**
 * Mewajibkan user untuk login. Jika belum login, redirect ke login.php.
 * Juga menangani auto-login via cookie "Remember Me".
 */
function requireLogin(): void
{
    if (isLoggedIn()) {
        return;
    }

    // Coba auto-login dari cookie remember_me (bonus feature)
    if (isset($_COOKIE['remember_token'])) {
        $users = loadUsers();
        foreach ($users as $user) {
            if (isset($user['remember_token']) && hash_equals($user['remember_token'], $_COOKIE['remember_token'])) {
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name']  = $user['nama'];
                return;
            }
        }
    }

    header('Location: login.php');
    exit;
}

/**
 * Menyimpan pesan flash (sukses/error) ke session untuk ditampilkan
 * setelah redirect (pola Post/Redirect/Get).
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Mengambil dan menghapus pesan flash dari session.
 */
function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}
