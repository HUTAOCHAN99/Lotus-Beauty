<?php
session_start();
include('db.php'); // Pastikan koneksi database terhubung

// Ambil input JSON yang dikirim dari JavaScript
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['order_id']) && isset($data['user_id']) && isset($data['items']) && isset($data['payment_method'])) {
    $transaksi_id = time(); // Generate ID untuk transaksi
    $user_id = $data['user_id'];
    $items = $data['items'];
    $payment_method = $data['payment_method']; // Ambil metode pembayaran dari data
    $total_harga = 0; // Inisialisasi total harga

    // Hitung total harga berdasarkan item
    foreach ($items as $item) {
        $total_harga += $item['price'] * $item['quantity'];
    }

    // Mulai transaksi database
    $konek->begin_transaction();

    try {
        // 1. Tambahkan data transaksi ke tabel riwayat_transaksi
        $transaksiQuery = $konek->prepare("INSERT INTO riwayat_transaksi (transaksi_id, user_id, tanggal, total_harga, status, metode_pembayaran, alamat_pengiriman, catatan) VALUES (?, ?, NOW(), ?, 'completed', ?, '', '')");
        $transaksiQuery->bind_param("iids", $transaksi_id, $user_id, $total_harga, $payment_method);
        $transaksiQuery->execute();

        // 2. Loop melalui setiap item untuk mengurangi stok dan menambahkan detail transaksi
        foreach ($items as $item) {
            $product_id = $item['id'];
            $quantity = $item['quantity'];

            // 2.1 Kurangi stok dari tabel produk
            $updateStockQuery = $konek->prepare("UPDATE product SET stock = stock - ? WHERE product_id = ?");
            $updateStockQuery->bind_param("ii", $quantity, $product_id);
            $updateStockQuery->execute();

            // 2.2 Tambahkan detail transaksi ke tabel detail_transaksi
            $detailQuery = $konek->prepare("INSERT INTO detail_transaksi (transaksi_id, product_id, jumlah, harga_satuan) VALUES (?, ?, ?, ?)");
            $harga_satuan = $item['price'];
            $detailQuery->bind_param("iidi", $transaksi_id, $product_id, $quantity, $harga_satuan);
            $detailQuery->execute();
        }

        // 3. Hapus item dari tabel cart
        $deleteCartQuery = $konek->prepare("DELETE FROM cart WHERE user_id = ?");
        $deleteCartQuery->bind_param("i", $user_id);
        $deleteCartQuery->execute();

        // Commit transaksi
        $konek->commit();
        echo json_encode(['success' => true, 'message' => 'Transaksi berhasil diproses!']);
    } catch (Exception $e) {
        // Rollback transaksi jika ada yang gagal
        $konek->rollback();
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
}
