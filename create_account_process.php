<?php
// Include koneksi database
include 'db.php';

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

    // SQL query untuk memasukkan data user baru
    $sql = "INSERT INTO users (username, email, password, full_name, phone_number, address, role)
            VALUES ('$username', '$email', '$password', '$full_name', '$phone_number', '$address', '$role')";

    // Cek apakah query berhasil
    if ($konek->query($sql) === TRUE) {
        echo "Akun baru berhasil dibuat!";
        header("Location: Landing_Page.php"); // Redirect ke halaman lain
        exit(); // Menghentikan eksekusi setelah redirect
    } else {
        // Menampilkan pesan error jika query gagal
        echo "Terjadi kesalahan: " . $sql . "<br>" . $konek->error;
    }

    $konek->close();
} else {
    echo "Form tidak dikirim dengan metode POST.";
}
