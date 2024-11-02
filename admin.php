<?php
session_start();

// Koneksi ke database
function getConnection()
{
    $hostname = "localhost";
    $username_db = "root";
    $password_db = "";
    $database = "lotusbeauty";
    $konek = new mysqli($hostname, $username_db, $password_db, $database);
    if ($konek->connect_error) {
        die("Koneksi gagal: " . $konek->connect_error);
    }
    return $konek;
}

// Ambil username dari session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Memeriksa apakah pengguna sudah login
if (empty($username)) {
    echo "Anda harus login untuk mengakses halaman ini.";
    exit;
}

// Memastikan pengguna adalah admin
$konek = getConnection();
$query = "SELECT role FROM users WHERE username = ?";
$stmt = $konek->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($userRole);
$stmt->fetch();
$stmt->close();

if ($userRole !== 'admin') {
    echo "Hanya admin yang dapat mengakses halaman ini.";
    exit;
}

// Ambil daftar semua pengguna
$userQuery = "SELECT user_id, username, role FROM users";
$userResult = $konek->query($userQuery);

// Ambil riwayat chat
$chatQuery = "SELECT m.*, u.username as user_username, r.username as recipient_username FROM messages m 
              JOIN users u ON m.user_id = u.user_id 
              JOIN users r ON m.recipient_id = r.user_id 
              ORDER BY m.created_at DESC";
$chatResult = $konek->query($chatQuery);

// Tutup koneksi
$konek->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-4xl bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="p-4">
            <h2 class="font-semibold mb-4">Riwayat Chat</h2>
            <table class="w-full mb-4">
                <thead>
                    <tr>
                        <th class="border px-4 py-2">Pengirim</th>
                        <th class="border px-4 py-2">Penerima</th>
                        <th class="border px-4 py-2">Pesan</th>
                        <th class="border px-4 py-2">Tanggal</th>
                        <th class="border px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($chat = $chatResult->fetch_assoc()): ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($chat['user_username']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($chat['recipient_username']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($chat['message_text']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($chat['created_at']); ?></td>
                            <td class="border px-4 py-2">
                                <form method="POST" action="delete_message.php">
                                    <input type="hidden" name="message_id" value="<?php echo $chat['message_id']; ?>">
                                    <button type="submit" class="text-red-500 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>
