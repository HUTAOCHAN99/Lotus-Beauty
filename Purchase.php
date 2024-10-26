<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Farmaku UI</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-4">

  <!-- Search bar -->
  <div class="flex items-center mb-4">
    <i class="ri-search-line text-xl text-gray-500 mr-2"></i>
    <input type="text" placeholder="Cari di Farmaku" class="w-full border rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
  </div>

  <!-- Categories -->
  <div class="mb-4">
    <h2 class="font-semibold text-lg mb-2">Kategori Sering Dicari</h2>
    <div class="flex gap-2 flex-wrap">
      <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-sm">Obat Resep</span>
      <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-sm">Disfungsi Ereksi</span>
      <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-sm">Pharmacy</span>
      <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-sm">Gangguan Pencernaan</span>
    </div>
  </div>

  <!-- Popular Searches -->
  <div class="mb-6">
    <h2 class="font-semibold text-lg mb-2">Pencarian Populer <i class="ri-fire-line text-red-500"></i></h2>
    <div class="grid grid-cols-2 gap-4">
      <div class="flex items-center">
        <img src="https://via.placeholder.com/50" alt="Product" class="w-10 h-10 mr-3">
        <span>Sodium Bicarbonate 500 mg Tablet PIM</span>
      </div>
      <div class="flex items-center">
        <img src="https://via.placeholder.com/50" alt="Product" class="w-10 h-10 mr-3">
        <span>Combatrin 250 mg Strip 2 Tablet</span>
      </div>
      <div class="flex items-center">
        <img src="https://via.placeholder.com/50" alt="Product" class="w-10 h-10 mr-3">
        <span>Xonce 500 mg Strip 2 Tablet</span>
      </div>
      <div class="flex items-center">
        <img src="https://via.placeholder.com/50" alt="Product" class="w-10 h-10 mr-3">
        <span>Paracetamol 500 mg Strip 10 Tablet MEF</span>
      </div>
      <div class="flex items-center">
        <img src="https://via.placeholder.com/50" alt="Product" class="w-10 h-10 mr-3">
        <span>Schick Razor Intuition Touch Up Set isi 3 Pcs</span>
      </div>
      <div class="flex items-center">
        <img src="https://via.placeholder.com/50" alt="Product" class="w-10 h-10 mr-3">
        <span>Citicoline 500 mg Tablet Hexp</span>
      </div>
    </div>
  </div>

  <!-- Trending Brands -->
  <div>
    <h2 class="font-semibold text-lg mb-2">Trending Brands</h2>
    <div class="flex gap-4">
      <img src="https://via.placeholder.com/50" alt="Brand 1" class="w-12 h-12">
      <img src="https://via.placeholder.com/50" alt="Brand 2" class="w-12 h-12">
      <img src="https://via.placeholder.com/50" alt="Brand 3" class="w-12 h-12">
      <img src="https://via.placeholder.com/50" alt="Brand 4" class="w-12 h-12">
    </div>
  </div>

  <!-- Suggest Product -->
  <div class="mt-6 text-center text-blue-500">
   Produk tidak ditemukan?  <a href="#" class="hover:underline"></a>Sarankan Produk</a>
  </div>

</body>
</html>
