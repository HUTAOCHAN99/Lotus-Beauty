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

// Mengarahkan pengguna berdasarkan r

// Mengarahkan pengguna berdasarkan role dengan mengirimkan nilai $type
if ($userRole === 'dokter') {
    if (isset($_GET['type'])) {
        $type = $_GET['type'];
    } else {
        echo "Tipe tidak ditemukan.";
        exit();
    }
    header("Location: dokter.php?type=" . urlencode($type));
    exit();
} elseif ($userRole === 'apoteker') {
    if (isset($_GET['type'])) {
        $type = $_GET['type'];
    } else {
        echo "Tipe tidak ditemukan.";
        exit();
    }
    header("Location: apoteker.php?type=" . urlencode($type));
    exit();
} elseif ($userRole === 'cs') {
    if (isset($_GET['type'])) {
        $type = $_GET['type'];
    } else {
        echo "Tipe tidak ditemukan.";
        exit();
    }
    header("Location: cs.php?type=" . urlencode($type));
    exit();
} elseif ($userRole == 'customer') {
    if (isset($_GET['type'])) {
        $type = $_GET['type'];
    } else {
        echo "Tipe tidak ditemukan.";
        exit();
    }
    header("Location: customer.php?type=" . urlencode($type));
    exit();
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
</body>

</html>