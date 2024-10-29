<?php
session_start();
include('db.php'); // Pastikan koneksi database terhubung

// Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    echo "Anda harus login untuk melihat riwayat transaksi.";
    exit; // Hentikan eksekusi jika pengguna belum login
}

$username = $_SESSION['username']; // Ambil username dari session

// Ambil user_id berdasarkan username
$sql_user = "SELECT user_id FROM users WHERE username = ?";
$stmt_user = $konek->prepare($sql_user);
$stmt_user->bind_param("s", $username);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_data = $result_user->fetch_assoc();
$user_id = $user_data['user_id'];

// Ambil data riwayat transaksi dari database
$sql_transaksi = "SELECT * FROM riwayat_transaksi WHERE user_id = ? ORDER BY tanggal DESC";
$stmt_transaksi = $konek->prepare($sql_transaksi);
$stmt_transaksi->bind_param("i", $user_id);
$stmt_transaksi->execute();
$result_transaksi = $stmt_transaksi->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="style.css"> <!-- Link ke file CSS jika diperlukan -->
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn-detail {
            background-color: #4CAF50; /* Hijau */
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
        }
        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
<?php include('Header.php'); ?>
<h1>Riwayat Transaksi</h1>

<table>
    <thead>
        <tr>
            <th>ID Transaksi</th>
            <th>Tanggal</th>
            <th>Total Harga</th>
            <th>Status</th>
            <th>Metode Pembayaran</th>
            <th>Alamat Pengiriman</th>
            <th>Catatan</th>
            <th>Detail</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result_transaksi->num_rows > 0): ?>
            <?php while ($row = $result_transaksi->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['transaksi_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['tanggal']); ?></td>
                    <td><?php echo htmlspecialchars($row['total_harga']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo htmlspecialchars($row['metode_pembayaran']); ?></td>
                    <td><?php echo htmlspecialchars($row['alamat_pengiriman']); ?></td>
                    <td><?php echo htmlspecialchars($row['catatan']); ?></td>
                    <td>
                        <button class="btn-detail" onclick="showModal(<?php echo $row['transaksi_id']; ?>)">Lihat Detail</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">Tidak ada riwayat transaksi ditemukan.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Modal untuk menampilkan detail transaksi -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Detail Transaksi</h2>
        <div id="modal-body">
            <!-- Detail transaksi akan dimuat di sini -->
        </div>
    </div>
</div>

<script>
function showModal(transaksi_id) {
    // Ambil detail transaksi dari server menggunakan AJAX
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'detail_transaksi.php?transaksi_id=' + transaksi_id, true);
    xhr.onload = function() {
        if (this.status === 200) {
            document.getElementById('modal-body').innerHTML = this.responseText;
            document.getElementById('myModal').style.display = "block";
        } else {
            alert('Error loading details.');
        }
    };
    xhr.send();
}

function closeModal() {
    document.getElementById('myModal').style.display = "none";
}

// Close the modal when clicking outside of the modal content
window.onclick = function(event) {
    var modal = document.getElementById('myModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

<?php
$stmt_user->close();
$stmt_transaksi->close();
$konek->close();
?>
<br>
<?php include('Footer.php'); ?>
</body>
</html>
