<?php
session_start();

// Periksa apakah token dan action sudah diset dalam session sebelum memproses verifikasi
if (!isset($_SESSION['token']) || !isset($_SESSION['action'])) {
    echo "<script>alert('Akses tidak valid.'); window.location.href='landing_page.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {
    $input_token = $_POST['token'];

    // Periksa apakah token yang dimasukkan cocok dengan token di session
    if ($input_token === $_SESSION['token']) {
        if ($_SESSION['action'] === 'login') {
            echo "<script>alert('Verifikasi berhasil!'); window.location.href='Home.php';</script>";
        } else if ($_SESSION['action'] === 'password_reset') {
            echo "<script>alert('Token verifikasi berhasil! Silakan ubah kata sandi Anda.'); window.location.href='change_password.php';</script>";
        }
    } else {
        echo "<script>alert('Token verifikasi salah.'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Token</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="flex items-center justify-center h-screen bg-gray-900">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-4">Verifikasi Token</h2>
        <form method="POST" action="">
            <input type="text" name="token" placeholder="Masukkan Kode Verifikasi" class="w-full px-4 py-2 mb-4 border rounded" required>
            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Verifikasi</button>
        </form>
    </div>
</body>
</html>
