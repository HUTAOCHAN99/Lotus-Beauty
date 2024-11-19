<?php
session_start();
include 'db.php';

// Pastikan user memiliki hak akses
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'dokter', 'apoteker', 'cs'])) {
    header("Location: no_access.php");
    exit;
}

// Ambil parameter type dan id
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

    $products = [];
    $productResult = $konek->query("SELECT product_id, name FROM product");
    while ($row = $productResult->fetch_assoc()) {
        $products[] = $row;
    }
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
        // Ambil data form
        $name = htmlspecialchars(trim($_POST['name']));
        $category = htmlspecialchars(trim($_POST['category']));
        $description = isset($_POST['description_p']) ? htmlspecialchars(trim($_POST['description_p'])) : '';
        $price = floatval($_POST['price']);
        $stock = intval($_POST['stock']);
        $status = htmlspecialchars(trim($_POST['status']));


        // Proses upload gambar (jika ada)
        if (!empty($_FILES['image']['tmp_name'])) {
            // Pastikan file yang diunggah adalah gambar
            $allowed_extensions = ['jpg', 'jpeg', 'png'];
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

            if (in_array(strtolower($file_extension), $allowed_extensions)) {
                // Baca file gambar sebagai biner
                $image_data = file_get_contents($_FILES['image']['tmp_name']);
            } else {
                // Jika file bukan gambar
                echo "<script>alert('Hanya file gambar (jpg, jpeg, png) yang diperbolehkan.');</script>";
                exit;
            }
        }

        // Update query
        $update_query = "UPDATE product SET name = ?, category = ?, price = ?, description = ? , stock = ?, status = ?, image = ? WHERE product_id = ?";
        $editproses = $konek->prepare($update_query);

        // Dengan gambar
        $editproses->bind_param("ssdsisbi", $name, $category, $price, $description, $stock, $status, $null, $id);
        $editproses->send_long_data(6, $image_data);


        if ($editproses->execute()) {
            echo "<script>
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Produk berhasil diperbarui!',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = 'all_product.php';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal memperbarui produk',
                    text: 'Error: " . $editproses->error . "'
                });
            </script>";
        }
    }
    // Bagian untuk menangani recipe
    else if ($type === 'recipe') {
        // Ambil data form
        $nama_resep = htmlspecialchars(trim($_POST['nama_resep']));
        $doctor_name = htmlspecialchars(trim($_POST['doctor_name']));
        $usage_instructions = htmlspecialchars(trim($_POST['usage_instructions']));
        $desc_recipe = htmlspecialchars(trim($_POST['desc_recipe']));
        $status = htmlspecialchars(trim($_POST['status']));
        $product_ids = isset($_POST['selected_product_ids']) ? explode(',', $_POST['selected_product_ids']) : [];


        if (!empty($_FILES['image']['tmp_name'])) {
            // Pastikan file yang diunggah adalah gambar
            $allowed_extensions = ['jpg', 'jpeg', 'png'];
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

            if (in_array(strtolower($file_extension), $allowed_extensions)) {
                // Baca file gambar sebagai biner
                $image_data = file_get_contents($_FILES['image']['tmp_name']);
            } else {
                // Jika file bukan gambar
                echo "<script>alert('Hanya file gambar (jpg, jpeg, png) yang diperbolehkan.');</script>";
                exit;
            }
        }

        // Query pembaruan
        $update_query = "UPDATE prescription 
                 SET nama_resep = ?, doctor_name = ?, usage_instructions = ?, desc_recipe = ?, status = ?, product_id = ?, image_url = ?
                 WHERE prescription_id = ?";
        $editproses = $konek->prepare($update_query);

        foreach ($product_ids as $product_id) {
            if (!in_array($product_id, array_column($products, 'product_id'))) {
                echo "<script>alert('Produk dengan ID $product_id tidak valid.');</script>";
                exit;
            }
            $editproses->bind_param("sssssibi", $nama_resep, $doctor_name, $usage_instructions, $desc_recipe, $status, $product_id, $null, $id);
        
            // Kirim data gambar secara manual jika ada
            if (!empty($image_data)) {
                $editproses->send_long_data(6, $image_data); // Indeks ke-6 untuk gambar
            }
        
            if (!$editproses->execute()) {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal memperbarui resep',
                        text: 'Error: " . $editproses->error . "'
                    });
                </script>";
                exit;
            }
        }

    }
}


$stmt->close();
$konek->close();
?>

<!-- HTML Form untuk Edit Produk -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit <?= ucfirst($type); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body class="bg-gray-100">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md mt-10">
        <h1 class="text-2xl font-bold mb-6">Edit <?= ucfirst($type); ?></h1>
        <form action="Edit.php?type=<?= $type; ?>&id=<?= $id; ?>" method="POST" enctype="multipart/form-data">
            <?php if ($type === 'product'): ?>
                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Name</label>
                    <input type="text" name="name" id="name" value="<?= htmlspecialchars($data['name']); ?>"
                        class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label for="category" class="block text-gray-700">Category</label>
                    <select name="category" id="category" class="w-full px-4 py-2 border rounded-lg">
                        <option value="">-- Pilih Kategori Herbal --</option>
                        <?php
                        // list kategori
                        $categories = [
                            "Herbal untuk Perawatan Kulit",
                            "Herbal untuk Perawatan Rambut",
                            "Herbal untuk Kesehatan Kuku",
                            "Herbal untuk Perawatan Tubuh",
                            "Herbal untuk Perawatan Mata",
                            "Herbal untuk Detoksifikasi",
                            "Herbal untuk Vitalitas dan Keseimbangan Hormon",
                            "Herbal untuk Pengharum Tubuh Alami",
                        ];
                        foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category); ?>" <?= $data['category'] === $category ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($category); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="price" class="block text-gray-700">Price</label>
                    <input type="number" name="price" id="price" value="<?= htmlspecialchars($data['price']); ?>"
                        class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label for="description_p" class="block text-gray-700">Description</label>
                    <textarea name="description_p" id="description_p" rows="4"
                        class="w-full px-4 py-2 border rounded-lg"><?= $description = isset($_POST['description_p']) ? htmlspecialchars(trim($_POST['description_p'])) : ''; ?></textarea>
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
                    <?php if (!empty($data['image'])): ?>
                        <?php
                        $image_data = base64_encode($data['image']); // Konversi data biner ke base64
                        $image_src = "data:image/jpeg;base64," . $image_data; // Tambahkan prefix data URI
                        ?>
                        <img src="<?= $image_src ?>" alt="Product Image" class="h-40 shadow-lg shadow-gray-500/50 rounded-lg">
                    <?php endif; ?>
                    <input type="file" name="image" id="image" class="w-full px-4 py-2 border rounded-lg">
                </div>
            <?php elseif ($type === 'recipe'): ?>
                <div class="mb-4">
                    <label for="nama_resep" class="block text-gray-700">Recipe Name</label>
                    <input type="text" name="nama_resep" id="nama_resep"
                        value="<?= htmlspecialchars($data['nama_resep']); ?>" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label for="doctor_name" class="block text-gray-700">Doctor Name</label>
                    <input type="text" name="doctor_name" id="doctor_name"
                        value="<?= htmlspecialchars($data['doctor_name']); ?>" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label for="usage_instructions" class="block text-gray-700">Usage Instructions</label>
                    <textarea name="usage_instructions" id="usage_instructions" rows="4"
                        class="w-full px-4 py-2 border rounded-lg"><?= htmlspecialchars($data['usage_instructions']); ?></textarea>
                </div>
                <div class="mb-4">
                    <label for="desc_recipe" class="block text-gray-700">Description</label>
                    <textarea name="desc_recipe" id="desc_recipe" rows="4"
                        class="w-full px-4 py-2 border rounded-lg"><?= htmlspecialchars($data['desc_recipe']); ?></textarea>
                </div>
                <div class="mb-4">
                    <label for="status" class="block text-gray-700">Status</label>
                    <select name="status" id="status" class="w-full px-4 py-2 border rounded-lg">
                        <option value="active" <?= $data['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?= $data['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>




                <!-- Pilih Produk Herbal -->
                <div class="mb-4">
                    <label class="block text-gray-700">Pilih Produk Herbal</label>

                    <!-- Dropdown Pilihan Produk Herbal -->
                    <div class="relative">
                        <select id="productSelect" class="w-full px-4 py-2 border rounded-lg">
                            <option value="">-- Pilih Produk --</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['product_id']; ?>">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <span class="error-message">Minimal satu produk herbal wajib dipilih.</span>

                    <!-- Placeholder Produk yang Terpilih -->
                    <div id="selectedProducts" class="mt-4 flex flex-wrap"></div>

                    <!-- Input Tersembunyi untuk Menyimpan Produk Terpilih -->
                    <input type="hidden" name="selected_product_ids" id="selectedProductIds">

                </div>




                <script>
                    const selectedProducts = [];
                    const selectedProductsContainer = document.getElementById('selectedProducts');
                    const productSelect = document.getElementById('productSelect');
                    const selectedProductIdsInput = document.getElementById('selectedProductIds');

                    productSelect.addEventListener('change', function () {
                        const selectedOption = this.options[this.selectedIndex];
                        const productId = selectedOption.value;
                        const productName = selectedOption.text;

                        if (productId && !selectedProducts.some(product => product.id === productId)) {
                            selectedProducts.push({ id: productId, name: productName });
                            updateSelectedProductsDisplay();
                        }

                        // Reset dropdown to placeholder option
                        this.selectedIndex = 0;
                    });

                    function updateSelectedProductsDisplay() {
                        selectedProductsContainer.innerHTML = '';
                        selectedProducts.forEach(product => {
                            const productDiv = document.createElement('div');
                            productDiv.className = 'selected-product';
                            productDiv.innerHTML = `
                            <span>${product.name}</span>
                            <span class="remove-product" onclick="removeProduct('${product.id}')">×</span>
                        `;
                            selectedProductsContainer.appendChild(productDiv);
                        });

                        // Update the hidden input value with selected product IDs
                        selectedProductIdsInput.value = selectedProducts.map(product => product.id).join(',');
                    }

                    function removeProduct(productId) {
                        const index = selectedProducts.findIndex(product => product.id === productId);
                        if (index > -1) {
                            selectedProducts.splice(index, 1);
                            updateSelectedProductsDisplay();
                        }
                    }
                </script>
                
                <div class="mb-4">
                    <label for="image" class="block text-gray-700">Image</label>
                    <?php if (!empty($data['image_url'])): ?>
                        <?php
                        $image_data = base64_encode($data['image_url']);
                        $image_src = "data:image/jpeg;base64," . $image_data;
                        ?>
                        <img src="<?= $image_src ?>" alt="Recipe Image" class="h-40 shadow-lg shadow-gray-500/50 rounded-lg">
                    <?php endif; ?>
                    <input type="file" name="image" id="image" class="w-full px-4 py-2 border rounded-lg">
                </div>
            <?php endif; ?>

            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg">Save Changes</button>
        </form>
    </div>
</body>

</html>