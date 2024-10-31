<?php
// Include koneksi database
include 'db.php';
session_start(); // Memulai sesi

// Simpan data ke database jika form di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pastikan semua variabel POST tersedia
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : '';
    $full_name = isset($_POST['full_name']) ? $_POST['full_name'] : '';
    $phone_number = isset($_POST['phone_number']) ? $_POST['phone_number'] : '';
    $address = isset($_POST['address']) ? $_POST['address'] : '';
    $role = isset($_POST['role']) ? $_POST['role'] : '';

    // SQL query untuk memeriksa apakah username atau email sudah ada
    $checkQuery = "SELECT * FROM users WHERE username='$username' OR email='$email'";
    $checkResult = $konek->query($checkQuery);

    if ($checkResult->num_rows > 0) {
        $_SESSION['error_message'] = 'Username atau email sudah terdaftar!';
        header("Location: create_account.php");
        exit();
    }

    // SQL query untuk memasukkan data user baru
    $sql = "INSERT INTO users (username, email, password, full_name, phone_number, address, role)
            VALUES ('$username', '$email', '$password', '$full_name', '$phone_number', '$address', '$role')";

    // Cek apakah query berhasil
    if ($konek->query($sql) === TRUE) {
        $_SESSION['success_message'] = 'Akun baru berhasil dibuat!';
    } else {
        $_SESSION['error_message'] = 'Terjadi kesalahan: ' . $konek->error; // Simpan pesan error
    }
    
    // Redirect kembali ke halaman create_account
    header("Location: create_account.php");
    exit();
} else {
    echo "Form tidak dikirim dengan metode POST.";
}

$konek->close();
