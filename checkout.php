<?php
session_start();
include('db.php'); // Pastikan koneksi database terhubung

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['order_id']) && isset($data['user_id']) && isset($data['items']) && isset($data['payment_method'])) {
    // Lanjutkan proses transaksi seperti yang sudah dilakukan
    $transaksi_id = time();
    $user_id = $data['user_id'];
    $items = $data['items'];
    $payment_method = $data['payment_method'];
    $total_harga = 0;

    foreach ($items as $item) {
        $total_harga += $item['price'] * $item['quantity'];
    }

    // Mulai transaksi ke database seperti kode yang ada
    $konek->begin_transaction();

    try {
        $transaksiQuery = $konek->prepare("INSERT INTO riwayat_transaksi (transaksi_id, user_id, tanggal, total_harga, status, metode_pembayaran, alamat_pengiriman, catatan) VALUES (?, ?, NOW(), ?, 'completed', ?, '', '')");
        $transaksiQuery->bind_param("iids", $transaksi_id, $user_id, $total_harga, $payment_method);
        $transaksiQuery->execute();

        foreach ($items as $item) {
            $product_id = $item['id'];
            $quantity = $item['quantity'];
            $harga_satuan = $item['price'];

            $updateStockQuery = $konek->prepare("UPDATE product SET stock = stock - ?, terjual = terjual + ? WHERE product_id = ?");
            $updateStockQuery->bind_param("iii", $quantity, $quantity, $product_id);
            $updateStockQuery->execute();

            $detailQuery = $konek->prepare("INSERT INTO detail_transaksi (transaksi_id, product_id, jumlah, harga_satuan) VALUES (?, ?, ?, ?)");
            $detailQuery->bind_param("iidi", $transaksi_id, $product_id, $quantity, $harga_satuan);
            $detailQuery->execute();
        }

        foreach ($items as $item) {
            $cart_id = $item['cart_id']; // Pastikan setiap item memiliki cart_id

            $deleteCartQuery = $konek->prepare("DELETE FROM cart WHERE cart_id = ?");
            $deleteCartQuery->bind_param("i", $cart_id);
            $deleteCartQuery->execute();
        }
        $konek->commit();
        echo json_encode(['success' => true, 'message' => 'Transaksi berhasil diproses!']);
    } catch (Exception $e) {
        $konek->rollback();
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
}
?>