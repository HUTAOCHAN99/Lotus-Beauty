<?php
$type = isset($_GET['type']) ? $_GET['type'] : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Konsultasi</title>
<link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="w-full max-w-md bg-white shadow-lg rounded-lg overflow-hidden">
  
  <!-- Header -->
  <div class="bg-blue-600 p-4 flex items-center">
    <button onclick="window.history.back()" class="text-white mr-4">
      <i class="ri-arrow-left-line text-xl"></i>
    </button>
    <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center mr-3">
      <i class="<?php echo $type == 'dokter' ? 'ri-stethoscope-line' : ($type == 'apoteker' ? 'ri-medicine-bottle-line' : 'ri-customer-service-2-line'); ?> text-blue-600 text-2xl"></i>
    </div>
    <h2 class="text-white font-semibold">
      <?php 
        if ($type == 'dokter') {
            echo "Farmaku Dokter";
        } elseif ($type == 'apoteker') {
            echo "Farmaku Apoteker";
        } elseif ($type == 'cs') {
            echo "Farmaku CS";
        } else {
            echo "Farmaku Konsultasi";
        }
      ?>
    </h2>
  </div>

  <!-- Bagian Pesan Chat -->
  <div class="p-4 h-80 overflow-y-auto bg-gray-50">
    <?php
      if ($type == 'dokter') {
          echo "<p>Dokter rekanan kami akan menjawab keluhan Anda.</p>";
      } elseif ($type == 'apoteker') {
          echo "<p>Apoteker akan membantumu mendapatkan obat.</p>";
      } elseif ($type == 'cs') {
          echo "<p>Butuh bantuan? Kami akan membantumu dalam 24 jam.</p>";
      } else {
          echo "<p>Silakan pilih jenis konsultasi.</p>";
      }
    ?>
  </div>

  <!-- Area Input Pesan -->
  <div class="flex items-center p-2 border-t">
    <input type="text" placeholder="Ketik di sini dan tekan enter.." class="flex-1 px-4 py-2 border rounded-full text-sm focus:outline-none" />
    <button class="ml-2 text-blue-500">
      <i class="ri-attachment-line text-xl"></i>
    </button>
    <button class="ml-2 text-orange-500">
      <i class="ri-send-plane-2-line text-xl"></i>
    </button>
  </div>

</div>

</body>
</html>
