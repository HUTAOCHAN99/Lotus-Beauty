<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Only start session if it is not already started
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
$userQuery = "SELECT DISTINCT u.user_id, u.username FROM messages m JOIN users u ON m.user_id = u.user_id WHERE m.recipient_id = ?";
$userStmt = $konek->prepare($userQuery);
$userStmt->bind_param("i", $_SESSION['user_id']); // user_id dokter dari session
$userStmt->execute();
$userResult = $userStmt->get_result();

// Mengambil pesan dari pengguna yang dipilih
$selectedUserId = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$messageQuery = "SELECT m.*, u.username FROM messages m JOIN users u ON m.user_id = u.user_id WHERE m.recipient_id = ? AND m.user_id = ? ORDER BY m.created_at ASC";
$messageStmt = $konek->prepare($messageQuery);
$messageStmt->bind_param("ii", $_SESSION['user_id'], $selectedUserId);
$messageStmt->execute();
$messageResult = $messageStmt->get_result();

// Pastikan untuk menutup semua statement sebelum menutup koneksi
$userStmt->close();
$messageStmt->close();

// Handle the message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_text'])) {
    $messageText = $_POST['message_text'];
    $recipientId = $_POST['user_id']; // customer ID to whom the message is sent
    $doctorId = $_SESSION['user_id']; // current logged-in doctor's user ID

    // Insert the message into the database
    $insertQuery = "INSERT INTO messages (user_id, recipient_id, message_text, created_at) VALUES (?, ?, ?, NOW())";
    $insertStmt = $konek->prepare($insertQuery);
    $insertStmt->bind_param("iis", $doctorId, $recipientId, $messageText);

    if ($insertStmt->execute()) {
        // Redirect to the same page to prevent form resubmission
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
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="w-full max-w-4xl bg-white shadow-lg rounded-lg overflow-hidden flex">

    <!-- Daftar Pengguna -->
    <div class="w-1/3 bg-gray-50 p-4 h-screen overflow-y-auto">
        <h2 class="font-semibold mb-4">Pengguna</h2>
        <?php while ($user = $userResult->fetch_assoc()) : ?>
            <a href="?user_id=<?php echo $user['user_id']; ?>" class="flex items-center p-2 hover:bg-blue-100 rounded">
                <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center mr-3">
                    <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                </div>
                <span><?php echo htmlspecialchars($user['username']); ?></span>
            </a>
        <?php endwhile; ?>
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
                    echo "Chat with " . htmlspecialchars($selectedUser['username']);
                    $stmt->close();
                } else {
                    echo "Pilih pengguna untuk memulai chat.";
                }
            ?>
        </h2>

        <div class="p-4 h-80 overflow-y-auto bg-gray-50">
            <?php
                while ($message = $messageResult->fetch_assoc()) {
                    echo "<div class='p-2 my-2 border-b'>";
                    echo "<strong>" . htmlspecialchars($message['username']) . ":</strong> " . htmlspecialchars($message['message_text']);
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
