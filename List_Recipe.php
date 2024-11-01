<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Resep</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<?php
include('Header.php');
include('db.php');

// Ambil prescription_id dari URL
$prescription_id = isset($_GET['prescription_id']) ? intval($_GET['prescription_id']) : 0;

if ($prescription_id === 0) {
    echo "<p>Resep tidak ditemukan.</p>";
    exit();
}

// Ambil detail resep
$stmt = $konek->prepare("SELECT nama_resep, doctor_name, patient_name, usage_instructions, image_url FROM prescription WHERE prescription_id = ?");
$stmt->bind_param("i", $prescription_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "<p>Resep tidak ditemukan.</p>";
    exit();
}

$resep = $res->fetch_assoc();
?>

<section class="max-w-4xl mx-auto p-4">
    <h2 class="text-2xl font-bold mb-6"><?php echo htmlspecialchars($resep['nama_resep']); ?></h2>
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <img src="<?php echo htmlspecialchars($resep['image_url']); ?>" alt="<?php echo htmlspecialchars($resep['nama_resep']); ?>" class="w-32 h-32 mx-auto mb-4">
        <p><strong>Dokter:</strong> <?php echo htmlspecialchars($resep['doctor_name']); ?></p>
        <p><strong>Pasien:</strong> <?php echo htmlspecialchars($resep['patient_name']); ?></p>
        <p><strong>Petunjuk Pemakaian:</strong> <?php echo htmlspecialchars($resep['usage_instructions']); ?></p>
    </div>

    <h3 class="text-xl font-semibold mb-4">Produk Terkait</h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php
        // Ambil produk terkait berdasarkan prescription_id
        $query = "
            SELECT p.product_id, p.name, p.image, p.price
            FROM product AS p
            JOIN product_prescription AS pp ON p.product_id = pp.product_id
            WHERE pp.prescription_id = ?";
        $stmt = $konek->prepare($query);
        $stmt->bind_param("i", $prescription_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo "<p>Tidak ada produk terkait untuk resep ini.</p>";
        } else {
            while ($product = $result->fetch_assoc()):
                ?>
                <div class="bg-white p-4 rounded-lg shadow-md flex flex-col items-center">
                    <a href="Detail_Product.php?product_id=<?php echo $product['product_id']; ?>" class="flex flex-col items-center">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-16 h-16 mb-3">
                        <p class="text-center text-sm font-semibold text-black-500"><?php echo htmlspecialchars($product['name']); ?></p>
                        <p class="text-center text-sm text-gray-700">Harga: Rp<?php echo number_format($product['price'], 0, ',', '.'); ?></p>
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
