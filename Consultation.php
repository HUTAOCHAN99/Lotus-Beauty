<?php
ob_start(); // Mulai output buffering
session_start();

// Koneksi ke database
include 'db.php';

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
// menghandle form atau chat terkirim 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_text']) && isset($_POST['recipient_id'])) {
    // variabel pesan dan penerima
    $messageText = $_POST['message_text'];
    $recipientId = $_POST['recipient_id'];
    $konek = getConnection();
    $insertQuery = "INSERT INTO messages (user_id, recipient_id, message_text, created_at) VALUES (?, ?, ?, NOW())";
    $insertStmt = $konek->prepare($insertQuery);
    $insertStmt->bind_param("iis", $userId, $recipientId, $messageText);

    if ($insertStmt->execute()) {
        // mengarahakan ke halaman sesuai dengan paramater penerima
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
</head>

<body class="bg-gray-100 ">

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_text']) && isset($_POST['recipient_id'])) {
        $messageText = $_POST['message_text'];
        $recipientId = $_POST['recipient_id'];
        $konek = getConnection();
        $insertQuery = "INSERT INTO messages (user_id, recipient_id, message_text, created_at) VALUES (?, ?, ?, NOW())";
        $insertStmt = $konek->prepare($insertQuery);
        $insertStmt->bind_param("iis", $userId, $recipientId, $messageText);
        if ($insertStmt->execute()) {
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