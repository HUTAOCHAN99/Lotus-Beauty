<?php

$product = [
    'name' => 'Naik Air Mak',
    'category' => "lanang sepatu",
    'price' => 199.99,
    'image' => '',  // Gambar bisa diisi dengan URL gambar produk
    'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam elementum nunc id erat condimentum bibendum.',
    'stock' => [100],
    'laku' => '4,3+Terjual',
];
$reviews = [
    [
        'name_customer' => 'Kapal Lawd',
        'time' => '2 minggu lalu',
        'comment' => 'tks semoga berhasil',
        'rating' => 5,
        'replies' => []
    ],
    [
        'name_customer' => ' Budiono siregar',
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
    <title>Sliding Product Card</title>
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
            height: 300px
        }

        .card.expanded {
            width: 600px;
        }

        /* Rotating the image on click */
        .rotated {
            transform: rotate(-45deg);
            transition: transform 0.5s ease-in-out;
        }
    </style>

<body>
    <?php 
    include('Header.php');
    ?>
    <div class="bg-gray-100 flex items-center justify-center p-2">
        <div class="bg-white-300 w-full h-1/2 absolute">
            <div class="bg-blue-300 w-full h-1/2 absolute"></div>
        </div>
        <div id="product-card" class="relative bg-white shadow-lg rounded-lg overflow-hidden card">
            <!-- Card Produk -->
            <div class=" bg-blue-500 h-70 flex items-center justify-center flex-1 cursor-pointer"
                onclick="toggleDetails()">
                <img id="product-image" src="<?= $product['image']; ?>" alt="<?= $product['name']; ?>" class="h-32">
            </div>

            <!-- Bagian Detail Produk (Sliding ke kanan) -->
            <div id="product-details" class="product-details bg-white shadow-lg">
                <div class="p-2">
                    <h3 class="text-xl font-semibold"><?= $product['name']; ?></h3>
                    <p class="text-gray-500"><?= $product['category']; ?></p>
                    <p class="text-lg font-bold">$<?= number_format($product['price'], 2); ?></p>
                    <p class="text-sm text-gray-700 mt-2">
                        <?= $product['description']; ?>
                    </p>
                    <div class="mt-2">
                        <div> 
                            <span class="text-sm font-semibold">Stock Tersedia:</span>
                            <span class="text-sm"><?= implode(', ', $product['stock']); ?></span>
                        </div>
                        <span class="text-sm font-semibold">  <?= $product['laku']; ?> </span>
                    </div>
                    <div class="flex w-full"><button class="bg-blue-500 ml-auto w-10">Buy</button></div>
                </div>
            </div>
        </div>

        <script>
            function toggleDetails() {
                var details = document.getElementById("product-details");
                var card = document.getElementById("product-card");
                var image = document.getElementById("product-image");

                // Toggle expanded class for card and details
                details.classList.toggle("expanded");
                card.classList.toggle("expanded");

                // Toggle rotation for product image
                image.classList.toggle("rotated");
            }
        </script>
    </div>
    <div class="bg-gray-100 py-8 px-4">
        <div class="max-w-2xl mx-auto bg-white p-6 shadow-md rounded-md">
            <!-- Sorting dan Informasi Ulasan -->
            <div class="mb-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold">ULASAN PILIHAN</h2>
                <div class="flex items-center space-x-2">
                    <span class="text-gray-500">Urutkan</span>
                    <select class="border border-gray-300 rounded-md p-2">
                        <option value="terbaru">Terbaru</option>
                        <option value="terlama">Terlama</option>
                    </select>
                </div>
            </div>

            <!-- Menampilkan Ulasan -->
            <?php foreach ($reviews as $review): ?>
                <div class="border-b border-gray-200 py-4">
                <span class="font-semibold text-gray-700"><?= $review['name_customer']; ?></span>
                    <div class="flex items-center mb-2">
                        <!-- Rating Bintang -->
                        <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.717 5.3h5.564c.969 0 1.371 1.24.588 1.81l-4.507 3.356 1.718 5.299c.3.921-.755 1.688-1.539 1.118L10 14.347l-4.507 3.356c-.784.57-1.838-.197-1.539-1.118l1.718-5.299-4.507-3.356c-.784-.57-.38-1.81.588-1.81h5.564l1.717-5.3z" />
                            </svg>
                        <?php endfor; ?>
                        <span class="text-sm text-gray-500 ml-2"><?= $review['time']; ?></span>
                    </div>
                    <p class="text-gray-700 mb-2"><?= $review['comment']; ?></p>
                    <div class="flex items-center space-x-4">
                        <button class="text-blue-500 text-sm">Membantu</button>
                        <button class="text-gray-500 text-sm">Lihat Balasan</button>
                    </div>

                    <!-- Menampilkan Balasan Jika Ada -->
                    <?php if (!empty($review['replies'])): ?>
                        <div class="mt-2">
                            <?php foreach ($review['replies'] as $reply): ?>
                                <div class="border-l-2 border-gray-300 pl-4 mt-2">
                                    <div class="flex items-center mb-2">
                                        <span class="font-semibold text-gray-700"><?= $reply['name_replier']; ?></span>
                                        <span class="text-sm text-gray-500 ml-2"><?= $reply['time']; ?></span>
                                    </div>
                                    <p class="text-gray-600"><?= $reply['comment']; ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php 
    include('Footer.php');
    ?>
</body>

</html>