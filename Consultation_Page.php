<?php
session_start();

// Pastikan pengguna sudah login dan memiliki role
if (!isset($_SESSION['role'])) {
    // Redirect ke halaman login jika pengguna belum login
    header('Location: login.php');
    exit();
}

$current_user_role = $_SESSION['role']; // Mendapatkan role pengguna yang sedang login
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Consultation Cards</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
  <?php include('Header.php'); ?>
  
  <div class="flex items-center justify-center py-4">
    <div class="w-full max-w-lg space-y-4 px-4">

      <!-- Chat for Customer Card -->
      <?php if ($current_user_role !== 'customer'): ?>
      <a href="Consultation.php?type=customer"
        class="flex items-center p-4 bg-green-50 rounded-lg shadow-md hover:bg-green-100 transition duration-200">
        <div class="w-16 h-16 rounded-full flex items-center justify-center bg-green-100">
          <img src="img/icon/customer-icon.jpg" alt="Customer Icon" class="w-12 h-12">
        </div>
        <div class="ml-4">
          <h3 class="text-lg font-semibold text-gray-700">Chat with Customer</h3>
          <p class="text-sm text-gray-500">If you're a customer, choose to connect with a doctor, pharmacist, or CS</p>
        </div>
      </a>
      <?php endif; ?>

      <!-- Chat Dokter Card -->
      <?php if ($current_user_role !== 'dokter'): ?>
      <a href="Consultation.php?type=dokter"
        class="flex items-center p-4 bg-blue-50 rounded-lg shadow-md hover:bg-blue-100 transition duration-200">
        <div class="w-16 h-16 rounded-full flex items-center justify-center bg-blue-100">
          <img src="img/icon/doctor-icon.jpg" alt="Doctor Icon" class="w-12 h-12">
        </div>
        <div class="ml-4">
          <h3 class="text-lg font-semibold text-gray-700">Chat Dokter</h3>
          <p class="text-sm text-gray-500">Dokter rekanan kami akan menjawab keluhan Anda</p>
        </div>
      </a>
      <?php endif; ?>

      <!-- Chat Apoteker Card -->
      <?php if ($current_user_role !== 'apoteker'): ?>
      <a href="Consultation.php?type=apoteker"
        class="flex items-center p-4 bg-orange-50 rounded-lg shadow-md hover:bg-orange-100 transition duration-200">
        <div class="w-16 h-16 rounded-full flex items-center justify-center bg-orange-100">
          <img src="img/icon/pharmacist.jpg" alt="Pharmacist Icon" class="w-12 h-12">
        </div>
        <div class="ml-4">
          <h3 class="text-lg font-semibold text-gray-700">Chat Apoteker</h3>
          <p class="text-sm text-gray-500">Apoteker akan membantumu mendapatkan obat</p>
        </div>
      </a>
      <?php endif; ?>

      <!-- Chat CS Farmaku Card -->
      <?php if ($current_user_role !== 'cs'): ?>
      <a href="Consultation.php?type=cs"
        class="flex items-center p-4 bg-blue-50 rounded-lg shadow-md hover:bg-blue-100 transition duration-200">
        <div class="w-16 h-16 rounded-full flex items-center justify-center bg-blue-100">
          <img src="img/icon/CS.jpg" alt="Customer Service Icon" class="w-12 h-12">
        </div>
        <div class="ml-4">
          <h3 class="text-lg font-semibold text-gray-700">Chat CS Farmaku</h3>
          <p class="text-sm text-gray-500">Butuh bantuan? Kami akan membantumu dalam 24 jam</p>
        </div>
      </a>
      <?php endif; ?>

    </div>
  </div>

  <?php include('Footer.php'); ?>
</body>

</html>
