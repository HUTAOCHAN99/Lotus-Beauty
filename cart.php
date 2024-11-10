<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: Landing_Page.php");
    exit();
}

$username = $_SESSION['username'];
$sql = "SELECT * FROM users WHERE username = ?";
$userQuery = $konek->prepare($sql);
$userQuery->bind_param("s", $username);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();
$user_id = $user['user_id'];

// Ambil item dari keranjang untuk user yang sedang login
$cartQuery = $konek->prepare("SELECT c.cart_id, p.product_id, p.name, c.quantity, p.price FROM cart c JOIN product p ON c.product_id = p.product_id WHERE c.user_id = ?");
$cartQuery->bind_param("i", $user_id);
$cartQuery->execute();
$cartResult = $cartQuery->get_result();

// Simpan hasil query ke dalam array
$cartItems = [];
while ($row = $cartResult->fetch_assoc()) {
    $cartItems[] = $row;
}

$totalPrice = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Head content here -->
</head>
<body>
    <?php include('Header.php'); ?>
    <div class="max-w-4xl w-full mx-auto p-6 bg-white shadow-md rounded-lg mt-6">
        <h2 class="text-3xl font-bold text-gray-800">Keranjang Belanja</h2>

        <form action="placeOrder.php" method="POST" class="py-4">
            <table class="min-w-full mt-6 ">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-2 px-4 border-b text-left">Pilih</th>
                        <th class="py-2 px-4 border-b text-left">Produk</th>
                        <th class="py-2 px-4 border-b text-left">Kuantitas</th>
                        <th class="py-2 px-4 border-b text-left">Harga</th>
                        <th class="py-2 px-4 border-b text-left">Total</th>
                        <th class="py-2 px-4 border-b text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $row):
                        $itemTotal = $row['quantity'] * $row['price'];
                        $totalPrice += $itemTotal; // Accumulate total price
                    ?>
                        <tr class="hover:bg-gray-100">
                            <td class="py-2 px-4 border-b">
                                <input type="checkbox" name="selected_products[]" value="<?= $row['cart_id'] ?>"
                                    onchange="updateTotal()">
                                <input type="hidden" name="product_ids[<?= $row['cart_id'] ?>]"
                                    value="<?= $row['product_id'] ?>">
                                <input type="hidden" name="quantities[<?= $row['cart_id'] ?>]" value="<?= $row['quantity'] ?>">
                            </td>
                            <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['name']) ?></td>
                            <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['quantity']) ?></td>
                            <td class="py-2 px-4 border-b">Rp <?= number_format($row['price'], 2, ',', '.') ?></td>
                            <td class="py-2 px-4 border-b">Rp <?= number_format($itemTotal, 2, ',', '.') ?></td>
                            <td class="py-2 px-4 border-b">
                                <form action="remove_from_cart.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="cart_id" value="<?= $row['cart_id'] ?>">
                                    <button type="submit" class="text-red-500 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="mt-4">
                <strong>Total Pembayaran:</strong> Rp <span id="total-payment">0</span>
            </div>
            <button type="submit" class="mt-4 bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition">Checkout</button>
        </form>

        <a href="Product_Page.php" class="flex items-center bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition">
            <i class="ri-shopping-cart-fill mr-2"></i>
            Belanja Sekarang
        </a>
    </div>

    <script>
        function updateTotal() {
            const checkboxes = document.querySelectorAll('input[name="selected_products[]"]');
            let total = 0;

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const row = checkbox.closest('tr');
                    const price = parseFloat(row.cells[4].innerText.replace('Rp ', '').replace('.', '').replace(',', '.'));
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
