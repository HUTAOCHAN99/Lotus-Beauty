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

    $stmt = $konek->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        if (password_verify($old_password, $row['password'])) {
            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

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
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
</head>

<body class="flex items-center justify-center h-screen bg-gray-900">
    <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Ganti Kata Sandi</h2>
        <form method="POST" action="">
            <!-- Input Kata Sandi Lama -->
            <div class="relative mb-4">
                <input type="password" id="old_password" name="old_password" placeholder="Kata Sandi Lama" class="w-full px-4 py-2 border rounded focus:outline-none" required>
                <button type="button" onclick="togglePasswordVisibility('old_password')" class="absolute inset-y-0 right-3 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <i id="old_password_icon" class="ri-eye-line"></i>
                </button>
            </div>

            <!-- Input Kata Sandi Baru -->
            <div class="relative mb-4">
                <input type="password" id="new_password" name="new_password" placeholder="Kata Sandi Baru" class="w-full px-4 py-2 border rounded focus:outline-none" required>
                <button type="button" onclick="togglePasswordVisibility('new_password')" class="absolute inset-y-0 right-3 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <i id="new_password_icon" class="ri-eye-line"></i>
                </button>
            </div>

            <!-- Input Konfirmasi Kata Sandi Baru -->
            <div class="relative mb-4">
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Kata Sandi Baru" class="w-full px-4 py-2 border rounded focus:outline-none" required>
                <button type="button" onclick="togglePasswordVisibility('confirm_password')" class="absolute inset-y-0 right-3 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <i id="confirm_password_icon" class="ri-eye-line"></i>
                </button>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Ganti Kata Sandi</button>
        </form>
    </div>

    <script>
        function togglePasswordVisibility(id) {
            const input = document.getElementById(id);
            const icon = document.getElementById(`${id}_icon`);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('ri-eye-line');
                icon.classList.add('ri-eye-off-line');
            } else {
                input.type = 'password';
                icon.classList.remove('ri-eye-off-line');
                icon.classList.add('ri-eye-line');
            }
        }
    </script>
</body>

</html>

