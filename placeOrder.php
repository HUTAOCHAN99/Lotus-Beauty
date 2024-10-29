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

<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="SB-Mid-client-zu9OrVu-Hm2fCAJf"></script>
</head>

<body>
    <button id="pay-button">Pay!</button>

    <script type="text/javascript">
        var payButton = document.getElementById('pay-button');
        payButton.addEventListener('click', function() {
            // Memanggil Snap untuk menampilkan popup pembayaran
            window.snap.pay('<?php echo $snapToken; ?>', {
                onSuccess: function(result) {
                    alert("Pembayaran berhasil!");
                    console.log(result);
                    const paymentMethod = result.payment_type; // Ambil metode pembayaran yang digunakan

                    // Ambil data transaksi yang dibutuhkan
                    const transactionData = {
                        order_id: result.order_id, // Mengambil order_id dari hasil pembayaran
                        user_id: '<?php echo $user_id; ?>', // Mengambil user_id dari PHP
                        items: <?php echo json_encode($orderItems); ?>, // Mengambil detail produk
                        payment_method: paymentMethod // Ambil metode pembayaran dari hasil
                    };

                    // Mengirim data transaksi ke server untuk diproses
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
                                // Lakukan redirect atau tindakan lain jika diperlukan
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