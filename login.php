<?php
session_start();
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

$hostname = "localhost";
$username_db = "root";
$password_db = "";
$database = "lotusbeauty";
$konek = new mysqli($hostname, $username_db, $password_db, $database);

// Periksa koneksi ke database
if ($konek->connect_error) {
    die("Koneksi gagal: " . $konek->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek apakah username ada
    $stmt = $konek->prepare("SELECT email, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Simpan email dan username untuk verifikasi
            $_SESSION['reset_email'] = $user['email'];
            $_SESSION['username'] = $username;
            $_SESSION['action'] = 'login'; // Tambahkan ini agar halaman verifikasi tahu ini proses login

            // Buat dan simpan token untuk verifikasi
            $token = bin2hex(random_bytes(4));
            $_SESSION['token'] = $token;

            // Kirim token via email
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'hallmaster677@gmail.com';
                $mail->Password = 'emyh qqcr nqoa nuck';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('hallmaster677@gmail.com', 'Admin Lotus Beauty');
                $mail->addAddress($user['email']);
                $mail->isHTML(true);
                $mail->Subject = 'Verifikasi Login';
                $mail->Body = "Kode verifikasi Anda adalah: <b>$token</b>";

                $mail->send();
                echo "<script>alert('Kode verifikasi telah dikirim ke email Anda.'); window.location.href='verify_token.php?action=login';</script>";
            } catch (Exception $e) {
                echo "Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "<script>alert('Username atau kata sandi salah.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Username tidak ditemukan.'); window.history.back();</script>";
    }
}
