<?php
session_start();
include('db.php'); // Pastikan koneksi database terhubung

if (!isset($_GET['transaksi_id'])) {
    echo "ID Transaksi tidak ditemukan.";
    exit;
}

$transaksi_id = $_GET['transaksi_id'];

// Ambil data detail transaksi dari database
$sql = "SELECT dt.*, p.name AS product_name FROM detail_transaksi dt 
        JOIN product p ON dt.product_id = p.product_id 
        WHERE dt.transaksi_id = ?";
$stmt = $konek->prepare($sql);
$stmt->bind_param("i", $transaksi_id);
$stmt->execute();
$result = $stmt->get_result();

$output = '<table>
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Total Harga</th>
                </tr>
            </thead>
            <tbody>';
            
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $output .= '<tr>
                        <td>' . htmlspecialchars($row['product_name']) . '</td>
                        <td>' . htmlspecialchars($row['jumlah']) . '</td>
                        <td>' . htmlspecialchars($row['harga_satuan']) . '</td>
                        <td>' . htmlspecialchars($row['jumlah'] * $row['harga_satuan']) . '</td>
                    </tr>';
    }
} else {
    $output .= '<tr>
                    <td colspan="4">Tidak ada detail transaksi ditemukan.</td>
                </tr>';
}

$output .= '</tbody></table>';
echo $output;

$stmt->close();
$konek->close();
?>
