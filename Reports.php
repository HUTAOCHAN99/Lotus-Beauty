<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.0/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 flex flex-col items-center min-h-screen p-4">

    <header class="text-center mt-8">
        <h1 class="text-3xl font-bold text-blue-600">Laporan Penjualan Mingguan & Harian</h1>
        <p class="text-gray-600 mt-2">Analisis penjualan mingguan dan harian untuk memantau performa toko.</p>
    </header>

    <main class="container mx-auto max-w-4xl bg-white rounded-lg shadow-lg p-6 mt-8">
        <?php
        include 'db.php';
        $dataPenjualanMingguan = [];
        $dataPenjualanHarian = [];

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
        ?>

        <!-- Grafik Penjualan Mingguan -->
        <section class="mb-10">
            <h2 class="text-xl font-semibold text-blue-600 mb-4">Penjualan per Minggu</h2>
            <canvas id="chartPenjualanMingguan" class="w-full h-64"></canvas>
        </section>
<div class="block py-4"></div>
        <!-- Grafik Penjualan Harian -->
        <section>
            <h2 class="text-xl font-semibold text-blue-600 mb-4">Penjualan per Hari</h2>
            <canvas id="chartPenjualanHarian" class="w-full h-64"></canvas>
        </section>
    </main>

    <footer class="text-gray-500 text-sm mt-8">
        &copy; <?php echo date("Y"); ?> LotusBeauty - Semua Hak Dilindungi
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
