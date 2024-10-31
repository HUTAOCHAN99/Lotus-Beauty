<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <style>
        body {
            background: url('./src/images/background/bg-create-account.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        /* Glassmorphism effect */
        .glass {
            background: rgba(19, 62, 135, 0.3);
            /* Menggunakan warna yang sama (#133E87) dengan transparansi */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .bg-glass {
            background-color: rgba(19, 62, 135, 0.3);
            /* Menyesuaikan dengan warna dan transparansi */
        }

        .error-message {
            color: #ff6b6b;
            font-size: 0.875rem;
        }
    </style>
</head>

<body class="bg-gray-200 flex items-center justify-center min-h-screen">
    <!-- Glassmorphic Card -->
    <div class="bg-glass bg-opacity-10 relative z-10 max-w-md w-full p-8 glass rounded-lg shadow-lg backdrop-blur-lg ">
        <h2 class="text-center text-2xl font-semibold mb-6 text-white">Create Account</h2>

        <form id="createAccountForm" method="POST" action="create_account_process.php" class="space-y-5">
            <!-- Username Field -->
            <div>
                <label for="username" class="flex items-center text-white mb-1">
                    <i class="ri-user-line mr-2"></i>Username
                </label>
                <input type="text" id="username" name="username"
                    class="w-full p-3 bg-white bg-opacity-20 text-gray-100 border border-transparent rounded-lg placeholder-gray-300 focus:ring-2 focus:ring-green-400 focus:outline-none transition duration-200">
                <span class="error-message" id="usernameError"></span>
            </div>

            <!-- Email Field -->
            <div>
                <label for="email" class="flex items-center text-white mb-1">
                    <i class="ri-mail-line mr-2"></i>Email
                </label>
                <input type="email" id="email" name="email"
                    class="w-full p-3 bg-white bg-opacity-20 text-gray-100 border border-transparent rounded-lg placeholder-gray-300 focus:ring-2 focus:ring-green-400 focus:outline-none transition duration-200">
                <span class="error-message" id="emailError"></span>
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="flex items-center text-white mb-1">
                    <i class="ri-lock-line mr-2"></i>Password
                </label>
                <div class="relative">
                    <input type="password" id="password" name="password"
                        class="w-full p-3 bg-white bg-opacity-20 text-gray-100 border border-transparent rounded-lg placeholder-gray-300 focus:ring-2 focus:ring-green-400 focus:outline-none transition duration-200">
                    <span class="absolute right-3 top-3 cursor-pointer text-white" id="togglePassword">
                        <i class="ri-eye-line" id="togglePasswordIcon"></i>
                    </span>
                </div>
                <span class="error-message" id="passwordError"></span>
            </div>

            <!-- Full Name Field -->
            <div>
                <label for="full_name" class="flex items-center text-white mb-1">
                    <i class="ri-user-3-line mr-2"></i>Full Name
                </label>
                <input type="text" id="full_name" name="full_name"
                    class="w-full p-3 bg-white bg-opacity-20 text-gray-100 border border-transparent rounded-lg placeholder-gray-300 focus:ring-2 focus:ring-green-400 focus:outline-none transition duration-200">
                <span class="error-message" id="fullNameError"></span>
            </div>

            <!-- Phone Number Field -->
            <div>
                <label for="phone_number" class="flex items-center text-white mb-1">
                    <i class="ri-phone-line mr-2"></i>Phone Number
                </label>
                <input type="text" id="phone_number" name="phone_number"
                    class="w-full p-3 bg-white bg-opacity-20 text-gray-100 border border-transparent rounded-lg placeholder-gray-300 focus:ring-2 focus:ring-green-400 focus:outline-none transition duration-200">
                <span class="error-message" id="phoneError"></span>
            </div>

            <!-- Address Field -->
            <div>
                <label for="address" class="flex items-center text-white mb-1">
                    <i class="ri-map-pin-line mr-2"></i>Address
                </label>
                <input type="text" id="address" name="address"
                    class="w-full p-3 bg-white bg-opacity-20 text-gray-100 border border-transparent rounded-lg placeholder-gray-300 focus:ring-2 focus:ring-green-400 focus:outline-none transition duration-200">
                <span class="error-message" id="addressError"></span>
            </div>

            <!-- Role Field -->
            <div>
                <label for="role" class="flex items-center text-white mb-1">
                    <i class="ri-shield-user-line mr-2"></i>Role
                </label>
                <select id="role" name="role"
                    class="w-full p-3 bg-white bg-opacity-20 text-gray-100 border border-transparent rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none transition duration-200">
                    <option value="" disabled selected class="text-gray-300">Select Role</option>
                    <option value="dokter" class="text-gray-700">dokter</option>
                    <option value="cs" class="text-gray-700">customer-service</option>
                    <option value="apoteker" class="text-gray-700">Apoteker</option>
                    <option value="customer" class="text-gray-700">User</option>
                </select>
                <span class="error-message" id="roleError"></span>
            </div>

            <!-- Submit Button -->
            <button type="button" name="submitbutton" id="submitbutton"
                class="w-full bg-green-500 hover:bg-green-600 text-white py-3 rounded-lg transition duration-200 transform active:scale-95 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50">
                <i class="ri-check-line mr-2"></i>Create Account
            </button>
        </form>
    </div>

    <script>
        // JavaScript for showing/hiding password
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const togglePasswordIcon = document.getElementById('togglePasswordIcon');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            togglePasswordIcon.classList.toggle('ri-eye-line');
            togglePasswordIcon.classList.toggle('ri-eye-off-line');
        });

        document.getElementById('submitbutton').addEventListener('click', function (event) {
            event.preventDefault(); // Mencegah pengiriman form secara langsung
            let isValid = true;

            const fields = [{
                id: 'username',
                errorId: 'usernameError',
                message: 'Username is required'
            },
            {
                id: 'email',
                errorId: 'emailError',
                message: 'Email is required'
            },
            {
                id: 'password',
                errorId: 'passwordError',
                message: 'Password is required'
            },
            {
                id: 'full_name',
                errorId: 'fullNameError',
                message: 'Full name is required'
            },
            {
                id: 'phone_number',
                errorId: 'phoneError',
                message: 'Phone number is required'
            },
            {
                id: 'address',
                errorId: 'addressError',
                message: 'Address is required'
            },
            {
                id: 'role',
                errorId: 'roleError',
                message: 'Role is required'
            }
            ];

            fields.forEach(field => {
                const input = document.getElementById(field.id);
                const error = document.getElementById(field.errorId);

                if (input.value.trim() === '') {
                    error.textContent = field.message;
                    isValid = false;
                } else {
                    error.textContent = '';
                }
            });

            if (isValid) {
                console.log("Form is valid, submitting...");
                document.getElementById('createAccountForm').submit(); // Memanggil submit secara manual jika valid
            } else {
                console.warn("Form submission prevented due to validation errors.");
            }
        });
    </script>
</body>

</html>