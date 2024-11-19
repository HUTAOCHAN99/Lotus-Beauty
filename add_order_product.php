<?php
session_start();
include 'db.php';

// cek session 
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$product_id = $_POST['product_id'] ?? null; //jika dikosongkan maka akan diisi dengan null
$quantity = $_POST['quantity'] ?? null;

// Validate input
if (is_null($product_id) || is_null($quantity)) {
    echo json_encode(['success' => false, 'message' => 'Product ID and quantity are required']);
    exit(); // jika id null atau quantity null maka akan hentikan program
}

// mengambil semua data dari users 
$username = $_SESSION['username'];
$sql = "SELECT user_id FROM users WHERE username = ?";
$userQuery = $konek->prepare($sql);
$userQuery->bind_param("s", $username);
$userQuery->execute();
$userResult = $userQuery->get_result();

if ($userResult->num_rows > 0) {
    $userRow = $userResult->fetch_assoc();
    $user_id = $userRow['user_id'];

    // tambahkan produk ke cart 
    $cartQuery = $konek->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $cartQuery->bind_param("iii", $user_id, $product_id, $quantity);

    if ($cartQuery->execute()) {
        // hanya sebatas pesan json kok
        echo json_encode(['success' => true, 'message' => 'Product added to cart']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding product to cart']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}
?>
