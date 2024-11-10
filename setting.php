<?php
session_start();
include 'db.php';

// Pastikan pengguna telah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ambil informasi pengguna dari database
$username = $_SESSION['username'];
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $konek->query($sql);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body class="bg-gray-50 text-gray-800">
    <div class="max-w-4xl mx-auto p-8 bg-white shadow rounded-lg mt-10 space-y-8">
        <h2 class="text-center text-2xl font-semibold mb-6">Pengaturan Akun</h2>

        <!-- Ubah Kata Sandi -->
        <div class="flex items-center mb-6 border-b border-gray-200 pb-4">
            <i class="ri-lock-password-line text-xl text-blue-500 mr-4"></i>
            <div>
                <h3 class="text-xl font-medium">Ubah Kata Sandi</h3>
                <p class="text-gray-500 text-sm">Ganti kata sandi akun Anda.</p>
                <a href="change_password.php" class="text-blue-600 hover:underline text-sm">Ubah Kata Sandi</a>
            </div>
        </div>

        <!-- Verifikasi Dua Langkah -->
        <div class="flex items-center mb-6 border-b border-gray-200 pb-4">
            <i class="ri-shield-keyhole-line text-xl text-green-500 mr-4"></i>
            <div>
                <h3 class="text-xl font-medium">Verifikasi Dua Langkah</h3>
                <p class="text-gray-500 text-sm">Aktifkan verifikasi dua langkah untuk meningkatkan keamanan akun Anda.</p>
                <a href="request_token.php" class="text-blue-600 hover:underline text-sm">Aktifkan Verifikasi Dua Langkah</a>
            </div>
        </div>

        <!-- Pengaturan Privasi -->
        <!-- <div class="flex items-center mb-6 border-b border-gray-200 pb-4">
            <i class="ri-user-settings-line text-xl text-purple-500 mr-4"></i>
            <div>
                <h3 class="text-xl font-medium">Pengaturan Privasi</h3>
                <p class="text-gray-500 text-sm">Kelola siapa yang dapat melihat informasi profil Anda.</p>
                <a href="privacy_settings.php" class="text-blue-600 hover:underline text-sm">Pengaturan Privasi</a>
            </div>
        </div> -->

        <!-- Kelola Email Pemberitahuan -->
        <!-- <div class="flex items-center mb-6 border-b border-gray-200 pb-4">
            <i class="ri-mail-settings-line text-xl text-yellow-500 mr-4"></i>
            <div>
                <h3 class="text-xl font-medium">Kelola Email Pemberitahuan</h3>
                <p class="text-gray-500 text-sm">Pilih notifikasi yang ingin Anda terima melalui email.</p>
                <a href="email_notifications.php" class="text-blue-600 hover:underline text-sm">Kelola Pemberitahuan Email</a>
            </div>
        </div> -->

        <!-- Riwayat Login -->
        <!-- <div class="flex items-center mb-6 border-b border-gray-200 pb-4">
            <i class="ri-time-line text-xl text-indigo-500 mr-4"></i>
            <div>
                <h3 class="text-xl font-medium">Riwayat Login</h3>
                <p class="text-gray-500 text-sm">Lihat riwayat login terakhir Anda.</p>
                <a href="login_history.php" class="text-blue-600 hover:underline text-sm">Lihat Riwayat Login</a>
            </div>
        </div> -->

        <!-- Aktivasi/Deaktivasi Akun -->
        <!-- <div class="flex items-center mb-6 border-b border-gray-200 pb-4">
            <i class="ri-toggle-line text-xl text-red-500 mr-4"></i>
            <div>
                <h3 class="text-xl font-medium">Aktivasi/Deaktivasi Akun</h3>
                <p class="text-gray-500 text-sm">Aktifkan atau nonaktifkan akun Anda sesuai kebutuhan.</p>
                <a href="toggle_account.php" class="text-red-600 hover:underline text-sm">Aktifkan/Nonaktifkan Akun</a>
            </div>
        </div> -->

        <!-- Tombol Kembali -->
        <div class="text-center mt-8">
            <a href="dashboard.php" class="text-sm text-gray-600 hover:text-gray-800">← Kembali ke Dashboard</a>
        </div>
    </div>
</body>

</html>
