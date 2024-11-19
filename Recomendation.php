<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekomendasi Produk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <style>
        .swiper-container-recommendation {
            width: 100%;
            padding: 0 20px;
            overflow: hidden;
        }

        .swiper-slide-recommendation {
            transition: transform 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 10px;
        }

        .product-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 16px;
            width: 100%;
            height: auto;
        }

        .swiper-button-next-recommendation,
        .swiper-button-prev-recommendation {
            color: black;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
        }
    </style>
</head>

<body>

    <div class="container mx-auto my-8">
        <?php
        include('db.php'); // Koneksi ke database

        function renderProductRecommendations($products)
        {
            ?>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold">Rekomendasi Untukmu</h2>
                <a href="Product_Page.php" class="text-orange-500">Lihat semua</a>
            </div>

            <div class="swiper-container swiper-container-recommendation recommendation-swiper">
                <div class="swiper-wrapper">
                    <?php
                    foreach ($products as $product) {
                        echo '<div class="swiper-slide swiper-slide-recommendation">';
                        echo '<div class="product-card">';

                      
                        $image_data = base64_encode($product['image']); // Konversi data binary ke base64
                        $image_src = "data:image/jpeg;base64," . $image_data; // Tambahkan prefix data URI
                        echo '<img src="' . $image_src . '" alt="' . htmlspecialchars($product['name']) . '" class="w-full h-40 object-cover rounded-md">';
                        echo '<div class="mt-4">';
                        echo '<h3 class="text-sm font-semibold text-gray-800">' . htmlspecialchars($product['name']) . '</h3>';
                        echo '<div class="mt-2">';
                        echo '<span class="text-red-500 font-bold">Rp ' . number_format($product['price'], 0, ',', '.') . '</span>';
                        echo '</div>';
                        echo '<p class="text-gray-600 text-xs mt-1">Terjual ' . htmlspecialchars($product['terjual']) . '</p>';
                        echo '<a href="Detail_Product.php?product_id=' . urlencode($product['product_id']) . '" class="mt-4 bg-orange-500 text-white px-4 py-2 rounded-lg w-full text-center block">Beli Sekarang</a>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
            <?php
        }

        $query = "SELECT * FROM product ORDER BY terjual DESC LIMIT 10"; // Ambil 10 produk terlaris
        $result = $konek->query($query);
        if ($result->num_rows > 0) {
            $products = [];
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            renderProductRecommendations($products);
        } else {
            echo "Tidak ada produk yang ditemukan.";
        }
        ?>
    </div>

    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var swiper = new Swiper('.recommendation-swiper', {
                slidesPerView: 1,
                spaceBetween: 10,
                breakpoints: {
                    300: { slidesPerView: 1.5 },
                    400: { slidesPerView: 2 },
                    640: { slidesPerView: 2.5 },
                    768: { slidesPerView: 3 },
                    1024: { slidesPerView: 4 },
                    1280: { slidesPerView: 5 },
                    1536: { slidesPerView: 6 }
                }
            });
        });
    </script>
</body>

</html>
