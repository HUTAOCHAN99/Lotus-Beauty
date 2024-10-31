<?php
session_start();

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

// Ambil username dari session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Memeriksa apakah pengguna sudah login
if (empty($username)) {
    echo "Anda harus login untuk mengakses halaman ini.";
    exit;
}

// Mengambil role pengguna dari database berdasarkan username
$query = "SELECT role FROM users WHERE username = ?";
$stmt = $konek->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($userRole);
$stmt->fetch();
$stmt->close();

// Memeriksa apakah role ditemukan
if (empty($userRole)) {
    echo "Role pengguna tidak ditemukan.";
    exit;
}

// Memeriksa apakah pengguna adalah customer
if ($userRole !== 'customer') {
    echo "Anda tidak memiliki akses ke halaman ini.";
    exit;
}

// Ambil daftar dokter
$doctorQuery = "SELECT user_id, username FROM users WHERE role = 'dokter'";
$doctorResult = $konek->query($doctorQuery);

// Ambil pesan dari dokter jika ada yang dipilih
$selectedDoctorId = isset($_GET['doctor_id']) ? $_GET['doctor_id'] : null;
$messageQuery = "SELECT m.*, u.username FROM messages m JOIN users u ON m.user_id = u.user_id WHERE m.recipient_id = ? AND m.user_id = ? ORDER BY m.created_at ASC";
$messageStmt = $konek->prepare($messageQuery);

// Jika dokter dipilih, ambil pesan terkait
if ($selectedDoctorId) {
    $customerId = $_SESSION['user_id']; // Ambil user_id customer dari session
    $messageStmt->bind_param("ii", $customerId, $selectedDoctorId);
    $messageStmt->execute();
    $messageResult = $messageStmt->get_result();
} else {
    $messageResult = null;
}

// Menutup statement
$messageStmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat dengan Dokter</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="w-full max-w-4xl bg-white shadow-lg rounded-lg overflow-hidden flex">

    <!-- Daftar Dokter -->
    <div class="w-1/3 bg-gray-50 p-4 h-screen overflow-y-auto">
        <h2 class="font-semibold mb-4">Daftar Dokter</h2>
        <?php while ($doctor = $doctorResult->fetch_assoc()) : ?>
            <a href="?doctor_id=<?php echo $doctor['user_id']; ?>" class="flex items-center p-2 hover:bg-blue-100 rounded">
                <div class="w-10 h-10 rounded-full bg-green-600 text-white flex items-center justify-center mr-3">
                    <?php echo strtoupper(substr($doctor['username'], 0, 1)); ?>
                </div>
                <span><?php echo htmlspecialchars($doctor['username']); ?></span>
            </a>
        <?php endwhile; ?>
    </div>

    <!-- Area Pesan -->
    <div class="w-2/3 p-4 h-screen overflow-y-auto">
        <h2 class="font-semibold mb-4">
            <?php 
                if ($selectedDoctorId) {
                    $selectedDoctorQuery = "SELECT username FROM users WHERE user_id = ?";
                    $stmt = $konek->prepare($selectedDoctorQuery);
                    $stmt->bind_param("i", $selectedDoctorId);
                    $stmt->execute();
                    $selectedDoctorResult = $stmt->get_result();
                    $selectedDoctor = $selectedDoctorResult->fetch_assoc();
                    echo "Chat dengan " . htmlspecialchars($selectedDoctor['username']);
                    $stmt->close();
                } else {
                    echo "Pilih dokter untuk memulai chat.";
                }
            ?>
        </h2>

        <div class="p-4 h-80 overflow-y-auto bg-gray-50">
            <?php
                if ($messageResult) {
                    while ($message = $messageResult->fetch_assoc()) {
                        echo "<div class='p-2 my-2 border-b'>";
                        echo "<strong>" . htmlspecialchars($message['username']) . ":</strong> " . htmlspecialchars($message['message_text']);
                        echo "</div>";
                    }
                }
            ?>
        </div>

        <!-- Area Input Pesan -->
        <form method="POST" class="flex items-center p-2 border-t" <?php echo $selectedDoctorId ? '' : 'style="display:none;"'; ?>>
            <input type="hidden" name="doctor_id" value="<?php echo htmlspecialchars($selectedDoctorId); ?>" />
            <input type="text" name="message_text" placeholder="Ketik di sini dan tekan enter.." class="flex-1 px-4 py-2 border rounded-full text-sm focus:outline-none" required />
            <button type="submit" class="ml-2 text-orange-500">
                <i class="ri-send-plane-2-line text-xl"></i>
            </button>
        </form>
    </div>
</div>

</body>
</html>

<?php
// Handle the message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_text']) && isset($_POST['doctor_id'])) {
    $messageText = $_POST['message_text'];
    $recipientId = $_POST['doctor_id']; // Doctor ID to whom the message is sent
    $customerId = $_SESSION['user_id']; // Current logged-in customer's user ID

    // Insert the message into the database
    $insertQuery = "INSERT INTO messages (user_id, recipient_id, message_text, created_at) VALUES (?, ?, ?, NOW())";
    $insertStmt = $konek->prepare($insertQuery);
    $insertStmt->bind_param("iis", $customerId, $recipientId, $messageText);

    if ($insertStmt->execute()) {
        // Redirect to the same page to prevent form resubmission
        header("Location: customer.php?doctor_id=" . $recipientId);
        exit();
    } else {
        echo "Error: " . $insertStmt->error;
    }

    $insertStmt->close();
}

// Menutup koneksi
$konek->close();
?>
