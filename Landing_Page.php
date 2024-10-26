<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>

<body class="bg-gradient-to-b from-blue-900 to-indigo-800">
    <div class="relative text-center h-screen flex flex-col justify-center items-center">
        <div class="absolute inset-0 overflow-hidden">
            <img src="your-image-path.png" alt="Background" class="object-cover w-full h-full opacity-70">
        </div>

        <div class="relative z-10 flex flex-col items-center justify-center space-y-4">
            <h1 class="text-white font-bold text-4xl sm:text-6xl md:text-7xl">Lotus Beauty</h1>
            <p class="text-gray-300 text-lg sm:text-xl">.....Jargon entahlah.....</p>
            <div class="flex space-x-4 mt-4">
                <a href="#" class="px-6 py-2 bg-blue-600 text-white font-bold rounded-md hover:bg-blue-700 transition">Baca Journal Kecantikan</a>
                <a href="#" class="px-6 py-2 bg-purple-600 text-white font-bold rounded-md hover:bg-purple-700 transition">About Us</a>
            </div>
            <div class="flex space-x-4 mt-6 text-gray-300">
                <a href="#" class="hover:underline">Subscribe</a>
                <a href="#" class="hover:underline">Official Site</a>
            </div>
        </div>

        <div class="relative z-10 mt-10 flex justify-center">
            <a href="#login-section" class="text-white animate-bounce">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </a>
        </div>
    </div>

    <div id="login-section" class="relative z-20 bg-gray-900 w-full h-screen flex flex-col justify-center items-center">
        <div class="text-center">
            <h2 class="text-white text-3xl mb-6">Login</h2>
            <form method="POST" action="login.php" class="max-w-md mx-auto">
                <input type="text" id="username" name="username" placeholder="Username" class="w-full px-4 py-2 mb-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <input type="password" id="password" name="password" placeholder="Password" class="w-full px-4 py-2 mb-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <input type="hidden" name="action" value="login">
                <button type="submit" class="w-full px-6 py-2 bg-blue-600 text-white font-bold rounded-md hover:bg-blue-700 transition">Login</button>
            </form>
            <div class="mt-4 flex">
                <div class="m-auto flex">
                    <a href="create_account.php" class="text-gray-300 hover:underline">Register</a>
                    <span class="text-gray-400"> | </span>

                    <form method="POST" action="verify_token.php" class="max-w-md mx-auto">
                        <input type="hidden" name="action" value="password_reset">
                        <a href="request_token.php?action=password_reset" class="text-gray-300 hover:underline">Lupa Password?</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>






<!-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="max-w-md mx-auto p-6 bg-white shadow-md rounded-lg mt-10">
        <h2 class="text-center text-2xl font-bold mb-6">Login</h2>
        <form method="POST" action="login.php" class="space-y-4">
            <div>
                <label for="username" class="block text-gray-700">Username:</label>
                <input type="text" id="username" name="username" placeholder="Username" required class="w-full p-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label for="password" class="block text-gray-700">Password:</label>
                <input type="password" id="password" name="password" placeholder="Password" required class="w-full p-2 border border-gray-300 rounded-lg">
            </div>
            <button type="submit" name="submit" class="w-full bg-blue-500 text-white p-2 rounded-lg">Login</button>
        </form>
        <p class="mt-4 text-center">
            Belum punya akun? <a href="create_account.php" class="text-blue-500">Buat Akun</a>
        </p>
    </div>
</body>

</html> -->

</html>