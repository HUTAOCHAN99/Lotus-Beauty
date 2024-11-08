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

// Ambil daftar pengguna (customer) yang mengirim pesan ke apoteker
$userQuery = "SELECT DISTINCT u.user_id, u.username 
              FROM messages m 
              JOIN users u ON m.user_id = u.user_id 
              WHERE m.recipient_id = ? OR m.user_id = ? 
              AND u.user_id != ?";
$userStmt = $konek->prepare($userQuery);
$userStmt->bind_param("iii", $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']);
$userStmt->execute();
$userResult = $userStmt->get_result();

// Mengambil pesan dari pengguna yang dipilih
$selectedUserId = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$type = isset($_GET['type']) ? $_GET['type'] : 'customer';

$messageQuery = "SELECT m.*, u.username FROM messages m 
                 JOIN users u ON m.user_id = u.user_id 
                 WHERE (m.user_id = ? AND m.recipient_id = ?) 
                    OR (m.user_id = ? AND m.recipient_id = ?) 
                 ORDER BY m.created_at ASC";
$messageStmt = $konek->prepare($messageQuery);
$messageStmt->bind_param("iiii", $_SESSION['user_id'], $selectedUserId, $selectedUserId, $_SESSION['user_id']);
$messageStmt->execute();
$messageResult = $messageStmt->get_result();

// Mengambil daftar dokter dan customer service
$apotekerQuery = "SELECT user_id, username FROM users WHERE role = 'apoteker'";
$apotekerResult = $konek->query($apotekerQuery);

$dokterQuery = "SELECT user_id, username FROM users WHERE role = 'dokter'";
$dokterResult = $konek->query($dokterQuery);

// Handle pengiriman pesan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_text'])) {
    $messageText = $_POST['message_text'];
    $recipientId = $_POST['user_id'];
    $apotekerId = $_SESSION['user_id'];
    $type = $_POST['type'];

    $insertQuery = "INSERT INTO messages (user_id, recipient_id, message_text, created_at) VALUES (?, ?, ?, NOW())";
    $insertStmt = $konek->prepare($insertQuery);
    $insertStmt->bind_param("iis", $apotekerId, $recipientId, $messageText);

    if ($insertStmt->execute()) {
        header("Location: cs.php?user_id=" . $recipientId . "&type=" . $type);
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
    <title>Apoteker Chat</title>
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

        #scroll-button {
            display: none;
            position: fixed;
            bottom: 80px;
            right: 20px;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.2);
        }

        #scroll-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="bg-powderBlue shadow-md p-4 flex justify-between items-center">
        <a href="Consultation_Page.php" class="text-black flex items-center space-x-2">
            <i class="ri-arrow-left-line text-xl"></i>
        </a>
        <h1 class="text-gray-800 font-bold text-lg">Chat Konsultasi</h1>
        <div class="w-10"></div>
    </nav>

    <div class="w-full max-w-4xl bg-white shadow-lg rounded-lg overflow-hidden flex">

        <!-- Sidebar untuk daftar pengguna -->
        <div class="w-1/3 bg-gray-50 p-4 h-screen overflow-y-auto">
            <?php if ($type === 'customer'): ?>
                <h2 class="font-semibold mb-4">Daftar Pengguna</h2>
                <?php while ($user = $userResult->fetch_assoc()): ?>
                    <a href="?user_id=<?php echo $user['user_id']; ?>&type=customer" class="block p-2 hover:bg-blue-100">
                        <span><?php echo htmlspecialchars($user['username']); ?></span>
                    </a>
                <?php endwhile; ?>
            <?php elseif ($type === 'dokter'): ?>
                <h2 class="font-semibold mb-4">Daftar Dokter</h2>
                <?php while ($dokter = $dokterResult->fetch_assoc()): ?>
                    <a href="?user_id=<?php echo $dokter['user_id']; ?>&type=dokter" class="block p-2 hover:bg-blue-100">
                        <span><?php echo htmlspecialchars($dokter['username']); ?></span>
                    </a>
                <?php endwhile; ?>
            <?php elseif ($type === 'apoteker'): ?>
                <h2 class="font-semibold mb-4">Daftar Apoteker</h2>
                <?php while ($apoteker = $apotekerResult->fetch_assoc()): ?>
                    <a href="?user_id=<?php echo $apoteker['user_id']; ?>&type=apoteker" class="block p-2 hover:bg-blue-100">
                        <span><?php echo htmlspecialchars($apoteker['username']); ?></span>
                    </a>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <!-- Area Pesan -->
        <div class="w-2/3 p-4 h-screen overflow-y-auto">
            <?php
            // Ambil username pengguna yang dipilih, jika ada
            $selectedUser = null;
            if ($selectedUserId) {
                $selectedUserQuery = "SELECT username FROM users WHERE user_id = ?";
                $stmt = $konek->prepare($selectedUserQuery);
                $stmt->bind_param("i", $selectedUserId);
                $stmt->execute();
                $selectedUserResult = $stmt->get_result();
                $selectedUser = $selectedUserResult->fetch_assoc();
                $stmt->close();
            }
            ?>
            <div>
                <?php
                if ($selectedUser) {
                    echo "<h2>Chat dengan " . htmlspecialchars($selectedUser['username']) . "</h2>";
                } else {
                    echo "<h2>Pilih pengguna untuk memulai chat.</h2>";
                }
                ?>
            </div>

            <div class="p-4 bg-gray-50 h-80 overflow-y-auto" id="message-container">
                <?php while ($message = $messageResult->fetch_assoc()): ?>
                    <div
                        class="<?php echo ($message['user_id'] == $_SESSION['user_id']) ? 'message-apoteker' : 'message-user'; ?>">
                        <?php echo htmlspecialchars($message['message_text']); ?>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- Form kirim pesan -->
            <form method="POST" class="flex items-center p-2 border-t">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($selectedUserId); ?>" />
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>" />
                <input type="text" name="message_text" class="flex-1 border p-2" required placeholder="Ketik pesan...">
                <button type="submit" class="ml-2 text-orange-500">
                    <i class="ri-send-plane-2-line text-xl"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Scroll ke bawah -->
    <button id="scroll-button" onclick="scrollToBottom()">↓</button>

    <script>
        // Fungsi scroll ke bawah
        function scrollToBottom() {
            const messageContainer = document.getElementById('message-container');
            messageContainer.scrollTop = messageContainer.scrollHeight;
        }

        // Mengaktifkan tombol scroll jika tidak di bagian bawah
        document.getElementById('message-container').addEventListener('scroll', function () {
            const button = document.getElementById('scroll-button');
            const messageContainer = document.getElementById('message-container');
            button.style.display = messageContainer.scrollTop < messageContainer.scrollHeight - messageContainer.offsetHeight ? 'block' : 'none';
        });
    </script>

</body>

</html>