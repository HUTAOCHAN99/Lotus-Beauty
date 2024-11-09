<?php
ob_start();
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

// Ambil daftar pengguna yang pernah mengirim atau menerima pesan dari apoteker
$userQuery = "SELECT DISTINCT u.user_id, u.username 
              FROM messages m 
              JOIN users u ON m.user_id = u.user_id 
              WHERE (m.recipient_id = ? OR m.user_id = ?) 
                AND u.user_id != ?
                AND u.role = 'customer'"; // Pastikan hanya mengambil pengguna dengan role customer
$userStmt = $konek->prepare($userQuery);
$userStmt->bind_param("iii", $_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']);
$userStmt->execute();
$userResult = $userStmt->get_result();

// Mengambil daftar customer service
$csQuery = "SELECT user_id, username FROM users WHERE role = 'cs'";
$csResult = $konek->query($csQuery);

// Mengambil daftar dokter
$dokterQuery = "SELECT user_id, username FROM users WHERE role = 'dokter'";
$dokterResult = $konek->query($dokterQuery);

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
        header("Location: apoteker.php?user_id=" . $recipientId . "&type=" . $type);
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
    <title>Chat Apoteker</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        /* Warna latar belakang */
        .bg-powderBlue {
            background-color: #B0E0E6;
        }

        /* Styling Pesan */
        .message-row {
            display: flex;
            margin-bottom: 10px;
        }

        .message-user,
        .message-apoteker {
            display: inline-block;
            padding: 10px;
            border-radius: 15px;
            word-wrap: break-word;
            max-width: 80%;
            line-height: 1.5;
        }

        .message-user {
            background-color: #f0f9ff;
            text-align: right;
            align-self: flex-end;
            margin-left: auto;
        }

        .message-apoteker {
            background-color: #e0f7fa;
            align-self: flex-start;
            margin-right: auto;
        }

        /* Tombol Scroll ke bawah */
        .scroll-bottom-btn {
            position: absolute;
            bottom: 80px;
            right: 20px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            z-index: 1010;
        }

        #chatContent {
            max-height: calc(100vh - 160px);
            overflow-y: scroll;
        }

        #chatContent::-webkit-scrollbar {
            width: 8px;
        }

        #chatContent::-webkit-scrollbar-thumb {
            background-color: #4A5568;
            border-radius: 8px;
        }

        #chatContent::-webkit-scrollbar-track {
            background: #EDF2F7;
        }

        /* Menyembunyikan arrows atau tombol pada scrollbar */
        #chatContent::-webkit-scrollbar-button {
            display: none;
        }
    </style>
</head>

<body class="max-h-100vh">
    <nav class="bg-powderBlue shadow-md p-4 flex justify-between items-center">
        <a href="Consultation_Page.php" class="text-black flex items-center space-x-2">
            <i class="ri-arrow-left-line text-xl"></i>
        </a>
        <h1 class="text-gray-800 font-bold text-lg">Chat Konsultasi</h1>
        <div class="w-10"></div>
    </nav>

    <div class="w-full max-w-8xl bg-white shadow-lg rounded-lg overflow-hidden flex">
        <!-- Daftar Chat (Sidebar) -->
        <div class="w-1/3 bg-gray-50 p-4 h-screen overflow-y-auto">
            <?php $type = isset($_GET['type']) ? $_GET['type'] : 'dokter'; ?>
            <h2 class="font-semibold mb-4">Daftar Chat</h2>
            <?php if ($type === 'customer') { ?>
                <h3 class="font-semibold mt-2">Customer</h3>
                <?php while ($user = $userResult->fetch_assoc()): ?>
                    <a href="?user_id=<?php echo $user['user_id']; ?>&type=customer"
                        class="flex items-center p-2 hover:bg-blue-100 rounded">
                        <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center mr-3">
                            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                        </div>
                        <span><?php echo htmlspecialchars($user['username']); ?></span>
                    </a>
                <?php endwhile;
            } elseif ($type === 'dokter') { ?>
                <h3 class="font-semibold mt-2">dokter</h3>
                <?php while ($dokter = $dokterResult->fetch_assoc()): ?>
                    <a href="?user_id=<?php echo $dokter['user_id']; ?>&type=dokter"
                        class="flex items-center p-2 hover:bg-green-100 rounded">
                        <div class="w-10 h-10 rounded-full bg-green-600 text-white flex items-center justify-center mr-3">
                            <?php echo strtoupper(substr($dokter['username'], 0, 1)); ?>
                        </div>
                        <span><?php echo htmlspecialchars($dokter['username']); ?></span>
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
                <?php endwhile;
            } ?>
        </div>

        <!-- Area Pesan -->
        <div class="w-2/3 p-4 overflow-y-auto h-screen flex flex-col">
            <h2 class="font-semibold mb-0">
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

            <div class="p-4 h-full bg-gray-100 overflow-y-auto flex-grow scrollbar-thin scrollbar-thumb-rounded scrollbar-thumb-gray-400 scrollbar-track-gray-200"
                id="chatContent">
                <?php while ($message = $messageResult->fetch_assoc()): ?>
                    <div class="message-row flex">
                        <div
                            class="<?php echo ($message['user_id'] == $_SESSION['user_id']) ? 'message-user' : 'message-apoteker'; ?> px-4 py-2 rounded-lg max-w-4/5 break-words">
                            <?php echo htmlspecialchars($message['message_text']); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <div class="fixed bottom-0 left-0 right-0 flex items-center justify-between bg-white p-4 z-50">
        <!-- Tombol Scroll to Bottom -->
        <button
            class="scroll-bottom-btn rounded-full p-3 bg-blue-500 text-white w-12 h-12 flex items-center justify-center"
            id="scrollToBottomBtn" onclick="scrollToBottom()">
            <i class="ri-arrow-down-line text-xl"></i>
        </button>

        <!-- Form Input Pesan -->
        <form method="POST" class="flex items-center w-3/5 absolute bottom-0 right-10 items-center p-[10px]"
            <?php echo $selectedUserId ? '' : 'style="display:none;"'; ?> onsubmit="scrollToBottom(); return true;">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($selectedUserId); ?>" />
            <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>" />
            <input type="text" name="message_text" placeholder="Tulis pesan..." class="flex-1 p-2 rounded border mr-2"
                required>
            <button type="submit" class="ml-2 text-orange-500">
                <i class="ri-send-plane-2-line text-xl"></i>
            </button>
        </form>
    </div>

    <script>
        const chatContent = document.getElementById("chatContent");
        const scrollToBottomBtn = document.getElementById("scrollToBottomBtn");

        function scrollToBottom() {
            const lastMessage = chatContent.lastElementChild;
            if (lastMessage) {
                lastMessage.scrollIntoView({ behavior: 'smooth', block: 'end' });
            }
        }

        chatContent.addEventListener("scroll", () => {
            if (chatContent.scrollTop + chatContent.clientHeight < chatContent.scrollHeight - 20) {
                scrollToBottomBtn.style.display = "block";
            } else {
                scrollToBottomBtn.style.display = "none";
            }
        });

        window.onload = scrollToBottom;
    </script>
</body>

</html>
