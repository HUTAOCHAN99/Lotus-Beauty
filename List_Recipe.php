<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Resep</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <?php include('Header.php'); ?>
    <section class="max-w-6xl mx-auto p-4">
        <?php
        include('db.php'); // Database connection
        
        // Get prescription_id from URL
        $prescription_id = isset($_GET['prescription_id']) ? intval($_GET['prescription_id']) : 0;

        // Fetch prescription details including the product_id
        $query = "SELECT p.nama_resep, p.doctor_name, p.patient_name, p.usage_instructions,p.desc_recipe, p.image_url, p.product_id
              FROM prescription AS p
              WHERE p.prescription_id = $prescription_id";
        $result = $konek->query($query);

        // Check if query succeeded
        if ($result === false || $result->num_rows === 0) {
            echo "<p>Resep tidak ditemukan.</p>";
        } else {
            $resep = $result->fetch_assoc();
            $image_url = !empty($resep['image_url']) ? htmlspecialchars($resep['image_url']) : 'path/to/default-image.png';
            ?>
            <div class="w-full mx-auto flex flex-col items-center">
                <h2 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($resep['nama_resep']); ?></h2>
                <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($resep['nama_resep']); ?>"
                    class="w-32 h-32 mb-4">
                <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                    <div class="mb-4 flex items-center">
                        <i class="ri-stethoscope-line text-gray-600 mr-2"></i>
                        <p class="text-gray-800"><strong>Dokter:</strong>
                            <?php echo htmlspecialchars($resep['doctor_name']); ?></p>
                    </div>
                    <div class="mb-4 flex items-center">
                        <i class="ri-user-line text-gray-600 mr-2"></i>
                        <p class="text-gray-800"><strong>Pasien:</strong>
                            <?php echo htmlspecialchars($resep['patient_name']); ?></p>
                    </div>
                    <div class="mb-4">
                        <div class="flex items-center">
                            <i class="ri-file-list-3-line text-gray-600 mr-2"></i>
                            <p class="text-gray-800"><strong>Instruksi Penggunaan:</strong></p>
                        </div>
                        <p class="text-gray-700 mt-2 whitespace-pre-line">
                            <?php echo nl2br(htmlspecialchars($resep['usage_instructions'])); ?>
                        </p>
                    </div>
                    <div class="mb-4">
                        <div class="flex items-center">
                            <i class="ri-file-text-line text-gray-600 mr-2"></i>
                            <p class="text-gray-800"><strong>Deskripsi:</strong></p>
                        </div>
                        <p class="text-gray-700 mt-2 whitespace-pre-line">
                            <?php echo nl2br(htmlspecialchars($resep['desc_recipe'])); ?>
                        </p>
                    </div>
                </div>


                <h3 class="text-xl font-bold mt-6 mb-4">Produk Terkait</h3>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                    <?php
                    // Fetch related product based on product_id from prescription table
                    $product_id = intval($resep['product_id']);
                    $product_query = "
                SELECT 
                    p.product_id, 
                    p.name, 
                    p.category, 
                    p.price, 
                    p.image,
                    COALESCE(AVG(r.rating), 0) AS average_rating,
                    COALESCE(SUM(dt.jumlah), 0) AS total_sold
                FROM 
                    product AS p
                LEFT JOIN 
                    reviews AS r ON p.product_id = r.product_id
                LEFT JOIN 
                    detail_transaksi AS dt ON p.product_id = dt.product_id
                WHERE 
                    p.product_id = $product_id
                GROUP BY 
                    p.product_id
            ";
                    $product_result = $konek->query($product_query);

                    // Check if the product is found
                    if ($product_result && $product_result->num_rows > 0) {
                        $product = $product_result->fetch_assoc();
                        $product_image = !empty($product['image']) ? htmlspecialchars($product['image']) : 'path/to/default-image.png';
                        ?>
                        <!-- Wrap the card content in an anchor tag -->
                        <div
                            class="bg-white p-4 rounded-lg shadow-md transition-transform duration-200 transform hover:scale-105 flex flex-col items-center">
                            <a href="Detail_Product.php?product_id=<?php echo $product['product_id']; ?>" class="w-full">
                                <!-- Gambar produk di tengah -->
                                <div class="flex justify-center">
                                    <img src="<?php echo htmlspecialchars($product_image); ?>"
                                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                                        class="w-full h-40 object-cover mb-4 rounded">
                                </div>

                                <!-- Nama, Kategori, dan Harga di kiri -->
                                <div class="text-left">
                                    <h3 class="text-lg font-semibold text-gray-800">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </h3>
                                    <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($product['category']); ?></p>
                                    <p class="text-lg font-bold text-gray-900 mb-2">Rp
                                        <?php echo number_format($product['price'], 0, ',', '.'); ?>
                                    </p>
                                </div>

                                <!-- Rating dan Terjual tetap justify-between -->
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
                    } else {
                        echo "<p>Produk terkait tidak ditemukan.</p>";
                    }
                    ?>
                </div>
                <?php
        }
        ?>
    </section>

    <?php include('Footer.php'); ?>
</body>

</html>