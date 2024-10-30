<?php
// Koneksi database
include('db.php'); // Pastikan ini menghubungkan ke database Anda

// Ambil product_id dari URL
$product_id = isset($_GET['product_id']) ? (int) $_GET['product_id'] : 0;

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

// Inisialisasi kuantiti pesanan
$order_quantity = 1; // Default kuantiti

// Dapatkan user_id dari sesi jika ada
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;


// Contoh data ulasan
$reviews = [
    [
        'name_customer' => 'Kapal Lawd',
        'time' => '2 minggu lalu',
        'comment' => 'tks semoga berhasil',
        'rating' => 5,
        'replies' => []
    ],
    [
        'name_customer' => 'Budiono Siregar',
        'time' => '1 bulan lalu',
        'comment' => 'Pertama kali cobain suplemen ini. Komposisi active ingredients nya komplit utk kesehatan persendian. Cocok buat usia 40++ Minus nya cuma satu si, di komposisinya ada pewarna.',
        'rating' => 5,
        'replies' => [
            [
                'name_replier' => 'Reply User',
                'time' => '3 minggu lalu',
                'comment' => 'Terima kasih atas ulasannya!',
            ]
        ]
    ]
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($product['name']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.5.0/remixicon.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom animation styles */
        .product-details {
            flex-basis: 0;
            overflow: hidden;
            transition: flex-basis 0.5s ease-in-out;
        }

        .product-details.expanded {
            flex-basis: 50%;
        }

        .card {
            transition: all 0.5s ease-in-out;
            display: flex;
            width: 250px;
            height: 320px;
        }

        .card.expanded {
            width: 600px;
        }

        .rotated {
            transform: rotate(-45deg);
            transition: transform 0.5s ease-in-out;
        }
    </style>
</head>

<body>
    <?php include('Header.php'); ?>
    <div class="bg-gray-100 flex items-center justify-center p-2">
        <div id="product-card" class="relative bg-white shadow-lg rounded-lg overflow-hidden card">
            <div class="bg-blue-500 h-70 flex items-center justify-center flex-1 cursor-pointer"
                onclick="toggleDetails()">
                <img id="product-image" src="<?= htmlspecialchars($product['image']); ?>"
                    alt="<?= htmlspecialchars($product['name']); ?>" class="h-32">
            </div>
            <div id="product-details" class="product-details bg-white shadow-lg">
                <div class="p-2">
                    <h3 class="text-xl font-semibold"><?= htmlspecialchars($product['name']); ?></h3>
                    <p class="text-gray-500"><?= htmlspecialchars($product['category']); ?></p>
                    <p class="text-lg font-bold">$<?= number_format($product['price'], 2); ?></p>
                    <p class="text-sm text-gray-700 mt-2"><?= htmlspecialchars($product['description']); ?></p>
                    <div class="mt-2">
                        <div>
                            <span class="text-sm font-semibold">Stock Tersedia:</span>
                            <span class="text-sm"><?= implode(', ', (array) $product['stock']); ?></span>
                        </div>
                        <span class="text-sm font-semibold">Terjual:</span>
                        <span class="text-sm font-semibold"><?= htmlspecialchars($product['terjual']); ?>0+</span>
                    </div>
                    <!-- Modifikasi bagian tombol Buy -->
                    <div class="flex w-full py-4">
                        <div class="flex items-center">
                            <a href="javascript:void(0);" onclick="openModal()"
                                class="bg-blue-500 text-white ml-auto px-4 py-2 rounded">Buy</a>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <script>
            function toggleDetails() {
                var details = document.getElementById("product-details");
                var card = document.getElementById("product-card");
                var image = document.getElementById("product-image");
                details.classList.toggle("expanded");
                card.classList.toggle("expanded");
                image.classList.toggle("rotated");
            }

            function changeQuantity(amount) {
                var quantityInput = document.getElementById('order-quantity');
                var currentQuantity = parseInt(quantityInput.value);
                var newQuantity = currentQuantity + amount;

                // Pastikan kuantiti tidak kurang dari 1
                if (newQuantity >= 1) {
                    quantityInput.value = newQuantity;
                }
            }

            // script pengurangan stock,kerangjang,checkout
            // Buka modal ketika tombol "Buy" ditekan
            function openModal() {
                document.getElementById('buyModal').classList.remove('hidden');
            }

            // Tutup modal
            function closeModal() {
                document.getElementById('buyModal').classList.add('hidden');
            }

            // Function to add product to cart
            function addToCart() {
                var quantity = document.getElementById('order-quantity').value;
                var productId = <?= $product_id ?>; // Getting product_id from PHP to JavaScript

                var formData = new FormData();
                formData.append('product_id', productId);
                formData.append('quantity', quantity);

                fetch('add_order_product.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json()) // Parse JSON response
                    .then(data => {
                        if (data.success) {
                            alert("Pesanan berhasil ditambahkan!"); // Alert for success
                            closeModal();
                            window.location.reload(); // Reload page to update stock
                            // Optionally redirect to cart.php
                            window.location.href = 'cart.php'; // Redirect to cart.php after successful addition
                        } else {
                            alert(data.message); // Alert for failure with specific message
                        }
                    })
                    .catch(error => {
                        alert("Error: " + error); // Handle any errors
                    });
            }





            // Fungsi untuk melakukan checkout langsung
            function checkout() {
                var quantity = document.getElementById('order-quantity').value;
                var productId = <?= $product_id ?>; // Mendapatkan product_id dari PHP

                // Mempersiapkan URL untuk dikirim ke checkout.php
                var checkoutUrl = 'checkout.php?checkout_product=' + productId + '&quantity=' + quantity;

                // Menggunakan fetch dengan metode GET
                fetch(checkoutUrl, {
                    method: 'GET',
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Pembelian berhasil!");
                            closeModal();
                            window.location.reload(); // Reload halaman untuk memperbarui stok
                        } else {
                            alert("Gagal melakukan pembelian: " + data.message); // Menampilkan pesan kegagalan
                        }
                    })
                    .catch(error => {
                        alert("Error: " + error); // Tangani error
                    });
            }
        </script>
    </div>
    <div class="bg-gray-100 py-8 px-4">
        <div class="max-w-2xl mx-auto bg-white p-6 shadow-md rounded-md">
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-center">ULASAN PILIHAN</h2>
            </div>
            <?php foreach ($reviews as $review): ?>
                <div class="border-b border-gray-200 py-4">
                    <span class="font-semibold text-gray-700"><?= htmlspecialchars($review['name_customer']); ?></span>
                    <div class="flex items-center mb-2">
                        <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.717 5.3h5.564c.969 0 1.371 1.24.588 1.81l-4.507 3.356 1.718 5.299c.3.921-.755 1.688-1.539 1.118L10 14.347l-4.507 3.356c-.784.57-1.838-.197-1.539-1.118l1.718-5.299-4.507-3.356c-.784-.57-.38-1.81.588-1.81h5.564l1.717-5.3z" />
                            </svg>
                        <?php endfor; ?>
                        <span class="text-sm text-gray-500 ml-2"><?= htmlspecialchars($review['time']); ?></span>
                    </div>
                    <p class="text-gray-700 mb-2"><?= htmlspecialchars($review['comment']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- event buy modal -->
    <div id="buyModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Confirm Purchase</h3>
                <button onclick="closeModal()"
                    class="bg-red-500 rounded-full border-0 w-6 h-6 flex items-center justify-center focus:outline-none transition-transform duration-200 ease-in-out hover:scale-110 active:scale-90">
                    <i class="ri-close-line text-white text-3xl"></i>
                </button>

            </div>
            <div class="mb-4">
                <p><strong>Product:</strong> <?= htmlspecialchars($product['name']); ?></p>
                <span class="text-sm font-semibold">Jumlah Pesanan:</span>
                <div class="flex items-center mx-0">
                    <button id="decrease-quantity" class="bg-gray-200 text-gray-700 rounded-l-md px-2"
                        onclick="changeQuantity(-1)">-</button>
                    <input id="order-quantity" type="number" value="<?= $order_quantity; ?>" min="1"
                        class="border text-center w-16 mx-1" readonly>
                    <button id="increase-quantity" class="bg-gray-200 text-gray-700 rounded-r-md px-2"
                        onclick="changeQuantity(1)">+</button>
                </div>
            </div>
            <div class="flex justify-end">
                <button onclick="addToCart()" class="bg-gray-300 text-gray-700 py-2 px-4 mr-2 rounded">Add to
                    Cart</button>
                <button onclick="checkout()" class="bg-blue-500 text-white py-2 px-4 rounded">Checkout</button>
            </div>
        </div>
    </div>


    <?php include('Footer.php'); ?>
</body>

</html>