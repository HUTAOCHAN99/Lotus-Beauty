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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include('Header.php'); ?>
    <div class="max-w-4xl w-full mx-auto p-6 bg-white shadow-md rounded-lg mt-6">
        <h2 class="text-3xl font-bold text-gray-800">Keranjang Belanja</h2>

        <form action="placeOrder.php" method="POST" class="py-4" id="cartForm">
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const form = document.getElementById('cartForm'); // Pastikan ID form benar
                    if (form) {
                        form.addEventListener('submit', function () {
                            // Cek jika `source` tidak ada, tambahkan sebagai input tersembunyi dengan nilai default
                            if (!document.getElementsByName('source')[0]) {
                                const sourceInput = document.createElement('input');
                                sourceInput.type = 'hidden';
                                sourceInput.name = 'source';
                                sourceInput.value = 'cart_page';
                                form.appendChild(sourceInput);
                            }
                        });
                    }
                });
            </script>
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
                                <input type="hidden" name="quantities[<?= $row['cart_id'] ?>]"
                                    value="<?= $row['quantity'] ?>">
                            </td>
                            <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['name']) ?></td>
                            <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['quantity']) ?></td>
                            <td class="py-2 px-4 border-b">Rp <?= number_format($row['price'], 2, ',', '.') ?></td>
                            <td class="py-2 px-4 border-b">Rp <?= number_format($itemTotal, 2, ',', '.') ?></td>
                            <td class="py-2 px-4 border-b">
                                <button type="button" class="text-red-500 hover:underline"
                                    onclick="confirmDelete(<?= $row['cart_id'] ?>)">Hapus</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="mt-4">
                <strong>Total Pembayaran:</strong> Rp <span id="total-payment">0</span>
            </div>
            <button type="submit"
                class="my-4 bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition">Checkout</button>
        </form>


        <a href="Product_Page.php"
            class="flex items-center bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition">
            <i class="ri-shopping-cart-fill mr-2"></i>
            Belanja Sekarang
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(cartId) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Perform AJAX request to delete item
                    fetch(`remove_from_cart.php?cart_id=${cartId}`, { method: 'GET' })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire("Deleted!", "Your item has been deleted.", "success").then(() => {
                                    location.reload(); // Reload page to reflect changes
                                });
                            } else {
                                Swal.fire("Error!", data.error || "Failed to delete item.", "error");
                            }
                        })
                        .catch(() => {
                            Swal.fire("Error!", "An error occurred. Please try again.", "error");
                        });
                }
            });
        }

        function updateTotal() {
            const checkboxes = document.querySelectorAll('input[name="selected_products[]"]');
            let total = 0;

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const row = checkbox.closest('tr');
                    const price = parseFloat(row.cells[4].innerText.replace('Rp ', '').replace(/\./g, '').replace(',', '.'));
                    total += price;
                }
            });

            document.getElementById('total-payment').innerText = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 2
            }).format(total);
        }
    </script>
    <?php include('Footer.php'); ?>
</body>

</html>