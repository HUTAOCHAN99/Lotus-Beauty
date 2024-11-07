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
    <section class="max-w-4xl mx-auto p-4">
        <?php
        include('db.php'); // Database connection

        // Get prescription_id from URL
        $prescription_id = isset($_GET['prescription_id']) ? intval($_GET['prescription_id']) : 0;

        // Fetch prescription details including the product_id
        $query = "SELECT p.nama_resep, p.doctor_name, p.patient_name, p.usage_instructions, p.image_url, p.product_id
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
            <h2 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($resep['nama_resep']); ?></h2>
            <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($resep['nama_resep']); ?>"
                class="w-32 h-32 mb-4">
            <p><strong>Dokter:</strong> <?php echo htmlspecialchars($resep['doctor_name']); ?></p>
            <p><strong>Pasien:</strong> <?php echo htmlspecialchars($resep['patient_name']); ?></p>
            <p><strong>Instruksi Penggunaan:</strong> <?php echo nl2br(htmlspecialchars($resep['usage_instructions'])); ?>
            </p>

            <h3 class="text-xl font-bold mt-6 mb-4">Produk Terkait</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                <?php
                // Fetch related product based on product_id from prescription table
                $product_id = intval($resep['product_id']);
                $product_query = "SELECT product_id, name, category, price, stock, image 
                      FROM product 
                      WHERE product_id = $product_id";
                $product_result = $konek->query($product_query);

                // Check if the product is found
                if ($product_result && $product_result->num_rows > 0) {
                    $product = $product_result->fetch_assoc();
                    $product_image = !empty($product['image']) ? htmlspecialchars($product['image']) : 'path/to/default-image.png';
                    ?>
                    <!-- Wrap the card content in an anchor tag -->
                    <a href="Detail_Product.php?product_id=<?php echo $product['product_id']; ?>"
                        class="bg-white p-4 rounded-lg shadow-md flex flex-col items-center transition-transform duration-200 transform hover:scale-105">
                        <img src="<?php echo $product_image; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>"
                            class="w-16 h-16 mb-3">
                        <p class="text-sm font-semibold text-gray-800"><?php echo htmlspecialchars($product['name']); ?></p>
                        <p class="text-xs text-gray-500">Kategori: <?php echo htmlspecialchars($product['category']); ?></p>
                        <p class="text-sm font-bold text-green-500">Rp
                            <?php echo number_format($product['price'], 2, ',', '.'); ?></p>
                        <p class="text-xs text-gray-500">Stok: <?php echo htmlspecialchars($product['stock']); ?></p>
                    </a>
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
