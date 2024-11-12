<?php
session_start();
include 'db.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: Landing_Page.php");
    exit();
}

// Ambil kategori dari parameter URL
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Ambil semua produk dari database
$products = [];
$productResult = $konek->query("SELECT product_id, name FROM product");
while ($row = $productResult->fetch_assoc()) {
    $products[] = $row;
}

// Ambil daftar dokter dari tabel users
$doctors = [];
$doctorResult = $konek->query("SELECT user_id, full_name FROM users WHERE role = 'dokter'");
while ($row = $doctorResult->fetch_assoc()) {
    $doctors[] = $row;
}

// Kategori herbal
$herbalCategories = [
    'Herbal untuk Perawatan Kulit',
    'Herbal untuk Perawatan Rambut',
    'Herbal untuk Detoksifikasi',
    'Herbal untuk Kesehatan Umum',
    'Herbal untuk Mengurangi Stres'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $created_at = date("Y-m-d H:i:s");
    $updated_at = date("Y-m-d H:i:s");
    $selected_category = htmlspecialchars($_POST['herbal_category']);

    // Inisialisasi variabel untuk mengecek keberhasilan upload
    $uploadSuccess = false;

    // Penanganan untuk kategori 'obat-herbal'
    if ($category === 'obat-herbal') {
        $name = htmlspecialchars($_POST['name']);
        $price = htmlspecialchars($_POST['price']);
        $description = htmlspecialchars($_POST['description']);
        $stock = htmlspecialchars($_POST['stock']);

        // Upload gambar
        $image = $_FILES['image'];
        $imagePath = 'img/product/' . basename($image['name']);

        if (move_uploaded_file($image['tmp_name'], $imagePath)) {
            $stmt = $konek->prepare("INSERT INTO product (name, category, price, description, stock, image, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdsisss", $name, $selected_category, $price, $description, $stock, $imagePath, $created_at, $updated_at);
            $stmt->execute();
            $uploadSuccess = $stmt->affected_rows > 0;
            $stmt->close();
        } else {
            echo "<p class='text-red-500'>Error uploading image.</p>";
            exit();
        }
    }
    // Penanganan untuk kategori 'resep'
    elseif ($category === 'resep') {
        $nama_resep = htmlspecialchars($_POST['nama_resep']);
        $doctor_name = htmlspecialchars($_POST['doctor_name']);
        $usage_instructions = htmlspecialchars($_POST['usage_instructions']);
        $desc_recipe = htmlspecialchars($_POST['desc_recipe']);
        $product_ids = isset($_POST['selected_product_ids']) ? explode(',', $_POST['selected_product_ids']) : [];

        if (!empty($product_ids)) {
            $image = $_FILES['image'];
            $imagePath = 'img/resep/' . basename($image['name']);
            if (move_uploaded_file($image['tmp_name'], $imagePath)) {
                // Loop through each selected product and insert it as a new row in the prescription table
                $stmt = $konek->prepare("INSERT INTO prescription (nama_resep, doctor_name, usage_instructions, product_id, created_at, updated_at, desc_recipe, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

                foreach ($product_ids as $product_id) {
                    $stmt->bind_param("sssiisss", $nama_resep, $doctor_name, $usage_instructions, $product_id, $created_at, $updated_at, $desc_recipe, $imagePath);
                    $stmt->execute();
                }

                $uploadSuccess = $stmt->affected_rows > 0;
                $stmt->close();
            } else {
                echo "<p class='text-red-500'>Error uploading image.</p>";
                exit();
            }
        } else {
            echo "<p class='text-red-500'>Minimal satu produk herbal wajib dipilih.</p>";
            exit();
        }
    }


    // Tampilkan SweetAlert jika upload berhasil
    if ($uploadSuccess) {
        $successMessage = true;
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Product</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .error-message {
            color: #ff0000;
            font-size: 0.875rem;
            display: none;
        }
    </style>
    <script>
        function validateForm(event) {
            const form = event.target;
            let isValid = true;

            form.querySelectorAll(".error-message").forEach(element => {
                element.style.display = "none";
            });

            form.querySelectorAll("[name]").forEach(input => {
                const errorMessage = input.nextElementSibling;
                if (!input.value.trim()) {
                    isValid = false;
                    errorMessage.style.display = "block";
                }
            });

            if (!isValid) {
                event.preventDefault();
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            <?php if (isset($successMessage) && $successMessage): ?>
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Produk berhasil diunggah',
                    showConfirmButton: false,
                    timer: 1500
                }).then(function () {
                    window.location.href = 'dashboard.php?success=1';
                });
            <?php endif; ?>
        });
    </script>
</head>

<body class="bg-gray-100">
    <div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md mt-10">
        <h2 class="text-2xl font-bold mb-6 text-center">
            <?php echo htmlspecialchars($category === 'resep' ? 'Upload Resep' : 'Upload Obat Herbal'); ?>
        </h2>

        <form method="POST" action="upload.php?category=<?php echo htmlspecialchars($category); ?>"
            enctype="multipart/form-data" onsubmit="validateForm(event)">

            <!-- Kategori Herbal -->
            <div class="mb-4">
                <label class="block text-gray-700">Pilih Kategori Herbal</label>
                <select name="herbal_category" class="w-full px-4 py-2 border rounded-lg">
                    <option value="">-- Pilih Kategori Herbal --</option>
                    <?php foreach ($herbalCategories as $herbalCategory): ?>
                        <option value="<?php echo htmlspecialchars($herbalCategory); ?>">
                            <?php echo htmlspecialchars($herbalCategory); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="error-message">Kategori herbal wajib dipilih.</span>
            </div>

            <!-- Bagian untuk resep -->
            <?php if ($category === 'resep'): ?>
                <div class="mb-4">
                    <label class="block text-gray-700">Nama Resep</label>
                    <input type="text" name="nama_resep" class="w-full px-4 py-2 border rounded-lg">
                    <span class="error-message">Nama resep wajib diisi.</span>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Nama Dokter</label>
                    <select name="doctor_name" class="w-full px-4 py-2 border rounded-lg">
                        <option value="">-- Pilih Dokter --</option>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?php echo $doctor['full_name']; ?>">
                                <?php echo htmlspecialchars($doctor['full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="error-message">Nama dokter wajib dipilih.</span>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Petunjuk Penggunaan</label>
                    <textarea name="usage_instructions" class="w-full px-4 py-2 border rounded-lg"></textarea>
                    <span class="error-message">Petunjuk penggunaan wajib diisi.</span>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Deskripsi</label>
                    <textarea name="desc_recipe" class="w-full px-4 py-2 border rounded-lg"></textarea>
                    <span class="error-message">Deskripsi wajib diisi.</span>
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
                <div class="mb-4">
                    <label class="block text-gray-700">Gambar Resep</label>
                    <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border rounded-lg">
                    <span class="error-message">Gambar resep wajib diunggah.</span>
                </div>

                <!-- Bagian untuk Obat Herbal -->
            <?php elseif ($category === 'obat-herbal'): ?>
                <div class="mb-4">
                    <label class="block text-gray-700">Nama Produk</label>
                    <input type="text" name="name" class="w-full px-4 py-2 border rounded-lg">
                    <span class="error-message">Nama produk wajib diisi.</span>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Harga</label>
                    <input type="number" name="price" class="w-full px-4 py-2 border rounded-lg">
                    <span class="error-message">Harga wajib diisi.</span>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Deskripsi</label>
                    <textarea name="description" class="w-full px-4 py-2 border rounded-lg"></textarea>
                    <span class="error-message">Deskripsi wajib diisi.</span>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Stok</label>
                    <input type="number" name="stock" class="w-full px-4 py-2 border rounded-lg">
                    <span class="error-message">Stok wajib diisi.</span>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Gambar Produk</label>
                    <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border rounded-lg">
                    <span class="error-message">Gambar produk wajib diunggah.</span>
                </div>
            <?php endif; ?>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Kirim</button>
        </form>
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
</body>

</html>