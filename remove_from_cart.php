<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit();
}

if (!isset($_GET['cart_id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request. No cart item selected.']);
    exit();
}

$cart_id = $_GET['cart_id'];
$deleteQuery = $konek->prepare("DELETE FROM cart WHERE cart_id = ?");
$deleteQuery->bind_param("i", $cart_id);

if ($deleteQuery->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to remove item from the cart.']);
}
$deleteQuery->close();
?>
