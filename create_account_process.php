<?php
// Include koneksi database
include 'db.php';

// Simpan data ke database jika form di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi password
    $full_name = $_POST['full_name'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $role = $_POST['role'];

    // SQL query untuk memasukkan data user baru (tanpa user_id karena auto-increment)
    $sql = "INSERT INTO users (username, email, password, full_name, phone_number, address, role)
            VALUES ('$username', '$email', '$password', '$full_name', '$phone_number', '$address', '$role')";

    // Cek apakah query berhasil
    if ($konek->query($sql) === TRUE) {
        echo "Akun baru berhasil dibuat!";
        header("Location: Landing_Page.php"); // Redirect ke halaman login
        exit(); // Menghentikan eksekusi setelah redirect
    } else {
        echo "Terjadi kesalahan: " . $sql . "<br>" . $konek->error;
    }

    $konek->close();
}
