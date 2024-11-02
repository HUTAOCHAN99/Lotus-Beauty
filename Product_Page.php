<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <?php
    include('Header.php');
    include('db.php'); // Pastikan sudah terhubung ke database
    
    // Ambil kata kunci pencarian dan pilihan sorting dari query string
    $search_keyword = isset($_GET['search']) ? $_GET['search'] : '';
    $sort_option = isset($_GET['sort']) ? $_GET['sort'] : 'default';

    // Tentukan query dasar
    $query = "SELECT p.product_id, p.name, p.image, p.price, 
                     COALESCE(AVG(r.rating), 0) as average_rating, 
                     COALESCE(SUM(dt.jumlah), 0) as total_sold
              FROM product p
              LEFT JOIN reviews r ON p.product_id = r.product_id
              LEFT JOIN detail_transaksi dt ON p.product_id = dt.product_id";

    // Jika ada kata kunci pencarian, tambahkan kondisi WHERE
    if (!empty($search_keyword)) {
        $query .= " WHERE p.name LIKE ?";
        $like_keyword = "%$search_keyword%";
    }

    // Tentukan sorting berdasarkan pilihan
    switch ($sort_option) {
        case 'rating':
            $query .= " GROUP BY p.product_id ORDER BY average_rating DESC";
            break;
        case 'harga_terendah':
            $query .= " GROUP BY p.product_id ORDER BY p.price ASC";
            break;
        case 'harga_tertinggi':
            $query .= " GROUP BY p.product_id ORDER BY p.price DESC";
            break;
        case 'terlaris':
            $query .= " GROUP BY p.product_id ORDER BY total_sold DESC";
            break;
        case 'terbaru':
            $query .= " GROUP BY p.product_id ORDER BY p.created_at DESC";
            break;
        default:
            $query .= " GROUP BY p.product_id"; // Default grouping
            break;
    }

    // Siapkan dan eksekusi query
    if (!empty($search_keyword)) {
        $stmt = $konek->prepare($query);
        $stmt->bind_param("s", $like_keyword);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $konek->query($query);
    }

    // Periksa apakah query berhasil
    if ($result === false) {
        echo "<p>Error fetching data: " . $konek->error . "</p>";
        exit();
    }
    ?>

    <section class="max-w-4xl mx-auto p-4">
        <h2 class="text-2xl font-bold mb-6">Daftar Produk</h2>

        <!-- Pesan pencarian -->
        <?php if (!empty($search_keyword)): ?>
            <p class="text-gray-700 mb-4">Hasil pencarian untuk:
                <strong><?php echo htmlspecialchars($search_keyword); ?></strong></p>
        <?php endif; ?>

        <!-- Dropdown untuk sorting -->
        <div class="mb-4">
            <label for="sort" class="mr-2">Urutkan berdasarkan:</label>
            <select name="sort" id="sort" onchange="window.location.href='Product_Page.php?search=<?php echo urlencode($search_keyword); ?>&sort=' + this.value" class="border p-2 rounded">
                <option value="default" <?php echo $sort_option === 'default' ? 'selected' : ''; ?>>Default</option>
                <option value="rating" <?php echo $sort_option === 'rating' ? 'selected' : ''; ?>>Rating</option>
                <option value="harga_terendah" <?php echo $sort_option === 'harga_terendah' ? 'selected' : ''; ?>>Harga Terendah</option>
                <option value="harga_tertinggi" <?php echo $sort_option === 'harga_tertinggi' ? 'selected' : ''; ?>>Harga Tertinggi</option>
                <option value="terlaris" <?php echo $sort_option === 'terlaris' ? 'selected' : ''; ?>>Terlaris</option>
                <option value="terbaru" <?php echo $sort_option === 'terbaru' ? 'selected' : ''; ?>>Terbaru</option>
            </select>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div
                    class="bg-white p-4 rounded-lg shadow-md flex flex-col items-center transition-transform duration-200 transform hover:scale-105">
                    <a href="Detail_Product.php?product_id=<?php echo $row['product_id']; ?>"
                        class="flex flex-col items-center">
                        <img src="<?php echo htmlspecialchars($row['image']); ?>"
                            alt="<?php echo htmlspecialchars($row['name']); ?>" class="w-16 h-16 mb-3">
                        <p class="text-center text-sm font-semibold text-black-500">
                            <?php echo htmlspecialchars($row['name']); ?>
                        </p>
                        <p class="text-center text-sm text-gray-500">
                            Rating: <?php echo number_format($row['average_rating'], 1); ?>
                        </p>
                        <p class="text-center text-sm text-gray-500">
                            Terjual: <?php echo $row['total_sold']; ?>
                        </p>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <?php include('Footer.php'); ?>

</body>

</html>
