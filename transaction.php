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
    <title>Riwayat Transaksi</title>
    <link rel="stylesheet" href="style.css"> <!-- Link ke file CSS jika diperlukan -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            margin: 20px 0;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            justify-items: center;
        }
        th {
            background-color: #7AB2D3;
            color: white;
        }
        .btn-detail {
            background-color: #7AB2D3; 
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
            border: none;
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
            background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border-radius: 5px;
            width: 80%; /* Could be more or less, depending on screen size */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
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
            cursor: pointer;
        }

        /* Responsiveness */
        @media (max-width: 768px) {
            table {
                width: 100%;
            }
            .modal-content {
                width: 90%; /* Lebar modal lebih kecil pada perangkat mobile */
            }
            th, td {
                padding: 8px; /* Mengurangi padding di tabel untuk tampilan mobile */
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.5rem; /* Ukuran font lebih kecil pada perangkat mobile */
            }
            .btn-detail {
                padding: 8px 5px; /* Mengurangi padding pada tombol untuk perangkat kecil */
                font-size: 0.9rem; /* Ukuran font tombol lebih kecil */
            }
        }
    </style>
</head>
<body>
<?php include('Header.php'); ?>
<h2 class="font-semibold text-center text-lg pt-4">Riwayat Transaksi</h2>

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
                <td colspan="8" style="text-align:center;">Tidak ada riwayat transaksi ditemukan.</td>
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
