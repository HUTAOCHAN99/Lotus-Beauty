<?php
session_start();
include('db.php'); // pastikan koneksi database terhubung
require_once dirname(__FILE__) . '/midtrans-php-master/Midtrans.php';

// Set konfigurasi Midtrans
\Midtrans\Config::$serverKey = 'SB-Mid-server-oV2TXd9n4SKpsc1Z_2BtUKaY';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Pastikan pengguna login
if (!isset($_SESSION['username'])) {
    echo "Silakan login terlebih dahulu.";
    exit();
}

// Ambil user_id dari sesi
$username = $_SESSION['username'];
$userQuery = $konek->prepare("SELECT user_id, full_name, email, phone_number FROM users WHERE username = ?");
$userQuery->bind_param("s", $username);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();

if (!$user) {
    echo "Pengguna tidak ditemukan.";
    exit();
}

$user_id = $user['user_id'];

// Ambil data produk di keranjang untuk user ini
$cartQuery = $konek->prepare("SELECT c.product_id, p.price, c.quantity, p.name FROM cart c JOIN product p ON c.product_id = p.product_id WHERE c.user_id = ?");
$cartQuery->bind_param("i", $user_id);
$cartQuery->execute();
$cartResult = $cartQuery->get_result();

// Inisialisasi total harga dan item detail
$totalAmount = 0;
$orderItems = [];

while ($cartRow = $cartResult->fetch_assoc()) {
    $product_id = $cartRow['product_id'];
    $price = $cartRow['price'];
    $quantity = $cartRow['quantity'];
    $productname = $cartRow['name'];
    $totalItemPrice = $price * $quantity;

    // Tambahkan ke total
    $totalAmount += $totalItemPrice;

    // Tambahkan setiap item ke dalam array detail order
    $orderItems[] = [
        'id' => $product_id,
        'price' => $price,
        'quantity' => $quantity,
        'name' => $productname
    ];
}

// Detail transaksi untuk Midtrans
$params = [
    'transaction_details' => [
        'order_id' => "ORDER-" . time(),
        'gross_amount' => $totalAmount,
    ],
    'item_details' => $orderItems,
    'customer_details' => [
        'first_name' => $user['full_name'],
        'email' => $user['email'],
        'phone' => $user['phone_number'],
    ],
];

try {
    $snapToken = \Midtrans\Snap::getSnapToken($params);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-zu9OrVu-Hm2fCAJf"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Invoice Pembayaran</title>
</head>

<body class="bg-gray-100">
    <div class="max-w-lg mx-auto my-10 p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">Invoice Pembayaran</h2>

        <!-- Daftar produk yang akan dibeli -->
        <div class="mb-4">
            <h3 class="font-semibold text-lg text-gray-700 mb-2">Detail Produk</h3>
            <ul class="divide-y divide-gray-200">
                <?php foreach ($orderItems as $item): ?>
                    <li class="py-4 flex justify-between items-center">
                        <div>
                            <p class="font-medium text-gray-800"><?= htmlspecialchars($item['name']); ?></p>
                            <p class="text-sm text-gray-500">Jumlah: <?= htmlspecialchars($item['quantity']); ?></p>
                        </div>
                        <p class="font-semibold text-gray-600">Rp<?= number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Total harga -->
        <div class="border-t border-gray-300 pt-4 mt-4">
            <div class="flex justify-between items-center">
                <span class="font-semibold text-gray-700">Total Harga:</span>
                <span class="text-lg font-semibold text-gray-800">Rp<?= number_format($totalAmount, 0, ',', '.'); ?></span>
            </div>
        </div>

        <!-- Tombol bayar -->
        <div class="mt-6 text-center">
            <button id="pay-button" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow-md focus:outline-none">
                Bayar Sekarang
            </button>
        </div>
    </div>

    <script type="text/javascript">
        var payButton = document.getElementById('pay-button');
        payButton.addEventListener('click', function() {
            // Memanggil Snap untuk menampilkan popup pembayaran
            window.snap.pay('<?php echo $snapToken; ?>', {
                onSuccess: function(result) {
                    alert("Pembayaran berhasil!");
                    console.log(result);
                    const paymentMethod = result.payment_type;

                    const transactionData = {
                        order_id: result.order_id,
                        user_id: '<?php echo $user_id; ?>',
                        items: <?php echo json_encode($orderItems); ?>,
                        payment_method: paymentMethod
                    };

                    fetch('checkout.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(transactionData)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                window.location.href = 'Home.php';
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                },
                onPending: function(result) {
                    alert("Pembayaran Anda sedang diproses!");
                    console.log(result);
                },
                onError: function(result) {
                    alert("Pembayaran gagal!");
                    console.log(result);
                },
                onClose: function() {
                    alert('Anda menutup popup tanpa menyelesaikan pembayaran');
                }
            });
        });
    </script>
</body>

</html>
