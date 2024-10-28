<?php
session_start();
include 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Get the product ID and quantity from the form submission
$product_id = $_POST['product_id'] ?? null;  // Use null coalescing to handle unset variables
$quantity = $_POST['quantity'] ?? null;

// Validate input
if (is_null($product_id) || is_null($quantity)) {
    echo json_encode(['success' => false, 'message' => 'Product ID and quantity are required']);
    exit();
}

// Retrieve the user ID from the database based on the username in the session
$username = $_SESSION['username'];
$sql = "SELECT user_id FROM users WHERE username = ?";
$userQuery = $konek->prepare($sql);
$userQuery->bind_param("s", $username);
$userQuery->execute();
$userResult = $userQuery->get_result();

if ($userResult->num_rows > 0) {
    $userRow = $userResult->fetch_assoc();
    $user_id = $userRow['user_id'];

    // Insert the product into the cart
    $cartQuery = $konek->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $cartQuery->bind_param("iii", $user_id, $product_id, $quantity);

    if ($cartQuery->execute()) {
        // Return a success response as JSON
        echo json_encode(['success' => true, 'message' => 'Product added to cart']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding product to cart']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}
?>
