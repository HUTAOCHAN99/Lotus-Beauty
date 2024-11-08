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

// Ambil daftar pengguna (customer) yang menghubungi apoteker
$konek = getConnection();
$userQuery = "SELECT DISTINCT u.user_id, u.username FROM messages m JOIN users u ON m.user_id = u.user_id WHERE m.recipient_id = ?";
$userStmt = $konek->prepare($userQuery);
$userStmt->bind_param("i", $_SESSION['user_id']); // user_id apoteker dari session
$userStmt->execute();
$userResult = $userStmt->get_result();

// Ambil daftar customer service (CS)
$csQuery = "SELECT user_id, username FROM users WHERE role = 'cs'";
$csResult = $konek->query($csQuery);

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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_text']) && isset($_POST['recipient_id'])) {
    $messageText = $_POST['message_text'];
    $recipientId = $_POST['recipient_id'];  // recipient_id yang dipilih pengguna
    $userId = $_SESSION['user_id']; // ID pengguna yang sedang login
    $messageType = 'apoteker';  // Misalnya, set ini sesuai dengan peran pengguna saat ini (bisa 'dokter', 'apoteker', atau 'cs')

    // Memastikan recipient_id valid
    if ($recipientId && !empty($messageText)) {
        // Menyisipkan pesan ke database
        $insertQuery = "INSERT INTO messages (user_id, recipient_id, message_text, message_type, status, created_at) 
                        VALUES (?, ?, ?, ?, 'sent', NOW())";
        $insertStmt = $konek->prepare($insertQuery);
        $insertStmt->bind_param("iiss", $userId, $recipientId, $messageText, $messageType);

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
        .bg-powderBlue {
            background-color: #B0E0E6;
        }

        /* Atur posisi pesan menggunakan Flexbox */
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

        /* Gaya Scrollbar */
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

        /* Tombol Scroll */
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

            <h2 class="font-semibold mb-4 mt-4">Customer Service</h2>
            <?php while ($cs = $csResult->fetch_assoc()): ?>
                <a href="?user_id=<?php echo $cs['user_id']; ?>" class="flex items-center p-2 hover:bg-blue-100 rounded">
                    <div class="w-10 h-10 rounded-full bg-green-600 text-white flex items-center justify-center mr-3">
                        <?php echo strtoupper(substr($cs['username'], 0, 1)); ?>
                    </div>
                    <span><?php echo htmlspecialchars($cs['username']); ?></span>
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
                    echo "Chat dengan " . htmlspecialchars($selectedUser['username']);
                    $stmt->close();
                } else {
                    echo "Pilih pengguna untuk memulai chat.";
                }
                ?>
            </h2>

            <div class="flex-1 overflow-y-auto message-container" id="message-container">
                <?php
                if ($selectedId) {
                    while ($message = $messageResult->fetch_assoc()) {
                        $isUserMessage = ($message['user_id'] == $_SESSION['user_id']);
                        $messageClass = $isUserMessage ? 'message-user' : 'message-apoteker';
                        $rowClass = $isUserMessage ? 'flex-row-reverse' : 'flex-row';

                        echo "<div class='message-row $rowClass'>";
                        echo "<div class='$messageClass'>";
                        if (!$isUserMessage) {
                            echo "<p class='font-bold text-xs mb-1'>" . htmlspecialchars($message['username']) . "</p>";
                        }
                        echo "<p>" . htmlspecialchars($message['message_text']) . "</p>";
                        echo "<p class='text-xs text-gray-500'>" . date("H:i", strtotime($message['created_at'])) . "</p>";
                        echo "</div>";
                        echo "</div>";
                    }
                }
                ?>
            </div>

            <div id="scroll-button">
                <i class="ri-arrow-down-line text-xl"></i>
            </div>

            <!-- Area Input Pesan -->
            <div class="fixed w-1/2 bottom-0 p-2 bg-white border-t">
                <form method="POST" class="flex items-center" <?php echo $selectedId ? '' : 'style="display:none;"'; ?>>
                    <input type="hidden" name="recipient_id" value="<?php echo htmlspecialchars($selectedId); ?>" />
                    <input type="text" name="message_text" placeholder="Ketik di sini dan tekan enter.."
                        class="flex-1 px-4 py-2 border rounded-full text-sm focus:outline-none" required />
                    <button type="submit" class="ml-2 text-orange-500">
                        <i class="ri-send-plane-2-line text-xl"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const messageContainer = document.getElementById("message-container");
            const scrollButton = document.getElementById("scroll-button");

            function scrollToBottom() {
                messageContainer.scrollTop = messageContainer.scrollHeight;
            }

            function checkScrollPosition() {
                const atBottom = messageContainer.scrollHeight - messageContainer.scrollTop <= messageContainer.clientHeight + 10;
                scrollButton.style.display = atBottom ? "none" : "block";
            }

            messageContainer.addEventListener("scroll", checkScrollPosition);
            scrollButton.addEventListener("click", scrollToBottom);

            window.onload = () => {
                scrollToBottom();
                checkScrollPosition();
            };

            document.querySelector('form').addEventListener('submit', function () {
                setTimeout(() => {
                    scrollToBottom();
                    checkScrollPosition();
                }, 300);
            });
        });
    </script>

</body>
</html>
