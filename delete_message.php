<?php
session_start();

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

// Memastikan admin sudah login
if (!isset($_SESSION['username'])) {
    echo "Anda harus login untuk mengakses halaman ini.";
    exit;
}

// Memastikan pengguna adalah admin
$username = $_SESSION['username'];
$konek = getConnection();
$query = "SELECT role FROM users WHERE username = ?";
$stmt = $konek->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($userRole);
$stmt->fetch();
$stmt->close();

if ($userRole !== 'admin') {
    echo "Hanya admin yang dapat mengakses halaman ini.";
    exit;
}

// Hapus pesan
if (isset($_POST['message_id'])) {
    $messageId = $_POST['message_id'];
    $deleteQuery = "DELETE FROM messages WHERE message_id = ?";
    $deleteStmt = $konek->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $messageId);
    $deleteStmt->execute();
    $deleteStmt->close();
}

// Redirect kembali ke halaman admin panel
header("Location: admin.php"); // Ganti dengan nama file halaman admin kamu
exit();
