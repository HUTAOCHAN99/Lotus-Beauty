<?php
include('db.php');

// Get the request method
$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod == 'POST') {
    // Check if products are selected for checkout
    if (isset($_POST['selected_products']) && !empty($_POST['selected_products'])) {
        $selectedProducts = $_POST['selected_products'];
        $productIds = $_POST['product_ids'];
        $quantities = $_POST['quantities'];

        $success = true;
        $messages = [];

        foreach ($selectedProducts as $cartId) {
            $productId = $productIds[$cartId];
            $quantity = $quantities[$cartId];

            // Check stock availability and get product name
            $productQuery = $konek->prepare("SELECT name, stock FROM product WHERE product_id = ?");
            $productQuery->bind_param("i", $productId);
            $productQuery->execute();
            $productResult = $productQuery->get_result();

            if ($productResult->num_rows > 0) {
                $productRow = $productResult->fetch_assoc();
                $productName = $productRow['name'];
                $availableStock = $productRow['stock'];

                if ($availableStock >= $quantity) {
                    // Reduce stock in the database
                    $updateStockQuery = $konek->prepare("UPDATE product SET stock = stock - ? WHERE product_id = ?");
                    $updateStockQuery->bind_param("ii", $quantity, $productId);

                    if ($updateStockQuery->execute() && $updateStockQuery->affected_rows > 0) {
                        // Successfully reduced stock
                        $messages[] = "$productName Berhasil Dicheckout.";

                        // Optional: Remove from cart after successful checkout
                        $removeQuery = $konek->prepare("DELETE FROM cart WHERE cart_id = ?");
                        $removeQuery->bind_param("i", $cartId);
                        $removeQuery->execute();
                    } else {
                        $success = false;
                        $messages[] = "Failed to checkout $productName: Error occurred.";
                    }
                } else {
                    $success = false;
                    $messages[] = "Insufficient stock for $productName.";
                }
            } else {
                $success = false;
                $messages[] = "Product ID $productId not found.";
            }
        }

        // Redirect to Home.php if checkout was successful
        if ($success) {
            // Prepare the success message
            $successMessage = implode(", ", $messages);
            echo "<script>
                      alert('$successMessage');
                      window.location.href = 'Home.php';
                    </script>";
        } else {
            echo json_encode(['success' => false, 'messages' => $messages]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No products selected for checkout']);
    }
} elseif ($requestMethod == 'GET') {
    // If it's a direct checkout for a specific product
    $product_id = isset($_GET['checkout_product']) ? (int)$_GET['checkout_product'] : null;
    $quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : null;

    if ($product_id && $quantity) {
        // Check stock availability
        $stockQuery = $konek->prepare("SELECT stock FROM product WHERE product_id = ?");
        $stockQuery->bind_param("i", $product_id);
        $stockQuery->execute();
        $stockResult = $stockQuery->get_result();

        if ($stockResult->num_rows > 0) {
            $stockRow = $stockResult->fetch_assoc();
            $availableStock = $stockRow['stock'];

            if ($availableStock >= $quantity) {
                // Reduce stock in the database
                $updateStockQuery = $konek->prepare("UPDATE product SET stock = stock - ? WHERE product_id = ?");
                $updateStockQuery->bind_param("ii", $quantity, $product_id);

                if ($updateStockQuery->execute() && $updateStockQuery->affected_rows > 0) {
                    // Successfully reduced stock
                    echo json_encode(['success' => true, 'message' => "Product ID $product_id checked out successfully."]);
                } else {
                    echo json_encode(['success' => false, 'message' => "Failed to checkout Product ID $product_id: Error occurred."]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => "Insufficient stock for Product ID $product_id."]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => "Product ID $product_id not found."]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Product ID and quantity are required for direct checkout.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
