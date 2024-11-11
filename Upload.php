<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: Landing_Page.php");
    exit();
}

// Get category from URL parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Retrieve all products from the database
$products = [];
$result = $konek->query("SELECT product_id, name FROM product");
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Categories for herbal medicine
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

    // Initialize a variable to track success
    $uploadSuccess = false;

    if ($category === 'obat-herbal') {
        // Herbal medicine data
        $name = htmlspecialchars($_POST['name']);
        $price = htmlspecialchars($_POST['price']);
        $description = htmlspecialchars($_POST['description']);
        $stock = htmlspecialchars($_POST['stock']);

        // Handle image upload
        $image = $_FILES['image'];
        $imagePath = 'img/product/' . basename($image['name']);

        if (move_uploaded_file($image['tmp_name'], $imagePath)) {
            $stmt = $konek->prepare("INSERT INTO product (name, category, price, description, stock, image, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssississ", $name, $selected_category, $price, $description, $stock, $imagePath, $created_at, $updated_at);
            $stmt->execute();
            $uploadSuccess = true; // Mark as successful
        } else {
            echo "<p class='text-red-500'>Error uploading image.</p>";
            exit();
        }
    } elseif ($category === 'resep') {
        // Prescription data
        $nama_resep = htmlspecialchars($_POST['nama_resep']);
        $doctor_name = htmlspecialchars($_POST['doctor_name']);
        $patient_name = htmlspecialchars($_POST['patient_name']);
        $usage_instructions = htmlspecialchars($_POST['usage_instructions']);
        $product_id = htmlspecialchars($_POST['product_id']);
        $desc_recipe = htmlspecialchars($_POST['desc_recipe']);
        $image = $_FILES['image'];
        $imagePath = 'img/resep/' . basename($image['name']);

        $stmt = $konek->prepare("INSERT INTO prescription (nama_resep, doctor_name, patient_name, usage_instructions, product_id, created_at, updated_at, desc_recipe, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiisss", $nama_resep, $doctor_name, $patient_name, $usage_instructions, $product_id, $created_at, $updated_at, $desc_recipe, $imagePath);

        if (move_uploaded_file($image['tmp_name'], $imagePath)) {
            $stmt = $konek->prepare("INSERT INTO prescription (nama_resep, doctor_name, patient_name, usage_instructions, product_id, created_at, updated_at,desc_recipe,image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?,?)");
            $stmt->bind_param("ssssiisss", $nama_resep, $doctor_name, $patient_name, $usage_instructions, $product_id, $created_at, $updated_at, $desc_recipe, $imagePath);

            $stmt->execute();
            $uploadSuccess = true; // Mark as successful
        } else {
            echo "<p class='text-red-500'>Error uploading image.</p>";
            exit();
        }
    }

    // Check if upload was successful and output SweetAlert script
    if ($uploadSuccess) {
        // Instead of echoing out JavaScript directly, store a message for later
        $successMessage = true; // We'll use this to check later in the HTML
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

            // Hide all previous error messages
            form.querySelectorAll(".error-message").forEach(element => {
                element.style.display = "none";
            });

            // Loop through each required input
            form.querySelectorAll("[name]").forEach(input => {
                const errorMessage = input.nextElementSibling;
                if (!input.value.trim()) {
                    isValid = false;
                    errorMessage.style.display = "block";
                }
            });

            if (!isValid) {
                event.preventDefault(); // Prevent form submission if there are errors
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Check if the upload was successful
            <?php if (isset($successMessage) && $successMessage): ?>
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Produk berhasil diunggah',
                    showConfirmButton: false,
                    timer: 1500
                }).then(function () {
                    window.location.href = 'dashboard.php?success=1'; // Redirect after alert
                });
            <?php endif; ?>
        });
    </script>
</head>

<body class="bg-gray-100">
    <div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md mt-10">
        <h2 class="text-2xl font-bold mb-6 text-center">
            <?php echo $category === 'resep' ? 'Upload Resep' : 'Upload Obat Herbal'; ?>
        </h2>

        <form method="POST" action="upload.php?category=<?php echo htmlspecialchars($category); ?>"
            enctype="multipart/form-data" onsubmit="validateForm(event)">
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

            <?php if ($category === 'resep'): ?>
                <div class="mb-4">
                    <label class="block text-gray-700">Nama Resep</label>
                    <input type="text" name="nama_resep" class="w-full px-4 py-2 border rounded-lg">
                    <span class="error-message">Nama resep wajib diisi.</span>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Nama Dokter</label>
                    <input type="text" name="doctor_name" class="w-full px-4 py-2 border rounded-lg">
                    <span class="error-message">Nama dokter wajib diisi.</span>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Nama Pasien</label>
                    <input type="text" name="patient_name" class="w-full px-4 py-2 border rounded-lg">
                    <span class="error-message">Nama pasien wajib diisi.</span>
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
                <div class="mb-4">
                    <label class="block text-gray-700">Pilih Produk Herbal</label>
                    <select name="product_id" class="w-full px-4 py-2 border rounded-lg">
                        <option value="">-- Pilih Produk --</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo $product['product_id']; ?>"><?php echo $product['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="error-message">Produk herbal wajib dipilih.</span>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Gambar Resep</label>
                    <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border rounded-lg">
                    <span class="error-message">Gambar resep wajib diunggah.</span>
                </div>

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
</body>

</html>