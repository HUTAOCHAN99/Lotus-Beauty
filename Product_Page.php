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
    include('db.php'); // Pastikan Anda sudah menghubungkan ke database
    
    // Ambil data semua produk dari database, pastikan untuk menyertakan product_id
    $result = $konek->query("SELECT product_id, name, image FROM product");

    // Periksa apakah query berhasil
    if ($result === false) {
        echo "<p>Error fetching data: " . $konek->error . "</p>";
        exit();
    }
    ?>

    <section class="max-w-4xl mx-auto p-4">
        <h2 class="text-2xl font-bold mb-6">Daftar Produk</h2>
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