<?php
session_start();

// Memastikan hanya admin dan dokter yang dapat mengakses
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'dokter'])) {
    header("Location: no_access.php");
    exit;
}

// Koneksi ke database
include 'db.php';

// Ambil data produk dari database
$sql = "SELECT * FROM product";
$result = $konek->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

$konek->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">All Products</h1>
    
    <div class="bg-white shadow-lg rounded-xl p-6 mb-6">
        <table class="w-full table-auto">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-4 text-left">ID</th>
                    <th class="p-4 text-left">Name</th>
                    <th class="p-4 text-left">Category</th>
                    <th class="p-4 text-left">Price</th>
                    <th class="p-4 text-left">Stock</th>
                    <th class="p-4 text-left">Sold</th>
                    <th class="p-4 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Periksa jika $products tidak kosong sebelum di-loop
                if (!empty($products)) {
                    foreach ($products as $product) {
                        echo "<tr class='border-b hover:bg-gray-50 transition'>";
                        echo "<td class='p-4'>{$product['product_id']}</td>";
                        echo "<td class='p-4'>{$product['name']}</td>";
                        echo "<td class='p-4'>{$product['category']}</td>";
                        echo "<td class='p-4'>Rp " . number_format($product['price'], 2, ',', '.') . "</td>";
                        echo "<td class='p-4'>{$product['stock']}</td>";
                        echo "<td class='p-4'>{$product['terjual']}</td>";
                        echo "<td class='p-4'>
                                <a href='edit_product.php?id={$product['product_id']}' class='text-blue-500 hover:underline mr-2'>Edit</a>
                                <a href='delete_product.php?id={$product['product_id']}' class='text-red-500 hover:underline'>Delete</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='p-4 text-center text-gray-500'>Tidak ada produk tersedia.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
