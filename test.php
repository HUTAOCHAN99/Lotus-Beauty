<?php
// Get the nama_resep based on the prescription_id from the URL
$query = "SELECT nama_resep FROM prescription WHERE prescription_id = $prescription_id LIMIT 1";
$result = $konek->query($query);

if ($result && $result->num_rows > 0) {
    $resep = $result->fetch_assoc();
    $nama_resep = $resep['nama_resep'];

    // Fetch all prescriptions with the same nama_resep
    $prescription_query = "SELECT doctor_name, usage_instructions, desc_recipe, image_url, product_id 
                           FROM prescription 
                           WHERE nama_resep = '$nama_resep'";
    $prescription_result = $konek->query($prescription_query);

    if ($prescription_result && $prescription_result->num_rows > 0) {
        while ($prescription = $prescription_result->fetch_assoc()) {
            // Display prescription details as needed
        }
    }

    // Fetch all related products for the prescriptions with the same nama_resep
    $product_query = "SELECT p.product_id, p.name, p.category, p.price, p.image, 
                             COALESCE(AVG(r.rating), 0) AS average_rating, 
                             COALESCE(SUM(dt.jumlah), 0) AS total_sold
                      FROM product AS p
                      JOIN prescription AS pr ON p.product_id = pr.product_id
                      LEFT JOIN reviews AS r ON p.product_id = r.product_id
                      LEFT JOIN detail_transaksi AS dt ON p.product_id = dt.product_id
                      WHERE pr.nama_resep = '$nama_resep'
                      GROUP BY p.product_id";
    $product_result = $konek->query($product_query);

    // Display each product under the same recipe
    if ($product_result && $product_result->num_rows > 0) {
        while ($product = $product_result->fetch_assoc()) {
            // Display product details as needed
        }
    } else {
        echo "<p>Produk terkait tidak ditemukan.</p>";
    }
} else {
    echo "<p>Resep tidak ditemukan.</p>";
}
?>