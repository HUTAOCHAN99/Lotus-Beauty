<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Resep</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<?php include('Header.php'); ?>
    <section class="max-w-4xl mx-auto p-4">
        <h2 class="text-2xl font-bold mb-6">Obat Resep</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            <!-- Item 1 -->
            <div class="bg-white p-4 rounded-lg shadow-md flex flex-col items-center">
                <img src="src/images/obat-1.png" alt="Obat Intraven & Larutan Steril" class="w-16 h-16 mb-3">
                <p class="text-center text-sm font-semibold">Obat Intraven & Larutan Steril</p>
            </div>
            <!-- Item 2 -->
            <div class="bg-white p-4 rounded-lg shadow-md flex flex-col items-center">
                <img src="src/images/obat-2.png" alt="Diabetes" class="w-16 h-16 mb-3">
                <p class="text-center text-sm font-semibold">Diabetes</p>
            </div>
            <!-- Item 3 -->
            <div class="bg-white p-4 rounded-lg shadow-md flex flex-col items-center">
                <img src="src/images/obat-3.png" alt="Darah Tinggi" class="w-16 h-16 mb-3">
                <p class="text-center text-sm font-semibold">Darah Tinggi</p>
            </div>
            <!-- Item 4 -->
            <div class="bg-white p-4 rounded-lg shadow-md flex flex-col items-center">
                <img src="src/images/obat-4.png" alt="Kolesterol" class="w-16 h-16 mb-3">
                <p class="text-center text-sm font-semibold">Kolesterol</p>
            </div>
            <!-- Item 5 -->
            <div class="bg-white p-4 rounded-lg shadow-md flex flex-col items-center">
                <img src="src/images/obat-5.png" alt="Jantung" class="w-16 h-16 mb-3">
                <p class="text-center text-sm font-semibold">Jantung</p>
            </div>
            <!-- Item 6 -->
            <div class="bg-white p-4 rounded-lg shadow-md flex flex-col items-center">
                <img src="src/images/obat-6.png" alt="Asam Urat" class="w-16 h-16 mb-3">
                <p class="text-center text-sm font-semibold">Asam Urat</p>
            </div>
            <!-- Item 7 -->
            <div class="bg-white p-4 rounded-lg shadow-md flex flex-col items-center">
                <img src="src/images/obat-7.png" alt="Disfungsi Ereksi" class="w-16 h-16 mb-3">
                <p class="text-center text-sm font-semibold">Disfungsi Ereksi</p>
            </div>
            <!-- Item 8 -->
            <div class="bg-white p-4 rounded-lg shadow-md flex flex-col items-center">
                <img src="src/images/obat-8.png" alt="Flu, Pusing dan Demam" class="w-16 h-16 mb-3">
                <p class="text-center text-sm font-semibold">Flu, Pusing dan Demam</p>
            </div>
            <!-- Item 9 -->
            <div class="bg-white p-4 rounded-lg shadow-md flex flex-col items-center">
                <img src="src/images/obat-9.png" alt="Anti Infeksi" class="w-16 h-16 mb-3">
                <p class="text-center text-sm font-semibold">Anti Infeksi</p>
            </div>
        </div>
    </section>
    <?php include('Footer.php'); ?>
</body>
</html>
