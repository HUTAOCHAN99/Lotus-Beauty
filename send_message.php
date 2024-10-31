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

// Proses pengiriman pesan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $userId = $_POST['user_id']; // ID pengguna yang dipilih
    $messageText = $_POST['message_text']; // Pesan dari input
    $doctorId = $_SESSION['user_id']; // ID dokter/apoteker/cs dari session
    $type = "chat"; // Jenis pesan bisa ditambahkan sesuai kebutuhan

    // Simpan pesan ke database
    $query = "INSERT INTO messages (user_id, recipient_id, message_text, type) VALUES (?, ?, ?, ?)";
    $stmt = $konek->prepare($query);
    $stmt->bind_param("iiss", $doctorId, $userId, $messageText, $type);
    
    if ($stmt->execute()) {
        echo "Pesan berhasil dikirim.";
    } else {
        echo "Kesalahan: " . $stmt->error;
    }

    // Redirect kembali ke consultation.php
    header("Location: consultation.php");
    exit;
}

// Menutup koneksi
$konek->close();
?>
