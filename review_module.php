<?php
session_start(); // Pastikan session sudah dimulai

// Connect to the database
include('db.php');

// Initialize an error variable for input handling
$error = '';

// Ambil username dari session
$username = $_SESSION['username'] ?? null; // Ganti dengan nama session yang sesuai

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Dapatkan product_id dari POST
    $product_id = $_POST['product_id'];

    // Ambil user_id dari database berdasarkan username
    if ($username) {
        $stmt = $konek->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();

        // Cek apakah user_id ditemukan
        if (!$user_id) {
            $error = "User tidak ditemukan.";
        }
    } else {
        $error = "Anda harus login untuk memberikan ulasan.";
    }

    // Jika user_id berhasil didapat, lanjutkan ke pengambilan rating dan komentar
    if (empty($error)) {
        $rating = $_POST['rating'];
        $comment = trim($_POST['comment']);

        // Validate comment length (max 500 characters)
        if (strlen($comment) > 500) {
            $error = "Komentar tidak boleh lebih dari 500 karakter.";
        } elseif (!is_numeric($rating) || $rating < 1 || $rating > 5) {
            // Validate rating (must be a number between 1 and 5)
            $error = "Rating harus antara 1 dan 5.";
        } else {
            // Insert review data into the database
            $query = $konek->prepare("INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
            $query->bind_param("iiis", $product_id, $user_id, $rating, $comment);
            
            if ($query->execute()) {
                // Redirect back to the product detail page after adding the review
                header("Location: detail_product.php?product_id=" . $product_id);
                exit();
            } else {
                $error = "Gagal menambahkan ulasan, silakan coba lagi.";
            }
        }
    }
}
?>
