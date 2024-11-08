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

// Ambil daftar pengguna (customer) yang mengirim pesan ke dokter
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
$messageQuery = "SELECT m.*, u.username FROM messages m JOIN users u ON m.user_id = u.user_id 
                 WHERE (m.user_id = ? AND m.recipient_id = ?) OR (m.user_id = ? AND m.recipient_id = ?) 
                 ORDER BY m.created_at ASC";
$messageStmt = $konek->prepare($messageQuery);
$messageStmt->bind_param("iiii", $_SESSION['user_id'], $selectedUserId, $selectedUserId, $_SESSION['user_id']);
$messageStmt->execute();
$messageResult = $messageStmt->get_result();

// Mengambil daftar apoteker dan customer service
$csQuery = "SELECT user_id, username FROM users WHERE role = 'cs'";
$csResult = $konek->query($csQuery);

$apotekerQuery = "SELECT user_id, username FROM users WHERE role = 'apoteker'";
$apotekerResult = $konek->query($apotekerQuery);

// Handle the message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_text'])) {
    $messageText = $_POST['message_text'];
    $recipientId = $_POST['user_id'];
    $doctorId = $_SESSION['user_id'];

    $insertQuery = "INSERT INTO messages (user_id, recipient_id, message_text, created_at) VALUES (?, ?, ?, NOW())";
    $insertStmt = $konek->prepare($insertQuery);
    $insertStmt->bind_param("iis", $doctorId, $recipientId, $messageText);

    if ($insertStmt->execute()) {
        header("Location: dokter.php?user_id=" . $recipientId);
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
    <title>Dokter Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .message-user {
            text-align: left;
            background-color: #f0f9ff;
        }

        .message-doctor {
            text-align: right;
            background-color: #e0f7fa;
        }
    </style>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-4xl bg-white shadow-lg rounded-lg overflow-hidden flex">

        <div class="w-1/3 bg-gray-50 p-4 h-screen overflow-y-auto">
            <?php
            // Cek tipe dari parameter URL
            $type = isset($_GET['type']) ? $_GET['type'] : 'dokter';

            if ($type === 'customer') {
                echo '<h2 class="font-semibold mb-4">Pengguna</h2>';
                while ($user = $userResult->fetch_assoc()) : ?>
                    <a href="?user_id=<?php echo $user['user_id']; ?>&type=customer" class="flex items-center p-2 hover:bg-blue-100 rounded">
                        <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center mr-3">
                            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                        </div>
                        <span><?php echo htmlspecialchars($user['username']); ?></span>
                    </a>
                <?php endwhile;
            } elseif ($type === 'apoteker') {
                echo '<h2 class="font-semibold mb-4">Daftar Apoteker</h2>';
                while ($apoteker = $apotekerResult->fetch_assoc()): ?>
                    <a href="?type=apoteker&user_id=<?php echo $apoteker['user_id']; ?>" class="flex items-center p-2 hover:bg-blue-100 rounded">
                        <div class="w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center mr-3">
                            <?php echo strtoupper(substr($apoteker['username'], 0, 1)); ?>
                        </div>
                        <span><?php echo htmlspecialchars($apoteker['username']); ?></span>
                    </a>
                <?php endwhile;
            } elseif ($type === 'cs') {
                echo '<h2 class="font-semibold mb-4 mt-4">Customer Service</h2>';
                while ($cs = $csResult->fetch_assoc()) : ?>
                    <a href="?user_id=<?php echo $cs['user_id']; ?>&type=cs" class="flex items-center p-2 hover:bg-blue-100 rounded">
                        <div class="w-10 h-10 rounded-full bg-green-600 text-white flex items-center justify-center mr-3">
                            <?php echo strtoupper(substr($cs['username'], 0, 1)); ?>
                        </div>
                        <span><?php echo htmlspecialchars($cs['username']); ?></span>
                    </a>
            <?php endwhile;
            } else {
                echo "<p>Tipe tidak dikenali.</p>";
            }
            ?>
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
                <?php
                while ($message = $messageResult->fetch_assoc()) {
                    $messageClass = ($message['user_id'] == $_SESSION['user_id']) ? 'message-doctor' : 'message-user';
                    echo "<div class='p-2 my-2 border-b $messageClass'>";
                    echo htmlspecialchars($message['message_text']);
                    echo "</div>";
                }
                ?>
            </div>

            <!-- Area Input Pesan -->
            <form method="POST" class="flex items-center p-2 border-t" <?php echo $selectedUserId ? '' : 'style="display:none;"'; ?>>
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($selectedUserId); ?>" />
                <input type="text" name="message_text" placeholder="Ketik di sini dan tekan enter.." class="flex-1 px-4 py-2 border rounded-full text-sm focus:outline-none" required />
                <button type="submit" class="ml-2 text-orange-500">
                    <i class="ri-send-plane-2-line text-xl"></i>
                </button>
            </form>
        </div>
    </div>

</body>

</html>