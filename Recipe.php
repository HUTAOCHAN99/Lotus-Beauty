<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Resep</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <?php include('Header.php'); ?>
    <section class="max-w-6xl mx-auto p-4 ">
        <h2 class="text-2xl font-bold mb-6">Obat Resep</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php
            include('db.php'); // Koneksi database
            
            // Ambil resep unik dari database
            $result = $konek->query("SELECT MIN(prescription_id) AS prescription_id, nama_resep, image_url
FROM prescription
GROUP BY nama_resep");

            // Tampilkan pesan kesalahan jika query gagal
            if ($result === false) {
                echo "<p>Error fetching data: " . $konek->error . "</p>";
                exit();
            }

            // Tampilkan hasil
            if ($result->num_rows === 0) {
                echo "<p>Tidak ada resep yang ditemukan.</p>";
            } else {
                while ($row = $result->fetch_assoc()):
                    $image_data = !empty($row['image_url']) ? base64_encode($row['image_url']) : null; // Konversi data biner ke base64 jika ada
                    $image_src = $image_data ? "data:image/jpeg;base64," . $image_data : 'path/to/default-image.png'; // Default gambar jika tidak ada
                    $prescription_id = $row['prescription_id'];
                    ?>
                    <div
                        class="bg-white p-4 rounded-lg shadow-md flex flex-col items-center transition-transform duration-300 transform hover:scale-105">
                        <a href="List_Recipe.php?prescription_id=<?php echo $prescription_id; ?>"
                            class="block text-center text-sm font-semibold text-black">
                            <img src="<?= $image_src ?>" alt="<?= htmlspecialchars($row['nama_resep']); ?>"
                                class="w-16 h-16 mb-3 mx-auto">
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