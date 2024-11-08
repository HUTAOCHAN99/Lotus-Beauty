<?php
ob_start(); // Mulai output buffering
session_start();

// Koneksi ke database
function getConnection()
{
    $hostname = "localhost";
    $username_db = "root";
    $password_db = "";
    $database = "lotusbeauty";
    $konek = new mysqli($hostname, $username_db, $password_db, $database);

    // Periksa koneksi ke database
    if ($konek->connect_error) {
        die("Koneksi gagal: " . $konek->connect_error);
    }
    return $konek;
}
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Memeriksa apakah pengguna sudah login
if (empty($username)) {
    echo "Anda harus login untuk mengakses halaman ini.";
    exit;
}

// Ambil role pengguna dari database
$konek = getConnection();
$roleQuery = "SELECT role FROM users WHERE username = ?";
$roleStmt = $konek->prepare($roleQuery);
$roleStmt->bind_param("s", $username);
$roleStmt->execute();
$roleResult = $roleStmt->get_result();
$roleRow = $roleResult->fetch_assoc();
$userRole = $roleRow['role']; // Menyimpan role pengguna
$roleStmt->close();

// Ambil daftar pengguna (customer, dokter, dan cs) berdasarkan role
if ($userRole == 'apoteker') {
    $userQuery = "SELECT DISTINCT u.user_id, u.username 
                  FROM messages m 
                  JOIN users u ON m.user_id = u.user_id 
                  WHERE (m.recipient_id = ? OR m.user_id = ?) 
                  AND u.role IN ('customer', 'cs') 
                  AND u.user_id != ?";
} else {
    // Jika pengguna adalah dokter atau role lain, ambil sesuai role mereka
    $userQuery = "SELECT DISTINCT u.user_id, u.username 
                  FROM messages m 
                  JOIN users u ON m.user_id = u.user_id 
                  WHERE (m.recipient_id = ? OR m.user_id = ?) 
                  AND u.role != ? 
                  AND u.user_id != ?";
}

$userStmt = $konek->prepare($userQuery);
$userStmt->bind_param("iiii", $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']);
$userStmt->execute();
$userResult = $userStmt->get_result();

// Mengambil pesan dari pengguna yang dipilih
$selectedUserId = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$messageQuery = "SELECT m.*, u.username FROM messages m JOIN users u ON m.user_id = u.user_id 
                 WHERE (m.user_id = ? AND m.recipient_id = ?) OR (m.user_id = ? AND m.recipient_id = ?) 
                 ORDER BY m.created_at ASC";
$messageStmt = $konek->prepare($messageQuery);
$messageStmt->bind_param("iiii", $_SESSION['user_id'], $selectedUserId, $selectedUserId, $_SESSION['user_id']);
$messageStmt->execute();
$messageResult = $messageStmt->get_result();

// Pastikan untuk menutup semua statement sebelum menutup koneksi
$userStmt->close();
$messageStmt->close();

// Handle the message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_text'])) {
    $messageText = $_POST['message_text'];
    $recipientId = $_POST['user_id']; // recipient_id yang dipilih pengguna
    $userId = $_SESSION['user_id']; // ID pengguna yang sedang login

    // Memastikan recipient_id valid
    if ($recipientId && !empty($messageText)) {
        // Menyisipkan pesan ke database
        $insertQuery = "INSERT INTO messages (user_id, recipient_id, message_text, message_type, status, created_at) 
                        VALUES (?, ?, ?, ?, 'sent', NOW())";
        $insertStmt = $konek->prepare($insertQuery);
        $insertStmt->bind_param("iiss", $userId, $recipientId, $messageText, 'apoteker');

        if ($insertStmt->execute()) {
            // Setelah pesan terkirim, arahkan ulang ke halaman chat dengan penerima yang sama
            header("Location: apoteker.php?user_id=" . $recipientId);
            exit();
        } else {
            echo "Terjadi kesalahan: " . $insertStmt->error;
        }

        $insertStmt->close();
    } else {
        echo "Pesan atau penerima tidak valid.";
    }
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

    <div class="flex min-h-screen bg-white shadow-lg rounded-lg overflow-hidden">

        <!-- Sidebar Pengguna -->
        <div class="w-1/3 bg-gray-50 p-4 h-screen overflow-y-auto">
            <h2 class="font-semibold mb-4">Pengguna</h2>
            <?php while ($user = $userResult->fetch_assoc()): ?>
                <a href="?user_id=<?php echo $user['user_id']; ?>" class="flex items-center p-2 hover:bg-blue-100 rounded">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center mr-3">
                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                    </div>
                    <span><?php echo htmlspecialchars($user['username']); ?></span>
                </a>
            <?php endwhile; ?>
        </div>

        <!-- Area Pesan -->
        <div class="w-2/3 h-screen flex flex-col">
            <h2 class="font-semibold mb-4 text-xl">
                <?php
                $selectedId = $selectedUserId ?? null;
                if ($selectedId) {
                    $stmt = $konek->prepare("SELECT username FROM users WHERE user_id = ?");
                    $stmt->bind_param("i", $selectedId);
                    $stmt->execute();
                    $selectedUserResult = $stmt->get_result();
                    $selectedUser = $selectedUserResult->fetch_assoc();
                    echo htmlspecialchars($selectedUser['username']);
                }
                ?>
            </h2>

            <!-- Tampilan Pesan -->
            <div class="message-container flex-1 p-4 overflow-y-auto" id="message-container">
                <?php while ($message = $messageResult->fetch_assoc()): ?>
                    <div class="message-row <?php echo $message['user_id'] == $_SESSION['user_id'] ? 'justify-end' : ''; ?>">
                        <div class="<?php echo $message['user_id'] == $_SESSION['user_id'] ? 'message-apoteker' : 'message-user'; ?>">
                            <?php echo htmlspecialchars($message['message_text']); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- Input Pesan -->
            <form method="POST" class="p-4 bg-gray-50 border-t-2">
                <input type="hidden" name="user_id" value="<?php echo $selectedUserId; ?>">
                <textarea name="message_text" class="w-full p-2 border rounded" rows="3" placeholder="Ketik pesan..."></textarea>
                <button type="submit" class="mt-2 w-full bg-blue-500 text-white p-2 rounded">Kirim Pesan</button>
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
        document.getElementById('message-container').addEventListener('scroll', function() {
            const button = document.getElementById('scroll-button');
            const messageContainer = document.getElementById('message-container');
            button.style.display = messageContainer.scrollTop < messageContainer.scrollHeight - messageContainer.offsetHeight ? 'block' : 'none';
        });
    </script>

</body>
</html>
