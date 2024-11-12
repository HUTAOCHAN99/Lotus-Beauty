<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.0/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100 flex flex-col items-center min-h-screen">
    <nav class="bg-powderBlue w-full shadow-md p-4 flex justify-between items-center mb-4">
        <a href="dashboard.php" class="text-black flex items-center space-x-2">
            <i class="ri-arrow-left-line text-xl"></i>
        </a>
        <h1 class="text-gray-800 font-bold text-lg">Laporan Statistik Penjualan</h1>
        <div class="w-10"></div>
    </nav>

    <main class="container mx-auto max-w-4xl rounded-lg shadow-lg p-2 mt-8">
        <?php
        include 'db.php';
        $dataPenjualanMingguan = [];
        $dataPenjualanHarian = [];
        $riwayatTransaksi = [];

        // Query untuk mendapatkan total penjualan per minggu
        $queryMingguan = "
            SELECT YEARWEEK(tanggal, 1) AS tahun_minggu, SUM(total_harga) AS total_penjualan
            FROM riwayat_transaksi
            WHERE status = 'completed'
            GROUP BY tahun_minggu
            ORDER BY tahun_minggu;
        ";
        $resultMingguan = $konek->query($queryMingguan);

        while ($row = $resultMingguan->fetch_assoc()) {
            $dataPenjualanMingguan[] = [
                'minggu' => 'Minggu ' . substr($row['tahun_minggu'], 4) . ' (Tahun ' . substr($row['tahun_minggu'], 0, 4) . ')',
                'total_penjualan' => $row['total_penjualan']
            ];
        }

        // Query untuk mendapatkan total penjualan per hari
        $queryHarian = "
            SELECT DATE(tanggal) AS tanggal_hari, SUM(total_harga) AS total_penjualan
            FROM riwayat_transaksi
            WHERE status = 'completed'
            GROUP BY tanggal_hari
            ORDER BY tanggal_hari;
        ";
        $resultHarian = $konek->query($queryHarian);

        while ($row = $resultHarian->fetch_assoc()) {
            $dataPenjualanHarian[] = [
                'tanggal' => $row['tanggal_hari'],
                'total_penjualan' => $row['total_penjualan']
            ];
        }

        // Query untuk mendapatkan riwayat transaksi termasuk user_id
        $queryRiwayat = "
            SELECT transaksi_id, user_id, tanggal, total_harga, status, metode_pembayaran, alamat_pengiriman, catatan
            FROM riwayat_transaksi
            ORDER BY tanggal DESC;
        ";
        $resultRiwayat = $konek->query($queryRiwayat);

        while ($row = $resultRiwayat->fetch_assoc()) {
            $riwayatTransaksi[] = $row;
        }
        ?>

        <!-- Grafik Penjualan Mingguan -->
        <section class="mb-4 border p-4">
            <h2 class="text-xl font-semibold text-blue-600 mb-4">Penjualan per Minggu</h2>
            <canvas id="chartPenjualanMingguan" class="w-full h-64"></canvas>
        </section>

        <div class="block py-4"></div>

        <!-- Grafik Penjualan Harian -->
        <section class="my-4 border p-4">
            <h2 class="text-xl font-semibold text-blue-600 mb-4">Penjualan per Hari</h2>
            <canvas id="chartPenjualanHarian" class="w-full h-64"></canvas>
        </section>

        <!-- Tabel Riwayat Penjualan -->
        <section class="my-4 border p-4 overflow-x-auto">
            <h2 class="text-xl font-semibold text-blue-600 mb-4">Riwayat Penjualan</h2>
            <table class="min-w-full bg-white rounded-lg shadow overflow-hidden">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-2 px-4 text-left">ID Transaksi</th>
                        <th class="py-2 px-4 text-left">User ID</th>
                        <th class="py-2 px-4 text-left">Tanggal</th>
                        <th class="py-2 px-4 text-left">Total Harga</th>
                        <th class="py-2 px-4 text-left">Status</th>
                        <th class="py-2 px-4 text-left">Metode Pembayaran</th>
                        <th class="py-2 px-4 text-left">Alamat Pengiriman</th>
                        <th class="py-2 px-4 text-left">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($riwayatTransaksi as $transaksi) : ?>
                        <tr class="border-t">
                            <td class="py-2 px-4"><?php echo $transaksi['transaksi_id']; ?></td>
                            <td class="py-2 px-4"><?php echo $transaksi['user_id']; ?></td>
                            <td class="py-2 px-4"><?php echo $transaksi['tanggal']; ?></td>
                            <td class="py-2 px-4"><?php echo number_format($transaksi['total_harga'], 2); ?></td>
                            <td class="py-2 px-4"><?php echo ucfirst($transaksi['status']); ?></td>
                            <td class="py-2 px-4"><?php echo $transaksi['metode_pembayaran']; ?></td>
                            <td class="py-2 px-4"><?php echo $transaksi['alamat_pengiriman']; ?></td>
                            <td class="py-2 px-4"><?php echo $transaksi['catatan']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer class="text-gray-500 text-sm my-8">
        &copy; <?php echo date("Y"); ?> LotusBeauty
    </footer>

    <script>
        // Grafik Penjualan Mingguan
        const ctxMingguan = document.getElementById('chartPenjualanMingguan').getContext('2d');
        const chartPenjualanMingguan = new Chart(ctxMingguan, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($dataPenjualanMingguan, 'minggu')); ?>,
                datasets: [{
                    label: 'Penjualan per Minggu',
                    data: <?php echo json_encode(array_column($dataPenjualanMingguan, 'total_penjualan')); ?>,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });

        // Grafik Penjualan Harian
        const ctxHarian = document.getElementById('chartPenjualanHarian').getContext('2d');
        const chartPenjualanHarian = new Chart(ctxHarian, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($dataPenjualanHarian, 'tanggal')); ?>,
                datasets: [{
                    label: 'Penjualan per Hari',
                    data: <?php echo json_encode(array_column($dataPenjualanHarian, 'total_penjualan')); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    </script>
</body>

</html>
