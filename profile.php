<?php
session_start();
include 'db.php'; // Pastikan ini mengarah ke file koneksi database yang benar

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

// Proses untuk mengupdate profil
$message = '';
$showAlert = false; // Tambahkan variabel untuk menampilkan alert
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];

    // Validasi input
    $errors = [];
    if (empty($full_name)) {
        $errors['full_name'] = "Nama Lengkap tidak boleh kosong.";
    }
    if (empty($email)) {
        $errors['email'] = "Email tidak boleh kosong.";
    }
    if (empty($phone_number)) {
        $errors['phone_number'] = "Nomor Telepon tidak boleh kosong.";
    }
    if (empty($address)) {
        $errors['address'] = "Alamat tidak boleh kosong.";
    }

    // Jika tidak ada error, update ke database
    if (empty($errors)) {
        $update_sql = "UPDATE users SET full_name=?, email=?, phone_number=?, address=? WHERE username=?";
        $stmt = $konek->prepare($update_sql);
        $stmt->bind_param("sssss", $full_name, $email, $phone_number, $address, $username);
        
        if ($stmt->execute()) {
            $message = "Profil berhasil diperbarui!";
            $showAlert = true; // Set untuk menampilkan alert
        } else {
            $message = "Terjadi kesalahan saat memperbarui profil.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            background: url('./src/images/background/bg-create-account.jpg') no-repeat center center fixed;
            background-size: cover;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen p-4">
    <div class="relative z-10 max-w-md w-full mx-auto p-8 bg-white bg-opacity-30 backdrop-blur-md shadow-lg rounded-lg border border-white/20">
        <h2 class="text-center text-2xl font-semibold mb-6 text-black">Pengaturan Profil</h2>
        <div class="profile-card p-6 rounded-lg shadow-lg">
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" class="form-input border border-gray-300 rounded-md p-3 w-full">
                    <?php if (isset($errors['full_name'])): ?>
                        <span class="text-red-600 text-sm"><?php echo $errors['full_name']; ?></span>
                    <?php endif; ?>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-input border border-gray-300 rounded-md p-3 w-full">
                    <?php if (isset($errors['email'])): ?>
                        <span class="text-red-600 text-sm"><?php echo $errors['email']; ?></span>
                    <?php endif; ?>
                </div>
                <div class="mb-4">
                    <label for="phone_number" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                    <input type="text" name="phone_number" id="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" class="form-input border border-gray-300 rounded-md p-3 w-full">
                    <?php if (isset($errors['phone_number'])): ?>
                        <span class="text-red-600 text-sm"><?php echo $errors['phone_number']; ?></span>
                    <?php endif; ?>
                </div>
                <div class="mb-4">
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($user['address']); ?>" class="form-input border border-gray-300 rounded-md p-3 w-full">
                    <?php if (isset($errors['address'])): ?>
                        <span class="text-red-600 text-sm"><?php echo $errors['address']; ?></span>
                    <?php endif; ?>
                </div>
                <div class="flex justify-center">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded transition duration-200">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-6 flex justify-end">
            <a href="dashboard.php" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-200">Kembali ke Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Menampilkan alert jika perubahan berhasil
        <?php if ($showAlert): ?>
            Swal.fire({
                title: 'Sukses!',
                text: '<?php echo $message; ?>',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>
    </script>
</body>

</html>
