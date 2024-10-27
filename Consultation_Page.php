<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Consultation Cards</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
<div class="w-full max-w-lg space-y-4 px-4">

  <!-- Chat Dokter Card -->
  <a href="Consultation.php?type=dokter" class="flex items-center p-4 bg-blue-50 rounded-lg shadow-md hover:bg-blue-100 transition duration-200">
    <div class="w-16 h-16 rounded-full flex items-center justify-center bg-blue-100">
      <img src="doctor-icon.png" alt="Doctor Icon" class="w-12 h-12">
    </div>
    <div class="ml-4">
      <h3 class="text-lg font-semibold text-gray-700">Chat Dokter</h3>
      <p class="text-sm text-gray-500">Dokter rekanan kami akan menjawab keluhan Anda</p>
    </div>
  </a>

  <!-- Chat Apoteker Card -->
  <a href="Consultation.php?type=apoteker" class="flex items-center p-4 bg-orange-50 rounded-lg shadow-md hover:bg-orange-100 transition duration-200">
    <div class="w-16 h-16 rounded-full flex items-center justify-center bg-orange-100">
      <img src="pharmacist-icon.png" alt="Pharmacist Icon" class="w-12 h-12">
    </div>
    <div class="ml-4">
      <h3 class="text-lg font-semibold text-gray-700">Chat Apoteker</h3>
      <p class="text-sm text-gray-500">Apoteker akan membantumu mendapatkan obat</p>
    </div>
  </a>

  <!-- Chat CS Farmaku Card -->
  <a href="Consultation.php?type=cs" class="flex items-center p-4 bg-blue-50 rounded-lg shadow-md hover:bg-blue-100 transition duration-200">
    <div class="w-16 h-16 rounded-full flex items-center justify-center bg-blue-100">
      <img src="cs-icon.png" alt="Customer Service Icon" class="w-12 h-12">
    </div>
    <div class="ml-4">
      <h3 class="text-lg font-semibold text-gray-700">Chat CS Farmaku</h3>
      <p class="text-sm text-gray-500">Butuh bantuan? Kami akan membantumu dalam 24 jam</p>
    </div>
  </a>

</div>
</body>
</html>
