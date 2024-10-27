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
        }

        .nav-link:hover {
            color: #2563eb;
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
        window.onload = function() {
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
            <ul class="space-y-2 pl-4 list-disc">
                <li><a href="profile.php" class="nav-link">Pengaturan Profil</a></li>
                <li><a href="transactions.php" class="nav-link">Riwayat Transaksi</a></li>
                <li><a href="setting.php" class="nav-link">Pengaturan Akun</a></li>

                <!-- Additional options for admin -->
                <?php if ($user['role'] == 'admin'): ?>
                    <li><a href="user_management.php" class="nav-link">Manajemen Pengguna</a></li>
                    <li><a href="reports.php" class="nav-link">Laporan dan Statistik</a></li>
                    <!-- Place this code where you want the buttons to be displayed on the dashboard -->
                    <div class="dashboard-card flex space-x-4 mt-6">
                        <a href="upload.php?category=resep" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                            Upload Resep
                        </a>
                        <a href="upload.php?category=obat-herbal" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                            Upload Obat Herbal
                        </a>
                    </div>

                <?php endif; ?>
            </ul>
        </div>

        <!-- Account Settings and Security -->
        <div class="dashboard-card flex items-center justify-between">
            <a href="logout.php" class="logout-button text-red-600 hover:bg-red-500 bg-red-100 rounded-md px-4 py-2">Logout</a>
            <a href="Home.php" class="back-button bg-gray-800 text-white rounded-md px-4 py-2 hover:bg-gray-700">
                Kembali ke Halaman Utama
            </a>
        </div>
    </div>
</body>

</html>