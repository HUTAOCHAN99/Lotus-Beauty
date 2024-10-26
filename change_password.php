<?php
session_start();
if (!isset($_SESSION['reset_email'])) {
    header("Location: request_token.php");
    exit();
}

$hostname = "localhost";
$username_db = "root";
$password_db = "";
$database = "lotusbeauty";
$konek = new mysqli($hostname, $username_db, $password_db, $database);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_SESSION['reset_email'];

    // Ambil kata sandi yang ada di database untuk verifikasi
    $stmt = $konek->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verifikasi kata sandi lama
        if (password_verify($old_password, $row['password'])) {
            // Pastikan kata sandi baru dan konfirmasi kata sandi cocok
            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Perbarui kata sandi di database
                $stmt = $konek->prepare("UPDATE users SET password = ? WHERE email = ?");
                $stmt->bind_param("ss", $hashed_password, $email);

                if ($stmt->execute()) {
                    unset($_SESSION['token']);
                    unset($_SESSION['reset_email']);
                    echo "<script>alert('Password berhasil diubah.'); window.location.href='Landing_Page.php';</script>";
                } else {
                    echo "<script>alert('Terjadi kesalahan saat mengubah password.');</script>";
                }
            } else {
                echo "<script>alert('Kata sandi baru dan konfirmasi kata sandi tidak cocok.');</script>";
            }
        } else {
            echo "<script>alert('Kata sandi lama salah.');</script>";
        }
    } else {
        echo "<script>alert('Email tidak ditemukan.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Kata Sandi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>

<body class="flex items-center justify-center h-screen bg-gray-900">
    <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Ganti Kata Sandi</h2>
        <form method="POST" action="">
            <input type="password" name="old_password" placeholder="Kata Sandi Lama" class="w-full px-4 py-2 mb-4 border rounded" required>
            <input type="password" name="new_password" placeholder="Kata Sandi Baru" class="w-full px-4 py-2 mb-4 border rounded" required>
            <input type="password" name="confirm_password" placeholder="Konfirmasi Kata Sandi Baru" class="w-full px-4 py-2 mb-4 border rounded" required>
            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Ganti Kata Sandi</button>
        </form>
    </div>
</body>

</html>
