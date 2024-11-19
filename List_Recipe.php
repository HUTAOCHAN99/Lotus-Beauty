<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Resep</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <?php include('Header.php'); ?>

    <section class="max-w-8xl mx-auto p-4">
        <?php
        include('db.php'); // Koneksi database
        
        // Memeriksa koneksi database
        if ($konek->connect_error) {
            die("Koneksi database gagal: " . $konek->connect_error);
        }

        // Mendapatkan prescription_id dari URL
        $prescription_id = isset($_GET['prescription_id']) ? intval($_GET['prescription_id']) : 0;

        // Query untuk mengambil detail resep berdasarkan prescription_id
        $query = "SELECT nama_resep, doctor_name, usage_instructions, desc_recipe, image_url, product_id 
                  FROM prescription WHERE prescription_id = $prescription_id LIMIT 1";
        $result = $konek->query($query);

        // Memeriksa apakah query berhasil
        if (!$result || $result->num_rows === 0) {
            echo "<p>Resep tidak ditemukan.</p>";
        } else {
            $resep = $result->fetch_assoc();
            $image_url = !empty($resep['image_url']) ? htmlspecialchars($resep['image_url']) : 'path/to/default-image.png';
            ?>

            <div class="w-full mx-auto flex flex-col items-center">
                <div class="bg-white w-1/2 p-4 rounded-lg shadow-md mb-6 mt-4">
                    <h2 class="text-2xl font-bold mb-4 text-center"><?php echo htmlspecialchars($resep['nama_resep']); ?>
                    </h2>

                    <?php
                    $image_data = base64_encode($resep['image_url']); // Konversi data binary ke base64
                    $image_src = "data:image/jpeg;base64," . $image_data; // Tambahkan prefix data URI
                    ?>

                    <img src="<?= $image_src ?>" alt="<?php echo htmlspecialchars($resep['nama_resep']); ?>"
                        class="w-32 h-32 m-auto">

                    <div class="mb-4 flex items-center">
                        <i class="ri-stethoscope-line text-gray-600 mr-2"></i>
                        <p class="text-gray-800"><strong>Dokter:</strong>
                            <?php echo htmlspecialchars($resep['doctor_name']); ?></p>
                    </div>
                    <div class="mb-4">
                        <div class="flex items-center">
                            <i class="ri-file-list-3-line text-gray-600 mr-2"></i>
                            <p class="text-gray-800"><strong>Instruksi Penggunaan:</strong></p>
                        </div>
                        <p class="text-gray-700 mt-2"><?php echo nl2br(htmlspecialchars($resep['usage_instructions'])); ?>
                        </p>
                    </div>
                    <div class="mb-4">
                        <div class="flex items-center">
                            <i class="ri-file-text-line text-gray-600 mr-2"></i>
                            <p class="text-gray-800"><strong>Deskripsi:</strong></p>
                        </div>
                        <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($resep['desc_recipe'])); ?></p>
                    </div>
                </div>

                <h3 class="text-xl font-bold mt-6 mb-4">Produk Terkait</h3>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                    <?php
                    // Mengambil semua produk terkait dengan nama resep yang sama
                    $nama_resep = $resep['nama_resep'];
                    $product_query = "
                    SELECT p.product_id, p.name, p.category, p.price, p.image, 
                           COALESCE(AVG(r.rating), 0) AS average_rating, 
                           COALESCE(SUM(dt.jumlah), 0) AS total_sold
                    FROM product AS p
                    LEFT JOIN reviews AS r ON p.product_id = r.product_id
                    LEFT JOIN detail_transaksi AS dt ON p.product_id = dt.product_id
                    JOIN prescription AS pr ON p.product_id = pr.product_id
                    WHERE pr.nama_resep = '$nama_resep'
                    GROUP BY p.product_id";
                    $product_result = $konek->query($product_query);

                    // Memeriksa apakah produk ditemukan
                    if ($product_result && $product_result->num_rows > 0) {
                        while ($product = $product_result->fetch_assoc()) {
                            $product_image = !empty($product['image']) ? htmlspecialchars($product['image']) : 'path/to/default-image.png';
                            ?>
                            <!-- Card Produk -->
                            <div
                                class="bg-white p-4 rounded-lg shadow-md transition-transform duration-200 transform hover:scale-105 flex flex-col items-center">
                                <a href="Detail_Product.php?product_id=<?php echo $product['product_id']; ?>" class="w-full">
                                    <!-- Gambar produk -->
                                    <div class="flex justify-center">

                                        <?php
                                        $image_data = base64_encode($product['image']); // Konversi data binary ke base64
                                        $image_src = "data:image/jpeg;base64," . $image_data; // Tambahkan prefix data URI
                                        ?>
                                        <img src="<?= $image_src ?>" alt="<?php echo htmlspecialchars($product['name']); ?>"
                                            class="w-full h-40 object-cover mb-4 rounded">
                                    </div>

                                    <!-- Nama, Kategori, dan Harga -->
                                    <div class="text-left">
                                        <h3 class="text-lg font-semibold text-gray-800">
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </h3>
                                        <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($product['category']); ?></p>
                                        <p class="text-lg font-bold text-gray-900 mb-2">Rp
                                            <?php echo number_format($product['price'], 0, ',', '.'); ?>
                                        </p>
                                    </div>

                                    <!-- Rating dan Terjual -->
                                    <div class="flex justify-between w-full">
                                        <p class="text-sm text-yellow-500 font-semibold">
                                            Rating:
                                            <?php echo isset($product['average_rating']) ? number_format($product['average_rating'], 1) : 'N/A'; ?>
                                            ★
                                        </p>
                                        <p class="text-sm text-gray-500 font-medium">
                                            Terjual: <?php echo isset($product['total_sold']) ? $product['total_sold'] : 0; ?>
                                        </p>
                                    </div>
                                </a>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p>Produk terkait tidak ditemukan.</p>";
                    }
                    ?>
                </div>
            </div>

            <?php
        }
        ?>
    </section>

    <?php include('Footer.php'); ?>
</body>

</html>