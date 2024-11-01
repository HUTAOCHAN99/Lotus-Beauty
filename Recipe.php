<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Resep</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<?php include('Header.php'); ?>
<section class="max-w-4xl mx-auto p-4">
    <h2 class="text-2xl font-bold mb-6">Obat Resep</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php
        include('db.php'); // Pastikan Anda sudah menghubungkan ke database

        // Ambil data semua resep dari database, termasuk prescription_id
        $result = $konek->query("SELECT prescription_id, nama_resep, image_url FROM prescription");

        // Periksa apakah query berhasil
        if ($result === false) {
            echo "<p>Error fetching data: " . $konek->error . "</p>";
            exit();
        }

        // Cek jumlah hasil
        if ($result->num_rows === 0) {
            echo "<p>Tidak ada resep yang ditemukan.</p>";
        } else {
            while ($row = $result->fetch_assoc()):
                $image_url = !empty($row['image_url']) ? htmlspecialchars($row['image_url']) : 'path/to/default-image.png';
                $prescription_id = $row['prescription_id'];
                ?>
                <div class="bg-white p-4 rounded-lg shadow-md flex flex-col items-center">
                    <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($row['nama_resep']); ?>" class="w-16 h-16 mb-3">
                    <a href="List_Recipe.php?prescription_id=<?php echo $prescription_id; ?>" class="text-center text-sm font-semibold text-blue-500 hover:underline">
                        <?php echo htmlspecialchars($row['nama_resep']); ?>
                    </a>
                </div>
            <?php endwhile;
        }
        ?>
    </div>
</section>
<?php include('Footer.php'); ?>
</body>
</html>
