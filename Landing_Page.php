<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css">
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
            <img src="src/images/background/bg-landing-page.jpg" alt="Background" class="object-cover w-full h-full opacity-70">
        </div>

        <div class="relative z-10 flex flex-col items-center justify-center space-y-4">
            <h1 class="text-white font-bold text-4xl sm:text-6xl md:text-7xl">Lotus Beauty</h1>
            <p class="text-gray-300 text-lg sm:text-xl">.....Jargon entahlah.....</p>
            <div class="flex space-x-4 mt-4">
                <a href="#"
                    class="px-6 py-2 bg-blue-600 text-white font-bold rounded-md hover:bg-blue-700 transition">Baca
                    Journal Kecantikan</a>
                <a href="#"
                    class="px-6 py-2 bg-purple-600 text-white font-bold rounded-md hover:bg-purple-700 transition">About
                    Us</a>
            </div>
            <div class="flex space-x-4 mt-6 text-gray-300">
                <a href="#" class="hover:underline">Subscribe</a>
                <a href="Home.php" class="hover:underline">Official Site</a>
            </div>
        </div>

        <div class="relative z-10 mt-10 flex justify-center">
            <a href="#login-section" class="text-white animate-bounce">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </a>
        </div>
    </div>

    <div id="login-section" class="relative z-20 bg-gray-900 w-full h-screen flex flex-col justify-center items-center p-4">
    <div class="text-center w-full max-w-md">
        <h2 class="text-white text-3xl mb-6">Login</h2>
        <form method="POST" action="login.php" class="bg-white p-6 rounded-lg shadow-md">
            <div class="relative mb-4">
                <input type="text" id="username" name="username" placeholder="Username"
                    class="w-full pl-10 pr-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                <span class="absolute left-3 top-2 text-gray-400">
                    <i class="ri-user-line"></i>
                </span>
            </div>
            <div class="relative mb-6">
                <input type="password" id="password" name="password" placeholder="Password"
                    class="w-full pl-10 pr-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                <span class="absolute left-3 top-2 text-gray-400">
                    <i class="ri-lock-line"></i>
                </span>
                <span class="absolute right-3 top-2 cursor-pointer" id="togglePassword">
                    <i class="ri-eye-line" id="togglePasswordIcon"></i>
                </span>
            </div>
            <input type="hidden" name="action" value="login">
            <button type="submit"
                class="w-full px-6 py-2 bg-blue-600 text-white font-bold rounded-md hover:bg-blue-700 transition">Login</button>
        </form>
        <div class="mt-4 flex flex-col items-center">
            <div class="flex">
                <a href="create_account.php" class="text-gray-300 hover:underline">Register</a>
                <span class="text-gray-400 mx-2">|</span>
                <a href="request_token.php?action=password_reset" class="text-gray-300 hover:underline">Lupa
                    Password?</a>
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript to toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const togglePasswordIcon = document.getElementById('togglePasswordIcon');

    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        togglePasswordIcon.classList.toggle('ri-eye-line');
        togglePasswordIcon.classList.toggle('ri-eye-off-line');
    });
</script>




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