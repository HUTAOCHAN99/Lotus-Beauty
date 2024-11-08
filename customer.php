<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Koneksi ke database
$hostname = "localhost";
$username_db = "root";
$password_db = "";
$database = "lotusbeauty";
$konek = new mysqli($hostname, $username_db, $password_db, $database);

// Periksa koneksi ke database
if ($konek->connect_error) {
    die("Koneksi gagal: " . $konek->connect_error);
}

// Ambil daftar dokter, apoteker, dan customer service untuk ditampilkan kepada customer
$doctorQuery = "SELECT user_id, username FROM users WHERE role = 'dokter'";
$doctorResult = $konek->query($doctorQuery);

$apotekerQuery = "SELECT user_id, username FROM users WHERE role = 'apoteker'";
$apotekerResult = $konek->query($apotekerQuery);

$csQuery = "SELECT user_id, username FROM users WHERE role = 'cs'";
$csResult = $konek->query($csQuery);

// Mengambil pesan dari pengguna yang dipilih
// Ambil nilai `type` dari URL
$selectedType = isset($_GET['type']) ? $_GET['type'] : null;
$selectedUserId = isset($_GET['user_id']) ? $_GET['user_id'] : null;

// Periksa apakah `type` valid (dokter, apoteker, atau cs)
if (!in_array($selectedType, ['dokter', 'apoteker', 'cs'])) {
    die("Tipe pengguna tidak valid.");
}

// Ambil pesan berdasarkan `type` dan `user_id`
$messageQuery = "SELECT m.*, u.username FROM messages m 
                 JOIN users u ON m.user_id = u.user_id 
                 WHERE ((m.user_id = ? AND m.recipient_id = ?) 
                 OR (m.user_id = ? AND m.recipient_id = ?)) 
                 ORDER BY m.created_at ASC";
$messageStmt = $konek->prepare($messageQuery);
$messageStmt->bind_param("iiii", $_SESSION['user_id'], $selectedUserId, $selectedUserId, $_SESSION['user_id']);
$messageStmt->execute();
$messageResult = $messageStmt->get_result();

// Handle the message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_text'])) {
    $messageText = $_POST['message_text'];
    $recipientId = $_POST['user_id'];
    $type = $_POST['type']; // Ambil `type` dari POST
    $customerId = $_SESSION['user_id']; // Menggunakan customer sebagai pengirim

    $insertQuery = "INSERT INTO messages (user_id, recipient_id, message_text, created_at) VALUES (?, ?, ?, NOW())";
    $insertStmt = $konek->prepare($insertQuery);
    $insertStmt->bind_param("iis", $customerId, $recipientId, $messageText);

    if ($insertStmt->execute()) {
        // Redirect kembali ke halaman dengan `type` yang sesuai
        header("Location: customer.php?user_id=" . $recipientId . "&type=" . $type);
        exit();
    } else {
        echo "Error: " . $insertStmt->error;
    }

    $insertStmt->close();
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .message-row {
            display: flex;
            margin-bottom: 10px;
            max-width: 70%;
        }

        .message-user {
            background-color: #f0f9ff;
            padding: 10px;
            border-radius: 15px;
            word-wrap: break-word;
            flex-shrink: 0;
        }

        .message-apoteker {
            background-color: #e0f7fa;
            padding: 10px;
            border-radius: 15px;
            word-wrap: break-word;
            flex-shrink: 0;
        }

        .message-container::-webkit-scrollbar {
            width: 8px;
        }

        .message-container::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .message-container::-webkit-scrollbar-thumb {
            background-color: #c0c0c0;
            border-radius: 4px;
        }

        .message-container::-webkit-scrollbar-thumb:hover {
            background: #888;
        }
    </style>
</head>

<body class="bg-gray-100">
    <nav class="bg-powderBlue shadow-md p-4 flex justify-between items-center">
        <a href="Consultation_Page.php" class="text-black flex items-center space-x-2">
            <i class="ri-arrow-left-line text-xl"></i>
        </a>
        <h1 class="text-gray-800 font-bold text-lg">Chat Konsultasi</h1>
        <div class="w-10"></div>
    </nav>

    <div class="w-full max-w-4xl bg-white shadow-lg rounded-lg overflow-hidden flex">
        <div class="w-1/3 bg-gray-50 p-4 h-screen overflow-y-auto">
            <?php   $type = isset($_GET['type']) ? $_GET['type'] : 'dokter';?>
            <h2 class="font-semibold mb-4">Daftar Chat</h2>
            <?php
            // Cek tipe dari parameter URL
            if ($type === 'dokter') {?>
            <h3 class="font-semibold mt-2">Dokter</h3>
            <?php while ($dokter = $doctorResult->fetch_assoc()): ?>
                <a href="?user_id=<?php echo $dokter['user_id']; ?>&type=dokter"
                    class="flex items-center p-2 hover:bg-blue-100 rounded">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center mr-3">
                        <?php echo strtoupper(substr($dokter['username'], 0, 1)); ?>
                    </div>
                    <span><?php echo htmlspecialchars($dokter['username']); ?></span>
                </a>
            <?php endwhile; 
            } elseif ($type === 'apoteker') { ?>
            <h3 class="font-semibold mt-2">Apoteker</h3>
            <?php while ($apoteker = $apotekerResult->fetch_assoc()): ?>
                <a href="?user_id=<?php echo $apoteker['user_id']; ?>&type=apoteker"
                    class="flex items-center p-2 hover:bg-green-100 rounded">
                    <div class="w-10 h-10 rounded-full bg-green-600 text-white flex items-center justify-center mr-3">
                        <?php echo strtoupper(substr($apoteker['username'], 0, 1)); ?>
                    </div>
                    <span><?php echo htmlspecialchars($apoteker['username']); ?></span>
                </a>
                <?php endwhile; 
            } elseif ($type === 'cs') { ?>
            <h3 class="font-semibold mt-2">Customer Service</h3>
            <?php while ($cs = $csResult->fetch_assoc()): ?>
                <a href="?user_id=<?php echo $cs['user_id']; ?>&type=cs"
                    class="flex items-center p-2 hover:bg-orange-100 rounded">
                    <div class="w-10 h-10 rounded-full bg-orange-600 text-white flex items-center justify-center mr-3">
                        <?php echo strtoupper(substr($cs['username'], 0, 1)); ?>
                    </div>
                    <span><?php echo htmlspecialchars($cs['username']); ?></span>
                </a>
                <?php endwhile; }?>
        </div>

        <!-- Area Pesan -->
        <div class="w-2/3 p-4 h-screen overflow-y-auto">
            <h2 class="font-semibold mb-4">
                <?php
                if ($selectedUserId) {
                    $selectedUserQuery = "SELECT username FROM users WHERE user_id = ?";
                    $stmt = $konek->prepare($selectedUserQuery);
                    $stmt->bind_param("i", $selectedUserId);
                    $stmt->execute();
                    $selectedUserResult = $stmt->get_result();
                    $selectedUser = $selectedUserResult->fetch_assoc();
                    echo "Chat dengan " . htmlspecialchars($selectedUser['username']);
                    $stmt->close();
                } else {
                    echo "Pilih pengguna untuk memulai chat.";
                }
                ?>
            </h2>

            <div class="p-4 h-80 overflow-y-auto bg-gray-50">
                <?php while ($message = $messageResult->fetch_assoc()): ?>
                    <div
                        class="p-2 my-2 border-b <?php echo ($message['user_id'] == $_SESSION['user_id']) ? 'message-user' : 'message-apoteker'; ?>">
                        <?php echo htmlspecialchars($message['message_text']); ?>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- Area Input Pesan -->
            <form method="POST" class="flex items-center p-2 border-t" <?php echo $selectedUserId ? '' : 'style="display:none;"'; ?>>
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($selectedUserId); ?>" />
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($selectedType); ?>" />
                <input type="text" name="message_text" placeholder="Tulis pesan..."
                    class="flex-1 p-2 rounded border mr-2" required>
                <button type="submit" class="p-2 rounded bg-blue-500 text-white">Kirim</button>
            </form>

        </div>
    </div>
</body>

</html>