<?php
// Koneksi database
include('db.php'); // Pastikan ini menghubungkan ke database Anda

// Ambil product_id dari URL
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

// Query untuk mendapatkan detail produk
$query = $konek->prepare("SELECT * FROM product WHERE product_id = ?");
$query->bind_param("i", $product_id);
$query->execute();
$result = $query->get_result();

// Periksa apakah produk ditemukan
if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    echo "<p>Produk tidak ditemukan.</p>";
    exit();
}

// Contoh data ulasan
$reviews = [
    [
        'name_customer' => 'Kapal Lawd',
        'time' => '2 minggu lalu',
        'comment' => 'tks semoga berhasil',
        'rating' => 5,
    ],
    [
        'name_customer' => 'Budiono Siregar',
        'time' => '1 bulan lalu',
        'comment' => 'Pertama kali cobain suplemen ini. Komposisi active ingredients nya komplit utk kesehatan persendian. Cocok buat usia 40++ Minus nya cuma satu si, di komposisinya ada pewarna.',
        'rating' => 5,
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .product-details {
            display: none;
            transition: all 0.5s ease-in-out;
        }
        .product-details.expanded {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include('Header.php'); ?>
    <div class="flex items-center justify-center p-4">
        <div id="product-card" class="bg-white shadow-lg rounded-lg overflow-hidden w-full max-w-md">
            <div class="bg-blue-500 h-32 flex items-center justify-center cursor-pointer" onclick="toggleDetails()">
                <img id="product-image" src="<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="h-24">
            </div>
            <div id="product-details" class="product-details p-4">
                <h3 class="text-xl font-semibold"><?= htmlspecialchars($product['name']); ?></h3>
                <p class="text-gray-500"><?= htmlspecialchars($product['category']); ?></p>
                <p class="text-lg font-bold">$<?= number_format($product['price'], 2); ?></p>
                <p class="text-sm text-gray-700 mt-2"><?= htmlspecialchars($product['description']); ?></p>
                <div class="mt-2">
                    <span class="text-sm font-semibold">Stock Tersedia:</span>
                    <span class="text-sm"><?= htmlspecialchars($product['stock']); ?></span>
                </div>
                <div class="mt-2">
                    <span class="text-sm font-semibold">Terjual:</span>
                    <span class="text-sm font-semibold"><?= htmlspecialchars($product['terjual']); ?>0+</span>
                </div>
                <div class="flex w-full mt-4">
                    <button class="bg-blue-500 text-white rounded-md py-2 w-full">Buy</button>
                </div>
            </div>
        </div>
        <script>
            function toggleDetails() {
                var details = document.getElementById("product-details");
                details.classList.toggle("expanded");
            }
        </script>
    </div>
    <div class="bg-gray-100 py-8 px-4">
        <div class="max-w-2xl mx-auto bg-white p-6 shadow-md rounded-md">
            <h2 class="text-lg font-semibold mb-4">ULASAN PILIHAN</h2>
            <?php foreach ($reviews as $review): ?>
                <div class="border-b border-gray-200 py-4">
                    <span class="font-semibold text-gray-700"><?= htmlspecialchars($review['name_customer']); ?></span>
                    <span class="text-sm text-gray-500 ml-2"><?= htmlspecialchars($review['time']); ?></span>
                    <p class="text-gray-700 mt-1"><?= htmlspecialchars($review['comment']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php include('Footer.php'); ?>
</body>
</html>
