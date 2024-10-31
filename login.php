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

if ($konek->connect_error) {
    die("Koneksi gagal: " . $konek->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $konek->prepare("SELECT user_id, email, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['reset_email'] = $user['email'];
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['action'] = 'login';
            

            $token = bin2hex(random_bytes(4));
            $_SESSION['token'] = $token;

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
                $_SESSION['success_message'] = 'Kode verifikasi telah dikirim ke email Anda.';
                header('Location: verify_token.php?action=login');
                exit();
            } catch (Exception $e) {
                echo "Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $_SESSION['error_message'] = 'Username atau kata sandi salah.';
            header('Location: Landing_Page.php#login-section');
            exit();
        }
    } else {
        $_SESSION['error_message'] = 'Username tidak ditemukan.';
        header('Location: Landing_Page.php#login-section');
        exit();
    }
}
?>
