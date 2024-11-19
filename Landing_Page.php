<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.5/dist/sweetalert2.min.css">
    <!-- GreatVibes fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        html {
            scroll-behavior: smooth;
        }

        /* Parallax effect */
        .parallax {
            background-image: url('src/images/background/bg-landing-page.png');
            height: 100vh;
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            transition: background-position 0.3s ease-out;
            /* Smooth transition for background position */
        }

        /* Section styles */
        .section {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .GreatVibes {
            font-family: 'Great Vibes', cursive;
        }

        .Terracota {
            color: #7C4B3A;
        }

        .emboss-text {
            font-size: 3rem;
            font-weight: bold;
            color: #7C4B3A;
            position: relative;
            text-shadow:
                1px 1px 2px rgba(255, 255, 255, 0.7),
                -1px -1px 2px rgba(0, 0, 0, 0.4);
        }
    </style>
</head>

<body class="bg-gradient-to-b from-blue-900 to-indigo-800">
    <div class="parallax relative flex flex-col justify-center items-center">
        <div class="relative z-10 flex flex-col items-center justify-center space-y-4 ">
            <h1 class="GreatVibes Terracota font-bold text-6xl sm:text-8xl md:text-9xl emboss-text">Lotus Beauty</h1>
            <blockquote class="text-white  text-lg sm:text-xl font-semibold italic">
                “Karena Keindahan Adalah Milik Kita Semua”
            </blockquote>


            <div class="flex space-x-4 mt-4">
                <a href="#"
                    class="px-6 py-2 bg-blue-600 text-white font-bold rounded-md hover:bg-blue-700 transition">Baca
                    Journal Kecantikan</a>
                <a href="AboutUs.php"
                    class="px-6 py-2 bg-purple-600 text-white font-bold rounded-md hover:bg-purple-700 transition">About
                    Us</a>
            </div>
            <div class="flex space-x-4 mt-6 text-gray-300">
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
    <!-- simpan sension login -->
    <?php
    session_start();

    if (isset($_SESSION['error_message'])) {
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: '" . $_SESSION['error_message'] . "',
                confirmButtonColor: '#d33',
            });
        });
    </script>";
        unset($_SESSION['error_message']);
    }
    ?>

    <div id="login-section" class="relative z-20 bg-gray-900 section">
        <div class="text-center w-full max-w-md">
            <h2 class="text-white text-3xl mb-6">Login</h2>
            <form method="POST" action="login.php" class="bg-white p-6 rounded-lg shadow-md"
                onsubmit="return validateForm()">
                <div class="relative mb-6">
                    <input type="text" id="username" name="username" placeholder="Username"
                        class="w-full pl-10 pr-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <span class="absolute left-3 top-2 text-gray-400">
                        <i class="ri-user-line"></i>
                    </span>
                    <span id="usernameError" class="absolute left-3 -bottom-5 text-red-500 text-sm hidden">Username
                        wajib diisi</span>
                </div>
                <div class="relative mb-6">
                    <input type="password" id="password" name="password" placeholder="Password"
                        class="w-full pl-10 pr-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <span class="absolute left-3 top-2 text-gray-400">
                        <i class="ri-lock-line"></i>
                    </span>
                    <span class="absolute right-3 top-2 cursor-pointer" id="togglePassword">
                        <i class="ri-eye-line" id="togglePasswordIcon"></i>
                    </span>
                    <span id="passwordError" class="absolute left-3 -bottom-5 text-red-500 text-sm hidden">Password
                        wajib diisi</span>
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
        // JavaScript untuk toggle visibility password
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const togglePasswordIcon = document.getElementById('togglePasswordIcon');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            togglePasswordIcon.classList.toggle('ri-eye-line');
            togglePasswordIcon.classList.toggle('ri-eye-off-line');
        });

        // Validasi form untuk menampilkan pesan required
        function validateForm() {
            let valid = true;

            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const usernameError = document.getElementById('usernameError');
            const passwordError = document.getElementById('passwordError');

            // Validasi Username
            if (usernameInput.value.trim() === '') {
                usernameError.classList.remove('hidden');
                valid = false;
            } else {
                usernameError.classList.add('hidden');
            }

            // Validasi Password
            if (passwordInput.value.trim() === '') {
                passwordError.classList.remove('hidden');
                valid = false;
            } else {
                passwordError.classList.add('hidden');
            }

            return valid;
        }
    </script>


</body>

</html>