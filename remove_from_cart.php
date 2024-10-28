<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: Landing_Page.php");
    exit();
}

// Check if cart_id is provided in the POST request
if (isset($_POST['cart_id'])) {
    $cart_id = $_POST['cart_id'];

    // Prepare and execute the delete query
    $deleteQuery = $konek->prepare("DELETE FROM cart WHERE cart_id = ?");
    $deleteQuery->bind_param("i", $cart_id);

    if ($deleteQuery->execute()) {
        // Redirect back to the cart page with a success message
        header("Location: cart.php?success=deleted");
        exit();
    } else {
        echo "Failed to remove item from the cart. Please try again.";
    }
} else {
    echo "Invalid request. No cart item selected.";
}
?>
