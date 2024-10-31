<?php
session_start();

// Periksa apakah token dan action sudah diset dalam session sebelum memproses verifikasi
if (!isset($_SESSION['token']) || !isset($_SESSION['action'])) {
    echo "<script>alert('Akses tidak valid.'); window.location.href='landing_page.php';</script>";
    exit;
}

$message = '';
$icon = 'success'; // Default icon for SweetAlert
if (isset($_SESSION['success_message'])) {
    $message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
} elseif (isset($_SESSION['error_message'])) {
    $message = $_SESSION['error_message'];
    $icon = 'error';
    unset($_SESSION['error_message']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {
    $input_token = $_POST['token'];

    // Periksa apakah token yang dimasukkan cocok dengan token di session
    if ($input_token === $_SESSION['token']) {
        if ($_SESSION['action'] === 'login') {
            $message = 'Verifikasi berhasil!';
            $icon = 'success';
            header('Location: Home.php');
            exit();
        } else if ($_SESSION['action'] === 'password_reset') {
            $message = 'Token verifikasi berhasil! Silakan ubah kata sandi Anda.';
            $icon = 'success';
            header('Location: change_password.php');
            exit();
        }
    } else {
        $message = 'Token verifikasi salah.';
        $icon = 'error';
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="flex items-center justify-center h-screen bg-gray-900">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-4">Verifikasi Token</h2>
        <form method="POST" action="" onsubmit="return validateForm()">
            <input type="text" id="token" name="token" placeholder="Masukkan Kode Verifikasi" class="w-full px-4 py-2 mb-4 border rounded">
            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Verifikasi</button>
        </form>
    </div>

    <script>
        function validateForm() {
            const tokenInput = document.getElementById('token');
            if (tokenInput.value.trim() === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Kode Verifikasi tidak boleh kosong!',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return false; // Mencegah pengiriman form
            }
            return true; // Mengizinkan pengiriman form
        }

        // Menampilkan SweetAlert2 jika ada pesan
        <?php if ($message): ?>
            Swal.fire({
                icon: '<?php echo $icon; ?>',
                title: '<?php echo $icon === 'error' ? 'Error' : 'Success'; ?>',
                text: '<?php echo $message; ?>',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>
    </script>
</body>
</html>
