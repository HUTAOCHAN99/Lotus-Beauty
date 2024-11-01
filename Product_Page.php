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
    
    // Ambil kata kunci pencarian dari query string
    $search_keyword = isset($_GET['search']) ? $_GET['search'] : '';

    // Query produk, cek apakah ada kata kunci
    if (!empty($search_keyword)) {
        $stmt = $konek->prepare("SELECT product_id, name, image FROM product WHERE name LIKE ?");
        $like_keyword = "%$search_keyword%";
        $stmt->bind_param("s", $like_keyword);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $konek->query("SELECT product_id, name, image FROM product");
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
            <p class="text-gray-700">Hasil pencarian untuk:
                <strong><?php echo htmlspecialchars($search_keyword); ?></strong></p>
        <?php endif; ?>

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
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <?php include('Footer.php'); ?>

</body>

</html>