<?php

include('db.php');

// Ambil product_id dari URL
$product_id = isset($_GET['product_id']) ? (int) $_GET['product_id'] : 0;

// Query untuk mendapatkan detail produk
$query = $konek->prepare("SELECT * FROM product WHERE product_id = ?");
$query->bind_param("i", $product_id);
$query->execute();
$result = $query->get_result();

$reviewQuery = $konek->prepare("SELECT users.username, reviews.rating, reviews.comment,reviews.created_at 
                                FROM reviews 
                                JOIN users ON reviews.user_id = users.user_id 
                                WHERE product_id = ?");
$reviewQuery->bind_param("i", $product_id);
$reviewQuery->execute();
$reviews = $reviewQuery->get_result();

// Cek apakah produk ditemukan
if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
    $image_data = base64_encode($product['image']);
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* warna custom */
        .bg-powderBlue {
            background-color: #B0E0E6;
        }

        /* Pengaturan animasi */
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
    <div class="block p-4"></div>
    <div class="bg-gray-100 flex items-center justify-center p-2">
        <div id="product-card" class="relative bg-white shadow-lg rounded-lg overflow-hidden card">
            <div class="bg-powderBlue h-70 flex items-center justify-center flex-1 cursor-pointer"
                onclick="toggleDetails()">
                <?php
                            $image_src = "data:image/jpeg;base64," . $image_data; // Tambahkan prefix data URI
                            ?>
                            <img  id="product-image" src="<?= $image_src ?>" alt="<?= htmlspecialchars($row['name']); ?>"
                            class="h-40 shadow-lg shadow-gray-500/50 rounded-lg">
                   
            </div>
            <div id="product-details" class="product-details bg-white shadow-lg">
                <div class="p-2">
                    <h3 class="text-xl font-semibold"><?= htmlspecialchars($product['name']); ?></h3>
                    <p class="text-gray-500"><?= htmlspecialchars($product['category']); ?></p>
                    <p class="text-lg font-bold">$<?= number_format($product['price'], 2); ?></p>
                    <p class="text-sm text-gray-700 mt-2 text-justify"><?= htmlspecialchars($product['description']); ?>
                    </p>
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

            // fUNGSI tambah ke cart
            function addToCart() {
                var quantity = document.getElementById('order-quantity').value;
                var productId = <?= $product_id ?>; 

                var formData = new FormData();
                formData.append('product_id', productId);
                formData.append('quantity', quantity);

                fetch('add_order_product.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: 'Produk berhasil ditambahkan ke keranjang!',
                                showConfirmButton: false,
                                timer: 1500
                            });

                            // Tunggu 1500ms (sesuai timer SweetAlert) sebelum pindah ke halaman cart
                            setTimeout(() => {
                                window.location.href = 'cart.php';
                            }, 1500);
                            // Redirect ke cart.php setelah sukses
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: data.message
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Terjadi kesalahan: ' + error
                        });
                    });
            }


            function checkout() {
                var quantity = document.getElementById('order-quantity').value;
                var productId = <?= $product_id ?>;
                var price = <?= $product['price']; ?>;
                var productName = "<?= htmlspecialchars($product['name']); ?>";

                // Pastikan quantity tidak kosong atau kurang dari 1
                if (!quantity || quantity <= 0) {
                    alert("Jumlah pesanan harus minimal 1");
                    return;
                }

                // Membuat form dinamis untuk mengirim data ke placeOrder.php
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = 'placeOrder.php'; // Arahkan ke placeOrder.php

                // Tambahkan data produk ke form

                var user_id = <?= json_encode($_SESSION['user_id'] ?? null); ?>;
                var source = "detail_product";
                // Format data sesuai yang diterima di placeOrder.php
                var items = [{
                    id: productId,
                    quantity: quantity,
                    price: price,
                    name: productName
                }];

                var inputSource = document.createElement('input');
                inputSource.type = 'hidden';
                inputSource.name = 'source';
                inputSource.value = source;
                form.appendChild(inputSource);

                var inputUserId = document.createElement('input');
                inputUserId.type = 'hidden';
                inputUserId.name = 'user_id';
                inputUserId.value = user_id;
                form.appendChild(inputUserId);

                // Kirimkan data items dalam format yang sesuai
                var inputItems = document.createElement('input');
                inputItems.type = 'hidden';
                inputItems.name = 'items';  // Pastikan key-nya adalah 'items'
                inputItems.value = JSON.stringify(items);  // Mengirimkan data dalam format JSON
                form.appendChild(inputItems);

                // Submit form untuk mengirimkan data ke placeOrder.php
                document.body.appendChild(form);
                form.submit();
            }




        </script>
    </div>




    <!-- Input Rating Bintang (di luar modal) -->
    <div class="w-full md:w-1/2 mx-auto p-4">
        <div class="w-full flex justify-center p-4">
            <label class="font-semibold text-center">Beri nilai untuk produk ini</label>
        </div>
        <div id="outerStarRating" class="w-full md:w-1/2 mx-auto text-center">
            <span class="star cursor-pointer" onclick="openReviewModal(1)">&#9733;</span>
            <span class="star cursor-pointer" onclick="openReviewModal(2)">&#9733;</span>
            <span class="star cursor-pointer" onclick="openReviewModal(3)">&#9733;</span>
            <span class="star cursor-pointer" onclick="openReviewModal(4)">&#9733;</span>
            <span class="star cursor-pointer" onclick="openReviewModal(5)">&#9733;</span>
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
    <div class="reviews-section max-w-2xl mx-auto my-8 p-6 bg-white shadow-md rounded-lg">
        <h2 class="font-bold text-xl text-center mb-6 text-gray-800">Ulasan Produk</h2>
        <?php if ($reviews->num_rows > 0): ?>
            <?php while ($review = $reviews->fetch_assoc()): ?>
                <div class="review border-b border-gray-200 pb-4 mb-4">
                    <div class="review-rating flex items-center mb-2">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star <?= $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>">&starf;</span>
                        <?php endfor; ?>
                        <div class="p-4">
                            <p class="text-sm font-light"><?php
                            // Pastikan untuk mengubah format tanggal sesuai dengan keinginan
                            echo date("m/d/Y", strtotime($review['created_at']));
                            ?></p>
                        </div>
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