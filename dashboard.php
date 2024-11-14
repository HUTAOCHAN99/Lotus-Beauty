<?php
session_start();
include 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: Landing_Page.php");
    exit();
}

// Retrieve user information from the database
$username = $_SESSION['username'];
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $konek->query($sql);
$user = $result->fetch_assoc();

// Cek jika ada parameter 'success' di URL
$success = isset($_GET['success']) ? $_GET['success'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9fafb;
        }

        .dashboard-card {
            background-color: #ffffff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 1rem;
        }

        .card-title {
            color: #1f2937;
            font-weight: 600;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .nav-link {
            color: #3b82f6;
            font-weight: 500;
            transition: color 0.2s ease;
            display: flex;
            align-items: center;
            padding: 0.5rem 0;
        }

        .nav-link:hover {
            color: #2563eb;
        }

        .nav-icon {
            margin-right: 0.5rem;
            font-size: 1.25rem;
            /* Adjust icon size */
        }

        .back-button,
        .logout-button {
            font-weight: 500;
            transition: background-color 0.2s ease;
        }

        .back-button:hover,
        .logout-button:hover {
            background-color: #374151;
        }

        @media (max-width: 768px) {
            .dashboard-card {
                padding: 1rem;
            }

            .card-title {
                font-size: 1.25rem;
            }
        }
    </style>

    <script>
        // Check if success parameter is set and show alert
        window.onload = function () {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success')) {
                alert("Data berhasil ditambahkan!");
            }
        };
    </script>
</head>

<body class="flex items-center justify-center min-h-screen">
    <div class="max-w-4xl w-full mx-auto p-6">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Dashboard</h2>
            <p class="text-gray-500">Selamat datang, <?php echo htmlspecialchars($user['full_name']); ?>!</p>
        </div>

        <!-- User Profile Information -->
        <div class="dashboard-card">
            <h3 class="card-title">Informasi Profil</h3>
            <table class="min-w-full bg-white">
                <tbody>
                    <tr>
                        <td class="py-2 px-4 border-b"><strong>Nama Lengkap</strong></td>
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($user['full_name']); ?></td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 border-b"><strong>Username</strong></td>
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($user['username']); ?></td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 border-b"><strong>Email</strong></td>
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($user['email']); ?></td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 border-b"><strong>Nomor Telepon</strong></td>
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($user['phone_number']); ?></td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 border-b"><strong>Alamat</strong></td>
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($user['address']); ?></td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 border-b"><strong>Peran</strong></td>
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($user['role']); ?></td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 border-b"><strong>Status Akun</strong></td>
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($user['status']); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Navigation Links -->
        <div class="dashboard-card">
            <h3 class="card-title">Navigasi</h3>
            <ul class="space-y-2 pl-">
                <li><a href="profile.php" class="nav-link"><i class="ri-user-fill nav-icon"></i> Pengaturan Profil</a>
                </li>
                <li><a href="transaction.php" class="nav-link"><i class="ri-history-fill nav-icon"></i> Riwayat
                        Transaksi</a></li>
                <li><a href="setting.php" class="nav-link"><i class="ri-settings-3-fill nav-icon"></i> Pengaturan
                        Akun</a></li>

                <!-- Additional options for admin -->
                <?php if ($user['role'] == 'admin'): ?>
                    <!-- Hanya admin yang dapat melihat Manajemen Pengguna -->
                    <li><a href="user_management.php" class="nav-link"><i class="ri-admin-fill nav-icon"></i> Manajemen
                            Pengguna</a></li>
                <?php endif; ?>

                <?php if ($user['role'] == 'admin' || $user['role'] == 'dokter'): ?>
                    <!-- Hanya admin yang dapat melihat Manajemen Pengguna -->
                    <li><a href="product_management.php" class="nav-link"><i class="ri-file-edit-line nav-icon"></i> Manajemen
                            Produk</a></li>
                <?php endif; ?>

                <?php if ($user['role'] == 'admin' || $user['role'] == 'cs'): ?>
                    <!-- Admin dan customer dapat melihat Laporan dan Statistik -->
                    <li><a href="Reports.php" class="nav-link"><i class="ri-file-list-3-fill nav-icon"></i> Laporan dan
                            Statistik</a></li>
                <?php endif; ?>

                <?php if ($user['role'] != 'customer'): ?>
                    <div class="dashboard-card flex space-x-4 mt-6 p-4 border border-gray-300 rounded-lg shadow-md">
                        <a href="upload.php?category=resep"
                            class="flex items-center bg-blue-500 text-white rounded-lg px-4 py-2 transition duration-300 ease-in-out hover:bg-blue-600 transform hover:scale-105">
                            <i class="ri-upload-2-line mr-2"></i> <!-- Ikon untuk Upload Resep -->
                            Upload Resep
                        </a>
                        <a href="upload.php?category=obat-herbal"
                            class="flex items-center bg-green-500 text-white rounded-lg px-4 py-2 transition duration-300 ease-in-out hover:bg-green-600 transform hover:scale-105">
                            <i class="ri-upload-2-line mr-2"></i> <!-- Ikon untuk Upload Obat Herbal -->
                            Upload Obat Herbal
                        </a>
                    </div>
                <?php endif; ?>

            </ul>
        </div>

        <!-- Account Settings and Security -->
        <div
            class="dashboard-card flex items-center justify-between p-4 border border-gray-300 rounded-lg shadow-md mt-6">
            <a href="logout.php"
                class="flex items-center text-red-600 bg-red-100 hover:bg-red-500 rounded-md px-4 py-2 transition duration-300 ease-in-out">
                <i class="ri-logout-box-line mr-2"></i> <!-- Ikon Logout -->
                Logout
            </a>
            <a href="Home.php"
                class="flex items-center bg-gray-800 text-white rounded-md px-4 py-2 hover:bg-gray-700 transition duration-300 ease-in-out">
                <i class="ri-home-2-line mr-2"></i> <!-- Ikon Kembali ke Halaman Utama -->
                Kembali ke Halaman Utama
            </a>
        </div>

    </div>
</body>

</html>