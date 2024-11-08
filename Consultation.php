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

    // Ambil daftar dokter
$doctorQuery = "SELECT user_id, username FROM users WHERE role = 'dokter'";
$doctorResult = $konek->query($doctorQuery);

$apotekerQuery = "SELECT user_id, username FROM users WHERE role = 'apoteker'";
$apotekerResult = $konek->query($apotekerQuery);

$csQuery = "SELECT user_id, username FROM users WHERE role = 'cs'";
$csResult = $konek->query($csQuery);

// Tutup koneksi
$konek->close();
?>

<!-- HTML dan form di sini -->

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
        header("Location: consultation.php?" . ($selectedRole === 'dokter' ? "doctor_id=" : ($selectedRole === 'apoteker' ? "apoteker_id=" : "cs_id=")) . $recipientId);
        exit();
    } else {
        echo "Error: " . $insertStmt->error;
    }
    $insertStmt->close();
    $konek->close();
}
ob_end_flush(); // Hentikan output buffering
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat dengan Dokter / Apoteker / CS</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-powderBlue {
            background-color: #B0E0E6;
        }

        .message-user {
            text-align: right;
            background-color: #f0f9ff;
            padding: 10px;
            border-radius: 15px;
            max-width: 70%;
            margin-left: auto;
            margin-bottom: 10px;
            word-wrap: break-word;
        }

        .message-doctor,
        .message-apoteker,
        .message-cs {
            text-align: left;
            background-color: #e0f7fa;
            padding: 10px;
            border-radius: 15px;
            max-width: 70%;
            margin-right: auto;
            margin-bottom: 10px;
            word-wrap: break-word;
        }

        .message-apoteker {
            background-color: #ffe0b2;
        }

        .message-cs {
            background-color: #e0f7fa;
        }

        .message-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
            overflow-y: auto;
            height: calc(100vh - 150px);
            padding-right: 5px;
        }

        /* Gaya Scrollbar Minimalis */
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

        /* Gaya Tombol Scroll ke Bawah */
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

<body class="bg-gray-100 ">
    <!-- Navbar -->
    <nav class="bg-powderBlue shadow-md p-4 flex justify-between items-center">
        <!-- Tombol Kembali ke Home -->
        <a href="Consultation_Page.php" class="text-black flex items-center space-x-2">
            <i class="ri-arrow-left-line text-xl"></i>
        </a>
        <!-- Nama Halaman -->
        <h1 class="text-gray-800 font-bold text-lg">Chat Konsultasi</h1>
        <!-- Placeholder untuk spasi antara tombol kembali dan nama halaman -->
        <div class="w-10"></div>
    </nav>
    <div class="flex items-center justify-center min-h-screen">

        <div class="w-full max-w- bg-white shadow-lg rounded-lg overflow-hidden flex">

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
                } elseif ($type === 'cs') {
                    echo '<h2 class="font-semibold mb-4">Daftar Customer Service</h2>';
                    while ($cs = $csResult->fetch_assoc()): ?>
                        <a href="?type=cs&cs_id=<?php echo $cs['user_id']; ?>"
                            class="flex items-center p-2 hover:bg-blue-100 rounded">
                            <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center mr-3">
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
                    // Tampilkan nama dokter, apoteker, atau CS yang dipilih
                    $selectedId = isset($_GET['doctor_id']) ? $_GET['doctor_id'] : (isset($_GET['apoteker_id']) ? $_GET['apoteker_id'] : (isset($_GET['cs_id']) ? $_GET['cs_id'] : null));
                    $selectedRole = isset($_GET['doctor_id']) ? 'dokter' : (isset($_GET['apoteker_id']) ? 'apoteker' : (isset($_GET['cs_id']) ? 'cs' : null));

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
                        echo "Pilih dokter untuk memulai chat";
                    }
                    ?>
                </h2>

                <!-- Kolom Pesan -->
                <div class="p-4 bg-gray-50 message-container" id="message-container">
                    <?php
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
                            $isUserMessage = ($message['user_id'] == $userId);
                            $messageClass = $isUserMessage ? 'message-user' :
                                ($selectedRole === 'dokter' ? 'message-doctor' :
                                    ($selectedRole === 'apoteker' ? 'message-apoteker' : 'message-cs'));

                            echo "<div class='p-2 my-2 border-b $messageClass'>";
                            if (!$isUserMessage) {
                                echo "<p class='font-bold text-xs mb-1'>" . htmlspecialchars($message['username']) . "</p>";
                            }
                            echo "<p>" . htmlspecialchars($message['message_text']) . "</p>";
                            echo "<p class='text-xs text-gray-500'>" . date("H:i", strtotime($message['created_at'])) . "</p>";
                            echo "</div>";
                        }
                        $messageStmt->close();
                        $konek->close();
                    }
                    ?>
                </div>
                <div id="scroll-button">
                    <i class="ri-arrow-down-line text-xl"></i>
                </div>

                <!-- Area Input Pesan -->
                <div class="fixed w-3/5 bottom-0 items-center">
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
                header("Location: consultation.php?" . ($selectedRole === 'dokter' ? "doctor_id=" : ($selectedRole === 'apoteker' ? "apoteker_id=" : "cs_id=")) . $recipientId);
                exit();
            } else {
                echo "Error: " . $insertStmt->error;
            }
            $insertStmt->close();
            $konek->close();
        }
        ?>
    </div>
    <script>
        const messageContainer = document.getElementById("message-container");
        const scrollButton = document.getElementById("scroll-button");

        function scrollToBottom() {
            messageContainer.scrollTop = messageContainer.scrollHeight;
        }

        // Fungsi untuk mengecek apakah user sudah di dasar chat
        function checkScrollPosition() {
            const atBottom = messageContainer.scrollHeight - messageContainer.scrollTop === messageContainer.clientHeight;
            scrollButton.style.display = atBottom ? "none" : "block";
        }

        // Event listener untuk scroll pada message container
        messageContainer.addEventListener("scroll", checkScrollPosition);

        // Event listener untuk tombol scroll ke bawah
        scrollButton.addEventListener("click", scrollToBottom);

        // Pastikan chat scroll ke bawah ketika halaman dimuat
        window.onload = () => {
            scrollToBottom();
            checkScrollPosition(); // Cek posisi saat halaman dimuat
        };

        // Scroll otomatis setelah form disubmit
        document.querySelector('form').addEventListener('submit', function () {
            setTimeout(() => {
                scrollToBottom();
                checkScrollPosition(); // Cek posisi setelah mengirim pesan
            }, 300);
        });
    </script>

</body>

</html>