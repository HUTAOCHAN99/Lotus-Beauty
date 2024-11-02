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

// Memastikan pengguna adalah CS
if ($userRole !== 'cs') {
    echo "Hanya customer service yang dapat mengakses halaman ini.";
    exit;
}

// Ambil daftar dokter, apoteker, dan pengguna
$doctorQuery = "SELECT user_id, username FROM users WHERE role = 'dokter'";
$doctorResult = $konek->query($doctorQuery);

$apotekerQuery = "SELECT user_id, username FROM users WHERE role = 'apoteker'";
$apotekerResult = $konek->query($apotekerQuery);

$customerQuery = "SELECT user_id, username FROM users WHERE role = 'customer'";
$customerResult = $konek->query($customerQuery);

// Tutup koneksi
$konek->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat dengan Dokter / Apoteker / Pengguna</title>
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

        .message-apoteker {
            text-align: left;
            background-color: #fff3e0;
        }

        .message-customer {
            text-align: left;
            background-color: #e8f5e9;
        }
    </style>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-4xl bg-white shadow-lg rounded-lg overflow-hidden flex">

        <div class="w-1/3 bg-gray-50 p-4 h-screen overflow-y-auto">
            <h2 class="font-semibold mb-4">Daftar Dokter, Apoteker, dan Pengguna</h2>

            <h3 class="font-semibold">Dokter</h3>
            <?php while ($doctor = $doctorResult->fetch_assoc()): ?>
                <a href="?type=dokter&doctor_id=<?php echo $doctor['user_id']; ?>"
                    class="flex items-center p-2 hover:bg-blue-100 rounded">
                    <div class="w-10 h-10 rounded-full bg-green-600 text-white flex items-center justify-center mr-3">
                        <?php echo strtoupper(substr($doctor['username'], 0, 1)); ?>
                    </div>
                    <span><?php echo htmlspecialchars($doctor['username']); ?></span>
                </a>
            <?php endwhile; ?>

            <h3 class="font-semibold mt-4">Apoteker</h3>
            <?php while ($apoteker = $apotekerResult->fetch_assoc()): ?>
                <a href="?type=apoteker&apoteker_id=<?php echo $apoteker['user_id']; ?>"
                    class="flex items-center p-2 hover:bg-blue-100 rounded">
                    <div class="w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center mr-3">
                        <?php echo strtoupper(substr($apoteker['username'], 0, 1)); ?>
                    </div>
                    <span><?php echo htmlspecialchars($apoteker['username']); ?></span>
                </a>
            <?php endwhile; ?>

            <h3 class="font-semibold mt-4">Pengguna</h3>
            <?php while ($customer = $customerResult->fetch_assoc()): ?>
                <a href="?type=customer&customer_id=<?php echo $customer['user_id']; ?>"
                    class="flex items-center p-2 hover:bg-blue-100 rounded">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center mr-3">
                        <?php echo strtoupper(substr($customer['username'], 0, 1)); ?>
                    </div>
                    <span><?php echo htmlspecialchars($customer['username']); ?></span>
                </a>
            <?php endwhile; ?>
        </div>

        <!-- Area Pesan -->
        <div class="w-2/3 p-4 h-screen overflow-y-auto">
            <h2 class="font-semibold mb-4">
                <?php
                // Tampilkan nama dokter, apoteker, atau pengguna yang dipilih
                $selectedId = isset($_GET['doctor_id']) ? $_GET['doctor_id'] : (isset($_GET['apoteker_id']) ? $_GET['apoteker_id'] : (isset($_GET['customer_id']) ? $_GET['customer_id'] : null));
                $selectedRole = isset($_GET['doctor_id']) ? 'dokter' : (isset($_GET['apoteker_id']) ? 'apoteker' : (isset($_GET['customer_id']) ? 'customer' : null));

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
                    echo "Pilih dokter, apoteker, atau pengguna untuk memulai chat.";
                }
                ?>
            </h2>

            <div class="p-4 h-80 overflow-y-auto bg-gray-50">
                <?php
                // Tampilkan pesan hanya jika dokter, apoteker, atau pengguna dipilih
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
                        $messageClass = ($message['user_id'] == $userId) ? 'message-user' : 
                                        ($selectedRole === 'dokter' ? 'message-doctor' : 
                                        ($selectedRole === 'apoteker' ? 'message-apoteker' : 'message-customer'));
                        echo "<div class='p-2 my-2 border-b $messageClass'>";
                        echo htmlspecialchars($message['message_text']);
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
            header("Location: cs.php?" . ($selectedRole === 'dokter' ? "doctor_id=" : ($selectedRole === 'apoteker' ? "apoteker_id=" : "customer_id=")) . $recipientId);
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
