<?php
session_start();

// Memastikan hanya admin, dokter, apoteker, dan cs yang dapat mengakses
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'dokter', 'apoteker', 'cs'])) {
    header("Location: no_access.php");
    exit;
}

// Koneksi ke database
include 'db.php';

// Fungsi untuk mengubah status produk jika ada parameter 'delete_id' di URL
if (isset($_GET['change_status_id']) && isset($_GET['action'])) {
    $product_id = intval($_GET['change_status_id']); // Pastikan input aman
    $action = $_GET['action'];
    $new_status = ($action === 'nonaktifkan') ? 'nonactive' : 'active';

    $update_sql = "UPDATE product SET status = '$new_status' WHERE product_id = $product_id";

    if ($konek->query($update_sql) === TRUE) {
        echo "<script>
                Swal.fire({
                    title: 'Updated!',
                    text: 'The product status has been updated.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'all_product.php';
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to update the product status: " . $konek->error . "',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
              </script>";
    }
}


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
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Tambahkan ini -->
</head>

<body class="bg-gray-100">
    <nav class="w-full bg-powderBlue shadow-md p-4 flex justify-between items-center mb-4">
        <!-- Tombol Kembali ke Home -->
        <a href="product_management.php" class="text-black flex items-center space-x-2">
            <i class="ri-arrow-left-line text-xl"></i>
        </a>
        <!-- Nama Halaman -->
        <h1 class="text-gray-800 font-bold text-lg">Semua Product</h1>
        <!-- Placeholder untuk spasi antara tombol kembali dan nama halaman -->
        <div class="w-10"></div>
    </nav>
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
                    <th class="p-4 text-left">Status</th>
                    <th class="p-4 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($products)) {
                    foreach ($products as $product) {
                        $status_label = $product['status'] === 'active' ? 'Active' : 'Nonactive';
                        $action_label = $product['status'] === 'active' ? 'Nonaktifkan' : 'Aktifkan';
                        $action_color = $product['status'] === 'active' ? 'text-red-500' : 'text-green-500';

                        echo "<tr class='border-b hover:bg-gray-50 transition'>";
                        echo "<td class='p-4'>{$product['product_id']}</td>";
                        echo "<td class='p-4'>{$product['name']}</td>";
                        echo "<td class='p-4'>{$product['category']}</td>";
                        echo "<td class='p-4'>Rp " . number_format($product['price'], 2, ',', '.') . "</td>";
                        echo "<td class='p-4'>{$product['stock']}</td>";
                        echo "<td class='p-4'>{$product['terjual']}</td>";
                        echo "<td class='p-4 text-black'>{$status_label}</td>";
                        echo "<td class='p-4'>
        <a href='Edit.php?type=product&id={$product['product_id']}' class='text-blue-500 hover:underline mr-2'>Edit</a>
        <a href='#' onclick=\"confirmStatusChange({$product['product_id']}, '{$action_label}')\" class='{$action_color} hover:underline'>{$action_label}</a>
      </td>";

                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='p-4 text-center text-gray-500'>Tidak ada produk tersedia.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function confirmStatusChange(productId, action) {
            Swal.fire({
                title: `Are you sure to ${action.toLowerCase()} this product?`,
                text: "This action can be reverted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: action === "Nonaktifkan" ? "#d33" : "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: `Yes, ${action.toLowerCase()}!`
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect ke halaman yang menangani perubahan status
                    window.location.href = `all_product.php?change_status_id=${productId}&action=${action.toLowerCase()}`;
                }
            });
        }
    </script>

</body>

</html>