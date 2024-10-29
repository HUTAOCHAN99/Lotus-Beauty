<?php
session_start();
include 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: Landing_Page.php");
    exit();
}

// Check if the user is logged in
$username = $_SESSION['username'];
$sql = "SELECT * FROM users WHERE username = ?";
$userQuery = $konek->prepare($sql);
$userQuery->bind_param("s", $username);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();
$user_id = $user['user_id'];

// Check if there is a success parameter in the URL
$success = isset($_GET['success']) ? $_GET['success'] : '';

// Fetch cart items for the logged-in user
$cartQuery = $konek->prepare("SELECT c.cart_id, p.product_id, p.name, c.quantity, p.price FROM cart c JOIN product p ON c.product_id = p.product_id WHERE c.user_id = ?");
$cartQuery->bind_param("i", $user_id);
$cartQuery->execute();
$cartResult = $cartQuery->get_result();

// Calculate the total price
$totalPrice = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9fafb;
        }
    </style>

    <script>
        // Check if success parameter is set and show alert
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success')) {
                alert("Data berhasil ditambahkan!");
            }
        };
    </script>
</head>

<body>
    <?php include('Header.php'); ?>
    <div class="max-w-4xl w-full mx-auto p-6">
        <h2 class="text-3xl font-bold text-gray-800">Keranjang Belanja</h2>


        <table class="min-w-full mt-6 bg-white border border-gray-200">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Pilih</th>
                    <th class="py-2 px-4 border-b">Produk</th>
                    <th class="py-2 px-4 border-b">Kuantitas</th>
                    <th class="py-2 px-4 border-b">Harga</th>
                    <th class="py-2 px-4 border-b">Total</th>
                    <th class="py-2 px-4 border-b">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $cartResult->fetch_assoc()):
                    $itemTotal = $row['quantity'] * $row['price'];
                    $totalPrice += $itemTotal; // Accumulate total price
                ?>
                    <tr>
                        <td class="py-2 px-4 border-b">
                            <input type="checkbox" name="selected_products[]" value="<?= $row['cart_id'] ?>" onchange="updateTotal()">
                            <input type="hidden" name="product_ids[<?= $row['cart_id'] ?>]" value="<?= $row['product_id'] ?>">
                            <input type="hidden" name="quantities[<?= $row['cart_id'] ?>]" value="<?= $row['quantity'] ?>">
                        </td>
                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['name']) ?></td>
                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['quantity']) ?></td>
                        <td class="py-2 px-4 border-b">Rp <?= number_format($row['price'], 2, ',', '.') ?></td>
                        <td class="py-2 px-4 border-b">Rp <?= number_format($itemTotal, 2, ',', '.') ?></td>
                        <td class="py-2 px-4 border-b">
                            <!-- bagian ini yang salah dia form dalam form -->
                            <form action="remove_from_cart.php" method="POST" style="display: inline;">
                                <input type="hidden" name="cart_id" value="<?= $row['cart_id'] ?>">
                                <button type="submit" class="text-red-500 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <!-- Checkout Form -->
        <form action="placeOrder.php" method="POST">
            <!-- Cart items display -->
            <?php if ($cartResult->num_rows > 0): ?>
                <div class="mt-4">
                    <strong>Total Pembayaran:</strong> Rp <span id="total-payment"><?= number_format($totalPrice, 2, ',', '.') ?></span>
                </div>
                <button type="submit" class="mt-4 bg-blue-500 text-white py-2 px-4 rounded">Checkout</button>
            <?php else: ?>
                <p class="mt-6">Keranjang Anda kosong.</p>
            <?php endif; ?>
        </form>

    </div>

    <script>
        function updateTotal() {
            const checkboxes = document.querySelectorAll('input[name="selected_products[]"]');
            let total = 0;

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const row = checkbox.closest('tr');
                    const price = parseFloat(row.cells[4].innerText.replace('Rp ', '').replace('.', '').replace(',', '.')); // Total cell
                    total += price;
                }
            });

            document.getElementById('total-payment').innerText = total.toLocaleString('id-ID', {
                minimumFractionDigits: 2
            });
        }
    </script>
    <?php include('Footer.php'); ?>
</body>

</html>