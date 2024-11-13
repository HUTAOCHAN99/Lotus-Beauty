<?php
session_start();

// Memastikan hanya admin dan dokter yang dapat mengakses
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'dokter'])) {
    header("Location: no_access.php");
    exit;
}

include 'db.php';

// Ambil data resep dari database
$sql = "SELECT * FROM prescription"; // Ganti 'prescription' dengan nama tabel yang benar
$result = $konek->query($sql);

$recipes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recipes[] = $row;
    }
}

$konek->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Recipes</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">All Recipes</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        // Periksa jika $recipes tidak kosong sebelum di-loop
        if (!empty($recipes)) {
            foreach ($recipes as $recipe) {
                echo "<div class='bg-white shadow-lg rounded-xl p-6 hover:bg-gray-50 transition'>";
                echo "<img src='{$recipe['image_url']}' alt='Recipe Image' class='w-full h-40 object-cover rounded-lg mb-4'>";
                echo "<h2 class='font-semibold text-xl text-gray-800 mb-2'>{$recipe['nama_resep']}</h2>";
                echo "<p class='text-gray-600 mb-4'>Product ID: {$recipe['product_id']}</p>";
                echo "<p class='text-gray-700 text-sm'>{$recipe['usage_instructions']}</p>";
                echo "</div>";
            }
        } else {
            echo "<p class='text-gray-500'>Tidak ada resep yang tersedia.</p>";
        }
        ?>
    </div>
</body>
</html>
