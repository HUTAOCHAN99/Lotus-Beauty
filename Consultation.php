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

// Mengambil role pengguna dari database berdasarkan username
$konek = getConnection();
$query = "SELECT role, user_id FROM users WHERE username = ?";
$stmt = $konek->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($userRole, $userId);
$stmt->fetch();
$stmt->close();

// Mengarahkan pengguna berdasarkan role
if ($userRole === 'dokter') {
    header("Location: dokter.php");
    exit();
} elseif ($userRole === 'apoteker') {
    header("Location: apoteker.php");
    exit();
} elseif ($userRole === 'cs') {
    header("Location: cs.php");
    exit();
} elseif ($userRole !== 'customer') {
    echo "Role tidak dikenali.";
    exit;
}

// Ambil daftar dokter dan apoteker
$doctorQuery = "SELECT user_id, username FROM users WHERE role = 'dokter'";
$doctorResult = $konek->query($doctorQuery);

$apotekerQuery = "SELECT user_id, username FROM users WHERE role = 'apoteker'";
$apotekerResult = $konek->query($apotekerQuery);

// Tutup koneksi
$konek->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat dengan Dokter / Apoteker</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .message-user {
            text-align: right;
            background-color: #f0f9ff;
        }

        .message-doctor {
            text-align: left;
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

            if ($type === 'dokter') {
                echo '<h2 class="font-semibold mb-4">Daftar Dokter</h2>';
                while ($doctor = $doctorResult->fetch_assoc()): ?>
                    <a href="?type=dokter&doctor_id=<?php echo $doctor['user_id']; ?>"
                        class="flex items-center p-2 hover:bg-blue-100 rounded">
                        <div class="w-10 h-10 rounded-full bg-green-600 text-white flex items-center justify-center mr-3">
                            <?php echo strtoupper(substr($doctor['username'], 0, 1)); ?>
                        </div>
                        <span><?php echo htmlspecialchars($doctor['username']); ?></span>
                    </a>
                <?php endwhile;
            } elseif ($type === 'apoteker') {
                echo '<h2 class="font-semibold mb-4">Daftar Apoteker</h2>';
                while ($apoteker = $apotekerResult->fetch_assoc()): ?>
                    <a href="?type=apoteker&apoteker_id=<?php echo $apoteker['user_id']; ?>"
                        class="flex items-center p-2 hover:bg-blue-100 rounded">
                        <div class="w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center mr-3">
                            <?php echo strtoupper(substr($apoteker['username'], 0, 1)); ?>
                        </div>
                        <span><?php echo htmlspecialchars($apoteker['username']); ?></span>
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
                // Tampilkan nama dokter atau apoteker yang dipilih
                $selectedId = isset($_GET['doctor_id']) ? $_GET['doctor_id'] : (isset($_GET['apoteker_id']) ? $_GET['apoteker_id'] : null);
                $selectedRole = isset($_GET['doctor_id']) ? 'dokter' : (isset($_GET['apoteker_id']) ? 'apoteker' : null);

                if ($selectedId) {
                    $konek = getConnection();
                    $stmt = $konek->prepare("SELECT username FROM users WHERE user_id = ?");
                    $stmt->bind_param("i", $selectedId);
                    $stmt->execute();
                    $selectedResult = $stmt->get_result();
                    $selectedUser = $selectedResult->fetch_assoc();
                    echo "Chat dengan " . htmlspecialchars($selectedUser['username']);
                    $stmt->close();
                    $konek->close();
                } else {
                    echo "Pilih dokter atau apoteker untuk memulai chat.";
                }
                ?>
            </h2>

            <div class="p-4 h-80 overflow-y-auto bg-gray-50">
                <?php
                // Tampilkan pesan hanya jika dokter atau apoteker dipilih
                if ($selectedId) {
                    $konek = getConnection();
                    $messageQuery = "SELECT m.*, u.username FROM messages m JOIN users u ON m.user_id = u.user_id 
                                WHERE (m.user_id = ? AND m.recipient_id = ?) OR (m.user_id = ? AND m.recipient_id = ?)
                                ORDER BY m.created_at ASC";
                    $messageStmt = $konek->prepare($messageQuery);
                    $messageStmt->bind_param("iiii", $userId, $selectedId, $selectedId, $userId);
                    $messageStmt->execute();
                    $messageResult = $messageStmt->get_result();

                    while ($message = $messageResult->fetch_assoc()) {
                        $messageClass = ($message['user_id'] == $userId) ? 'message-user' : 'message-doctor';
                        echo "<div class='p-2 my-2 border-b $messageClass'>";
                        echo "<strong>" . htmlspecialchars($message['username']) . ":</strong> " . htmlspecialchars($message['message_text']);
                        echo "</div>";
                    }
                    $messageStmt->close();
                    $konek->close();
                }
                ?>
            </div>

            <!-- Area Input Pesan -->
            <form method="POST" class="flex items-center p-2 border-t" <?php echo $selectedId ? '' : 'style="display:none;"'; ?>>
                <input type="hidden" name="recipient_id" value="<?php echo htmlspecialchars($selectedId); ?>" />
                <input type="text" name="message_text" placeholder="Ketik di sini dan tekan enter.."
                    class="flex-1 px-4 py-2 border rounded-full text-sm focus:outline-none" required />
                <button type="submit" class="ml-2 text-orange-500">
                    <i class="ri-send-plane-2-line text-xl"></i>
                </button>
            </form>
        </div>
    </div>

    <?php
    // Handle the message sending
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_text']) && isset($_POST['recipient_id'])) {
        $messageText = $_POST['message_text'];
        $recipientId = $_POST['recipient_id'];
        $konek = getConnection();
        $insertQuery = "INSERT INTO messages (user_id, recipient_id, message_text, created_at) VALUES (?, ?, ?, NOW())";
        $insertStmt = $konek->prepare($insertQuery);
        $insertStmt->bind_param("iis", $userId, $recipientId, $messageText);

        if ($insertStmt->execute()) {
            // Redirect to prevent form resubmission
            header("Location: consultation.php?" . ($selectedRole == 'dokter' ? "doctor_id=" : "apoteker_id=") . $recipientId);
            exit();
        } else {
            echo "Error: " . $insertStmt->error;
        }
        $insertStmt->close();
        $konek->close();
    }
    ?>

</body>

</html>