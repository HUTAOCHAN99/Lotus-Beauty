<?php
session_start();
include 'db.php';

// Pastikan user memiliki hak akses
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'dokter', 'apoteker', 'cs'])) {
    header("Location: no_access.php");
    exit;
}

// Ambil parameter `type` dan `id`
$type = isset($_GET['type']) ? $_GET['type'] : null;
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$type || !$id) {
    die("Parameter tidak valid.");
}

// Ambil data berdasarkan tipe
if ($type === 'product') {
    $query = "SELECT * FROM product WHERE product_id = ?";
} elseif ($type === 'recipe') {
    $query = "SELECT * FROM prescription WHERE prescription_id = ?";
} else {
    die("Tipe tidak dikenali.");
}

$stmt = $konek->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Data tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($type === 'product') {
        // Update data produk
        $name = htmlspecialchars(trim($_POST['name']));
        $category = htmlspecialchars(trim($_POST['category']));
        $price = floatval($_POST['price']);
        $stock = intval($_POST['stock']);
        $status = htmlspecialchars(trim($_POST['status']));
        $image_path = $data['image']; // Default ke gambar lama

        // Proses upload gambar (jika ada)
        if (!empty($_FILES['image']['name'])) {
            $image_name = $_FILES['image']['name'];
            $image_tmp = $_FILES['image']['tmp_name'];
            $target_directory = "img/product/";
            $target_file = $target_directory . basename($image_name);
            move_uploaded_file($image_tmp, $target_file);
            $image_path = $target_file;
        }

        // Update query
        $update_query = "UPDATE product SET name = ?, category = ?, price = ?, stock = ?, status = ?, image = ? WHERE product_id = ?";
        $stmt = $konek->prepare($update_query);
        $stmt->bind_param("ssdisii", $name, $category, $price, $stock, $status, $image_url, $id);
        if ($stmt->execute()) {
            echo "<script>
                    alert('Produk berhasil diperbarui!');
                    window.location.href = 'all_product.php';
                  </script>";
        } else {
            echo "<script>alert('Gagal memperbarui produk.');</script>";
        }
    } elseif ($type === 'recipe') {
        // Update data resep
        $nama_resep = htmlspecialchars(trim($_POST['nama_resep']));
        $usage_instructions = htmlspecialchars(trim($_POST['usage_instructions']));
        $image_path = $data['image_url'];

        // Proses upload gambar (jika ada)
        if (!empty($_FILES['image']['name'])) {
            $image_name = $_FILES['image']['name'];
            $image_tmp = $_FILES['image']['tmp_name'];
            $target_directory = "img/recipe/";
            $target_file = $target_directory . basename($image_name);
            move_uploaded_file($image_tmp, $target_file);
            $image_path = $target_file;
        }

        // Update query
        $update_query = "UPDATE prescription SET nama_resep = ?, usage_instructions = ?, image_url = ? WHERE prescription_id = ?";
        $stmt = $konek->prepare($update_query);
        $stmt->bind_param("sssi", $nama_resep, $usage_instructions, $image_path, $id);
        if ($stmt->execute()) {
            echo "<script>
                    alert('Resep berhasil diperbarui!');
                    window.location.href = 'all_recipe.php';
                  </script>";
        } else {
            echo "<script>alert('Gagal memperbarui resep.');</script>";
        }
    }
}

$stmt->close();
$konek->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit <?= ucfirst($type); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md mt-10">
        <h1 class="text-2xl font-bold mb-6">Edit <?= ucfirst($type); ?></h1>
        <form action="Edit.php?type=<?= $type; ?>&id=<?= $id; ?>" method="POST" enctype="multipart/form-data">
            <?php if ($type === 'product') : ?>
                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Name</label>
                    <input type="text" name="name" id="name" value="<?= htmlspecialchars($data['name']); ?>"
                        class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label for="category" class="block text-gray-700">Category</label>
                    <input type="text" name="category" id="category" value="<?= htmlspecialchars($data['category']); ?>"
                        class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label for="price" class="block text-gray-700">Price</label>
                    <input type="number" name="price" id="price" value="<?= htmlspecialchars($data['price']); ?>"
                        class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label for="stock" class="block text-gray-700">Stock</label>
                    <input type="number" name="stock" id="stock" value="<?= htmlspecialchars($data['stock']); ?>"
                        class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label for="status" class="block text-gray-700">Status</label>
                    <select name="status" id="status" class="w-full px-4 py-2 border rounded-lg">
                        <option value="active" <?= $data['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="nonactive" <?= $data['status'] === 'nonactive' ? 'selected' : ''; ?>>Nonactive</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="image" class="block text-gray-700">Image</label>
                    <?php if (!empty($data['image'])) : ?>
                        <img src="<?= $data['image']; ?>" alt="Product Image" class="w-32 h-32 object-cover mb-4">
                    <?php endif; ?>
                    <input type="file" name="image" id="image" class="w-full px-4 py-2 border rounded-lg">
                </div>
            <?php elseif ($type === 'recipe') : ?>
                <div class="mb-4">
                    <label for="nama_resep" class="block text-gray-700">Recipe Name</label>
                    <input type="text" name="nama_resep" id="nama_resep" value="<?= htmlspecialchars($data['nama_resep']); ?>"
                        class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label for="usage_instructions" class="block text-gray-700">Usage Instructions</label>
                    <textarea name="usage_instructions" id="usage_instructions" rows="4"
                        class="w-full px-4 py-2 border rounded-lg"><?= htmlspecialchars($data['usage_instructions']); ?></textarea>
                </div>
                <div class="mb-4">
                    <label for="image" class="block text-gray-700">Image</label>
                    <?php if (!empty($data['image_url'])) : ?>
                        <img src="<?= $data['image_url']; ?>" alt="Recipe Image" class="w-32 h-32 object-cover mb-4">
                    <?php endif; ?>
                    <input type="file" name="image" id="image" class="w-full px-4 py-2 border rounded-lg">
                </div>
            <?php endif; ?>
            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg">Save Changes</button>
        </form>
    </div>
</body>

</html>
