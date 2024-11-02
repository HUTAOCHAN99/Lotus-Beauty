<?php
// Fungsi untuk koneksi menggunakan PDO
function getConnection()
{
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "lotusbeauty";

    try {
        $pdo = new PDO("mysql:host=$hostname;dbname=$database;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Koneksi gagal: " . $e->getMessage());
    }
}

$pdo = getConnection();

// Fungsi untuk menonaktifkan atau ban pengguna
if (isset($_GET['action']) && $_GET['action'] === 'ban' && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Update status user menjadi 'inactive'
    $stmt = $pdo->prepare("UPDATE users SET status = 'inactive' WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // Redirect back to account management after the ban
    header("Location: account_management.php?ban_success=1");
    exit();
}

// Fungsi untuk menghapus akun pengguna
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    try {
        // Hapus semua ulasan yang terkait dengan pengguna
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Hapus pengguna dari database
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Redirect back to account management after deletion
        header("Location: account_management.php?delete_success=1");
        exit();
    } catch (PDOException $e) {
        // Redirect back with an error message
        header("Location: account_management.php?delete_error=1");
        exit();
    }
}

// Ambil data pengguna yang statusnya aktif
$stmt = $pdo->prepare("SELECT * FROM users WHERE status = 'active'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Manajemen Pengguna Aktif</title>
    <script>
        function confirmBan(userId) {
            Swal.fire({
                title: 'Yakin ingin menonaktifkan akun ini?',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Nonaktifkan',
                denyButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '?action=ban&user_id=' + userId;
                }
            });
        }

        function confirmDelete(userId) {
            Swal.fire({
                title: 'Yakin ingin menghapus akun ini?',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                denyButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '?action=delete&user_id=' + userId;
                }
            });
        }
    </script>
</head>

<body class="bg-gray-50 min-h-screen p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-semibold text-center mb-4">Daftar Pengguna Aktif</h1>

        <table class="min-w-full bg-white border border-gray-200 rounded-md">
            <thead>
                <tr class="text-left bg-gray-100">
                    <th class="px-4 py-2 border-b">ID Pengguna</th>
                    <th class="px-4 py-2 border-b">Username</th>
                    <th class="px-4 py-2 border-b">Email</th>
                    <th class="px-4 py-2 border-b">Nama Lengkap</th>
                    <th class="px-4 py-2 border-b">Peran</th>
                    <th class="px-4 py-2 border-b">Status</th>
                    <th class="px-4 py-2 border-b">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2"><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($user['username']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($user['email']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($user['full_name'] ?? '-'); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($user['role']); ?></td>
                        <td class="px-4 py-2 text-green-600"><?php echo htmlspecialchars($user['status']); ?></td>
                        <td class="px-4 py-2">
                            <a href="javascript:void(0);" class="text-red-500 hover:underline"
                                onclick="confirmBan('<?php echo $user['user_id']; ?>')">
                                Nonaktifkan
                            </a>
                            <span class="mx-1">|</span>
                            <a href="javascript:void(0);" class="text-red-500 hover:underline"
                                onclick="confirmDelete('<?php echo $user['user_id']; ?>')">
                                Hapus
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Success Messages -->
        <?php if (isset($_GET['ban_success'])): ?>
            <script>
                Swal.fire('Akun berhasil dinonaktifkan', '', 'success');
            </script>
        <?php elseif (isset($_GET['delete_success'])): ?>
            <script>
                Swal.fire('Akun berhasil dihapus', '', 'success');
            </script>
        <?php elseif (isset($_GET['delete_error'])): ?>
            <script>
                Swal.fire('Terjadi kesalahan saat menghapus akun. Pastikan tidak ada data terkait.', '', 'error');
            </script>
        <?php endif; ?>
    </div>
   
    <div class="text-center mt-8">
        <a href="user_management.php" class="text-sm text-gray-600 hover:text-gray-800">← Kembali ke user management</a>
    </div>
</body>

</html>