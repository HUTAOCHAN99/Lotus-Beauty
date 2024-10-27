<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade & Shopper Marketing</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@remix-run/icons@2.2.0/dist/esm/index.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-4">

<div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
    <!-- Header Gambar dan Judul -->
    <div class="flex gap-6">
        <!-- Gambar Produk -->
        <div class="w-1/3">
            <img src="https://via.placeholder.com/150" alt="Trade & Shopper Marketing" class="rounded-md">
            <div class="mt-2 flex items-center justify-center">
                <img src="https://via.placeholder.com/50" alt="Thumbnail" class="border border-gray-300 rounded-md">
            </div>
        </div>

        <!-- Detail Produk -->
        <div class="w-2/3">
            <h2 class="text-2xl font-semibold">Trade & Shopper Marketing - Sebuah Perspektif</h2>
            <div class="flex items-center mt-2">
                <span class="text-yellow-500 text-lg flex items-center">
                    <i class="remixicon-star-fill"></i> 5.0
                </span>
                <span class="ml-2 text-gray-600">(1 Penilaian)</span>
                <span class="ml-4 text-gray-600">| 2 Terjual</span>
            </div>
            <p class="mt-4 text-3xl font-bold text-red-600">Rp71.250</p>
            <p class="text-gray-500 line-through">Rp75.000</p>
            <p class="text-red-600">-5%</p>

            <!-- Pengiriman dan Paket Diskon -->
            <div class="mt-4">
                <div class="text-gray-600">
                    <p><i class="remixicon-truck-line"></i> Gratis Ongkir</p>
                    <p><i class="remixicon-location-line"></i> Pengiriman ke <span class="text-gray-800 font-semibold">Kab. Sleman</span></p>
                    <p>Ongkos Kirim Rp0</p>
                </div>
                <div class="mt-2 text-sm text-red-600 bg-red-100 py-1 px-2 rounded">
                    <i class="remixicon-discount-line"></i> Pilih 2, diskon 3%
                </div>
            </div>

            <!-- Quantity dan Tombol -->
            <div class="flex items-center mt-6">
                <label class="mr-4">Kuantitas</label>
                <div class="flex items-center border border-gray-300 rounded-md overflow-hidden">
                    <button class="px-2 bg-gray-200">-</button>
                    <input type="number" class="w-12 text-center outline-none" value="1">
                    <button class="px-2 bg-gray-200">+</button>
                </div>
                <span class="ml-4 text-gray-500">tersisa 8 buah</span>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex gap-4 mt-4">
                <button class="flex-1 bg-orange-500 text-white py-2 px-4 rounded-md hover:bg-orange-600"><i class="remixicon-shopping-cart-line mr-2"></i> Masukkan Keranjang</button>
                <button class="flex-1 bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700"><i class="remixicon-flash-line mr-2"></i> Beli Sekarang</button>
            </div>
        </div>
    </div>

    <!-- Bagian Share dan Wishlist -->
    <div class="flex items-center justify-between mt-6">
        <div class="flex items-center gap-2">
            <p>Share:</p>
            <i class="remixicon-facebook-circle-line text-blue-500"></i>
            <i class="remixicon-messenger-line text-blue-500"></i>
            <i class="remixicon-pinterest-line text-red-600"></i>
            <i class="remixicon-twitter-line text-blue-400"></i>
        </div>
        <div class="flex items-center">
            <i class="remixicon-heart-line text-gray-500 mr-1"></i>
            <span>0</span>
        </div>
    </div>
</div>

</body>
</html>
