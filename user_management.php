<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css">
    <title>Manajemen Pengguna</title>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="container mx-auto p-8 text-center">
        <h1 class="text-4xl font-bold mb-10 text-gray-800">Fitur Manajemen Pengguna</h1>
        <div class="flex justify-center">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">

                <!-- Kartu: Semua Akun -->
                <a href="account_management.php"
                    class="bg-white shadow-lg rounded-xl p-6 flex items-center justify-center flex-col hover:bg-gray-50 transform hover:scale-105 transition duration-300 ease-in-out">
                    <i class="ri-user-2-line text-5xl text-gray-800 mb-3"></i>
                    <h2 class="font-semibold text-xl text-gray-800">Semua Akun</h2>
                    <p class="text-gray-600 text-center">Lihat dan kelola semua akun pengguna</p>
                </a>

                <!-- Kartu: Chat Semua -->
                <a href="admin.php"
                    class="bg-white shadow-lg rounded-xl p-6 flex items-center justify-center flex-col hover:bg-gray-50 transform hover:scale-105 transition duration-300 ease-in-out">
                    <i class="ri-chat-3-line text-5xl text-gray-800 mb-3"></i>
                    <h2 class="font-semibold text-xl text-gray-800">Chat Semua</h2>
                    <p class="text-gray-600 text-center">Lihat semua percakapan pengguna</p>
                </a>

            </div>
        </div>
        <div class="text-center mt-8">
            <a href="dashboard.php" class="text-sm text-gray-600 hover:text-gray-800">← Kembali ke user
                management</a>
        </div>
    </div>

</body>

</html>