<?php
session_start();
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

// Konfigurasi koneksi database
$hostname = "localhost";
$username_db = "root";
$password_db = "";
$database = "lotusbeauty";
$konek = new mysqli($hostname, $username_db, $password_db, $database);

// Cek koneksi
if ($konek->connect_error) {
    die("Koneksi gagal: " . $konek->connect_error);
}

$swal_script = ""; // Variabel untuk menyimpan pesan SweetAlert2

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Cek apakah email ada di database
    $stmt = $konek->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Buat token verifikasi
        $token = bin2hex(random_bytes(4)); // Token 8 karakter
        $_SESSION['token'] = $token;
        $_SESSION['reset_email'] = $email;
        $_SESSION['action'] = 'password_reset';

        // Kirim token melalui email menggunakan PHPMailer
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'hallmaster677@gmail.com';
            $mail->Password = 'emyh qqcr nqoa nuck'; // Ganti dengan kata sandi aplikasi atau gunakan environment variable
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('hallmaster677@gmail.com', 'Admin Lotus Beauty');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Verifikasi Reset Password';
            $mail->Body = "Kode verifikasi Anda adalah: <b>$token</b>";

            $mail->send();
            // SweetAlert untuk sukses
            $swal_script = "Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Kode verifikasi telah dikirim ke email Anda.',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.href='verify_token.php';
                            });";
        } catch (Exception $e) {
            // SweetAlert untuk gagal
            $swal_script = "Swal.fire({
                                icon: 'error',
                                title: 'Gagal mengirim!',
                                text: 'Gagal mengirim email. Silakan coba lagi nanti.',
                                confirmButtonText: 'OK'
                            });";
        }
    } else {
        // SweetAlert untuk email tidak ditemukan
        $swal_script = "Swal.fire({
                            icon: 'warning',
                            title: 'Email tidak ditemukan!',
                            text: 'Email tidak ditemukan di sistem.',
                            confirmButtonText: 'OK'
                        });";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Token</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="flex items-center justify-center h-screen bg-gray-900">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-4">Reset Password</h2>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" class="w-full px-4 py-2 mb-4 border rounded" required>
            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Kirim Token</button>
        </form>
    </div>

    <!-- Menjalankan SweetAlert jika ada script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            <?php echo $swal_script; ?>
        });
    </script>
</body>

</html>
