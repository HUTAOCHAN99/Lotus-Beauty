<?php
include('db.php'); 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prescription_id = intval($_POST['prescription_id']);
    $product_id = intval($_POST['product_id']);

    // tambahkan ke table resep
    $insert_query = "INSERT INTO product_prescription (product_id, prescription_id) VALUES ($product_id, $prescription_id)";
    if ($konek->query($insert_query)) {
        echo "Produk berhasil ditambahkan!";
    } else {
        echo "Gagal menambahkan produk.";
    }
}
?>
