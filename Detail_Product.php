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

$reviewQuery = $konek->prepare("SELECT users.username, reviews.rating, reviews.comment 
                                FROM reviews 
                                JOIN users ON reviews.user_id = users.user_id 
                                WHERE product_id = ?");
$reviewQuery->bind_param("i", $product_id);
$reviewQuery->execute();
$reviews = $reviewQuery->get_result();

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

$error = '';

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

        /* Styling untuk bintang */
        .star {
            font-size: 1.5rem;
            /* Ukuran bintang */
            color: gray;
            /* Warna default untuk bintang */
            cursor: pointer;
            /* Mengubah kursor saat hover */
            transition: color 0.3s ease;
            /* Transisi warna halus */
        }

        /* Warna bintang saat dipilih atau saat di-hover */
        .star.selected,
        .star:hover,
        .star:hover~.star {
            color: gold;
            /* Warna untuk bintang yang dipilih atau saat di-hover */
        }

        /* Mengembalikan warna bintang setelah hover */
        .star:hover~.star {
            color: gray;
            /* Warna default untuk bintang yang tidak di-hover */
        }

        .review {
            border-bottom: 1px solid #ccc;
            padding: 10px 0;
        }

        .review-rating {
            font-weight: bold;
        }

        .reviews-section {
            margin-top: 20px;
        }
    </style>

</head>

<body class="bg-gray-100 ">
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




    <!-- Input Rating Bintang (di luar modal) -->
    <div class="w-1/2 mx-auto">
        <div class="w-1/2 flex justify-center mx-auto">
            <label class="font-semibold">Rate this Product</label>
        </div>
        <div id="outerStarRating" class="w-1/2 mx-auto text-center">
            <span class="star" onclick="openReviewModal(1)">&#9733;</span>
            <span class="star" onclick="openReviewModal(2)">&#9733;</span>
            <span class="star" onclick="openReviewModal(3)">&#9733;</span>
            <span class="star" onclick="openReviewModal(4)">&#9733;</span>
            <span class="star" onclick="openReviewModal(5)">&#9733;</span>
        </div>
    </div>

    <!-- Review Modal (pop-up) - Hidden initially -->
    <div id="reviewModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
        <div id="reviewModalContent" class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Beri Ulasan</h3>
                <button onclick="closeReviewModal()"
                    class="bg-red-500 rounded-full border-0 w-6 h-6 flex items-center justify-center focus:outline-none transition-transform duration-200 ease-in-out hover:scale-110 active:scale-90">
                    <i class="ri-close-line text-white text-3xl"></i>
                </button>
            </div>
            <form action="review_module.php" method="POST">
                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_id); ?>">
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id); ?>">
                <input type="hidden" id="selectedRating" name="rating">

                <!-- Bagian Rating Bintang di Modal -->
                <div class="mb-4 flex">
                    <label class="font-semibold mr-2">Rating:</label>
                    <div id="modalStarRating" class="flex">
                        <span class="star" onclick="setRating(1)">&#9733;</span>
                        <span class="star" onclick="setRating(2)">&#9733;</span>
                        <span class="star" onclick="setRating(3)">&#9733;</span>
                        <span class="star" onclick="setRating(4)">&#9733;</span>
                        <span class="star" onclick="setRating(5)">&#9733;</span>
                    </div>
                </div>

                <!-- Bagian Input Komentar -->
                <div class="mb-4">
                    <label for="comment" class="font-semibold">Komentar:</label>
                    <textarea name="comment" id="comment" rows="4" class="border w-full p-2 rounded"
                        placeholder="Ceritakan pengalaman Anda (Maksimal 500 kata)" maxlength="500"></textarea>
                </div>

                <!-- Tombol Submit -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">Post</button>
                </div>

                <?php if ($error): ?>
                    <p class="text-red-500 mt-2"><?= htmlspecialchars($error); ?></p>
                <?php endif; ?>
            </form>
        </div>
    </div>


    <script>

        let selectedRating = 0;

        function openReviewModal(rating) {

            // Setel rating ketika bintang diklik
            setRating(rating);
            // Tampilkan modal review
            document.getElementById('reviewModal').classList.remove('hidden');
        }

        function setRating(rating) {
            selectedRating = rating;
            document.getElementById('selectedRating').value = rating;

            // Update warna bintang di modal
            let stars = document.querySelectorAll('#modalStarRating .star');
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.add('selected'); // Menandai bintang yang dipilih
                } else {
                    star.classList.remove('selected'); // Menghapus tanda dari bintang yang tidak dipilih
                }
            });

            // Update warna bintang di luar modal
            let outerStars = document.querySelectorAll('#outerStarRating .star');
            outerStars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.add('selected');
                } else {
                    star.classList.remove('selected');
                }
            });
        }

        function closeReviewModal() {
            document.getElementById('reviewModal').classList.add('hidden');
        }

    </script>



    <div class="product-details">
        <h1><?= htmlspecialchars($product['name']); ?></h1>
        <p><?= htmlspecialchars($product['description']); ?></p>
        <p>Harga: <?= htmlspecialchars($product['price']); ?></p>
    </div>

    <div class="reviews-section max-w-2xl mx-auto my-8 p-6 bg-white shadow-md rounded-lg">
    <h2 class="font-bold text-xl text-center mb-6 text-gray-800">Ulasan Produk</h2>
    <?php if ($reviews->num_rows > 0): ?>
        <?php while ($review = $reviews->fetch_assoc()): ?>
            <div class="review border-b border-gray-200 pb-4 mb-4">
                <div class="review-rating flex items-center mb-2">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star <?= $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>">&starf;</span>
                    <?php endfor; ?>
                </div>
                <p class="text-gray-700 mb-1"><?= htmlspecialchars($review['comment']); ?></p>
                <small class="text-gray-500">oleh <?= htmlspecialchars($review['username']); ?></small>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-gray-500 text-center">Tidak ada ulasan untuk produk ini.</p>
    <?php endif; ?>
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