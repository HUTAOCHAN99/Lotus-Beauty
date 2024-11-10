<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Team</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        /* Custom CSS for the open/close transition */
        .open {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 2s ease, transform 2s ease-in-out;
            display: block;
        }

        .close {
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 1s ease, transform 2s ease-in-out;
            display: block;
        }

        /* Optional: Hide the team section initially */
        #team-section {
            display: none;
        }
    </style>
</head>

<body class="bg-gray-100">
    <?php include 'Header.php'; ?>
    <div class="min-h-screen flex flex-col items-center justify-center mt-4 relative sm:mt-8 sm:p-4" id="about-us-section">
        <!-- About Us Section -->
        <div class="flex flex-col md:flex-row items-center justify-between w-full max-w-5xl">
            <div class="w-full md:w-[40%] text-center mb-8 md:mb-0">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">About Us</h2>
                <div class="flex items-center justify-center">
                    <img src="img\icon\LotusBeauty.png" alt="Team Logo" class="w-xl rounded-xl shadow-lg"
                        style="box-shadow: inset 5px 5px 10px rgba(0, 0, 0, 0.3), inset -5px -5px 10px rgba(255, 255, 255, 0.3);">
                </div>
            </div>
            <div class="w-full md:w-[40%] text-left">
                <blockquote class="text-gray-800 italic border-l-4 border-gray-300 pl-4 my-6">
                    LotusBeauty adalah tim yang selalu melihat ke belakang untuk belajar dari pengalaman, memperbaiki
                    kesalahan, dan terus berinovasi guna menciptakan solusi yang lebih baik untuk masa depan.
                </blockquote>
            </div>
        </div>

        <!-- Button to Show Team Section -->
        <button id="open-team-button"
            class="absolute bottom-16 left-1/2 transform -translate-x-1/2 text-gray-800 text-3xl hover:text-gray-600 transition duration-300 flex items-center">
            <i id="open-icon" class="fas fa-chevron-down mr-2"></i>
            <span>Buka Tim</span>
        </button>

        <!-- Button to Hide Team Section -->


        <!-- Team Section -->
        <div id="team-section" class="w-full max-w-8xl mt-8 transition-opacity duration-500 ease-in-out close">
            <h3 class="text-3xl font-semibold text-gray-800 mb-6 text-center">Meet Our Team</h3>
            <div id="team-member1" class="flex flex-col space-y-4">
                <!-- Member 1 -->
                <div class="member w-full h-full flex flex-col md:flex-row bg-white shadow-lg rounded-lg p-4">
                    <div class="w-full md:w-1/2 flex items-center justify-center mb-4 md:mb-0">
                        <img src="img\team\zhofir.jpg" alt="Member 1" class="w-full h-3/4 object-cover rounded-md">
                    </div>
                    <div class="w-full md:w-1/2 flex flex-col items-center justify-center text-center">
                        <h4 class="text-xl font-bold text-gray-800">Ahmad Zhofir Amanullah Nayif</h4>
                        <p class="text-gray-600">Universitas Pembangunan Nasional "Veteran" Yogyakarta</p>
                        <p class="text-gray-600">Ketua Tim</p>
                        <p class="text-gray-600">Front End Developer</p>
                        <h4 class="font-bold mt-3">Contact me</h4>
                        <div class="mt-2 flex justify-center space-x-4 bg-slate-600 rounded-lg p-4">
                            <a href="https://www.instagram.com/ahmad_zhofir_a_nayif" target="_blank"
                                class="text-white hover:text-blue-300">
                                <i class="fab fa-instagram fa-2x"></i>
                            </a>
                            <a href="https://www.facebook.com/ahmad.z.an.75457" target="_blank"
                                class="text-white hover:text-blue-300">
                                <i class="fab fa-facebook fa-2x"></i>
                            </a>
                            <a href="https://github.com/HUTAOCHAN99" target="_blank"
                                class="text-white hover:text-blue-300">
                                <i class="fab fa-github fa-2x"></i>
                            </a>
                            <a href="https://www.linkedin.com/in/ahmad-zhofir-amanullah-nayif-56130a285" target="_blank"
                                class="text-white hover:text-blue-300">
                                <i class="fab fa-linkedin fa-2x"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Member 2 -->
                <div class="member w-full h-full flex flex-col md:flex-row bg-white shadow-lg rounded-lg p-4">
                    <div class="w-full md:w-1/2 flex items-center justify-center mb-4 md:mb-0">
                        <img src="img\team\hafid.jpg" alt="Member 3" class="w-full h-3/4 object-cover rounded-md" />
                    </div>
                    <div class="w-full md:w-1/2 flex flex-col items-center justify-center text-center">
                        <h4 class="text-xl font-bold text-gray-800">Hafid Dwi Saputra</h4>
                        <p class="text-gray-600">
                            Universitas Pembangunan Nasional "Veteran" Yogyakarta
                        </p>
                        <p class="text-gray-600">Anggota Tim</p>
                        <p class="text-gray-600">Front end Developer</p>
                        <h4 class="text-center justify-center font-bold mt-3">
                            Contact me
                        </h4>
                        <div class="mt-2 flex justify-center space-x-4 bg-slate-600 rounded-lg p-4">
                            <a href="https://www.instagram.com/hadstra?igsh=MXJxcDVsMzQ4YWo1cQ%3D%3D" target="_blank"
                                rel="noopener noreferrer" class="text-white hover:text-blue-300">
                                <i class="fab fa-instagram fa-2x"></i>
                            </a>
                            <a href="https://www.facebook.com/profile.php?id=100014185997586&mibextid=ZbWKwL"
                                target="_blank" rel="noopener noreferrer" class="text-white hover:text-blue-300">
                                <i class="fab fa-facebook fa-2x"></i>
                            </a>
                            <a href="https://www.youtube.com/" target="_blank" rel="noopener noreferrer"
                                class="text-white hover:text-blue-300">
                                <i class="fab fa-youtube fa-2x"></i>
                            </a>
                            <a href="https://github.com/hafid89" target="_blank" rel="noopener noreferrer"
                                class="text-white hover:text-blue-300">
                                <i class="fab fa-github fa-2x"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <button id="close-team-button"
                class="absolute bottom-16 left-1/2 transform -translate-x-1/2 text-gray-800 text-3xl hover:text-gray-600 transition duration-300 flex items-center hidden">
                <i id="close-icon" class="fas fa-chevron-up mr-2"></i>
                <span>Tutup Tim</span>
            </button>
        </div>
    </div>
    <?php include 'Footer.php'; ?>
    <script>
        document.getElementById('open-team-button').addEventListener('click', function () {
            const teamSection = document.getElementById('team-section');
            const openButton = document.getElementById('open-team-button');
            const closeButton = document.getElementById('close-team-button');

            // Show the team section and toggle buttons
            teamSection.style.display = 'block';
            teamSection.classList.remove('close');
            teamSection.classList.add('open');

            // Hide Open button and show Close button
            openButton.classList.add('hidden');
            closeButton.classList.remove('hidden');

            // Scroll to the first team member
            document.getElementById('team-member1').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });

        document.getElementById('close-team-button').addEventListener('click', function () {
            const teamSection = document.getElementById('team-section');
            const openButton = document.getElementById('open-team-button');
            const closeButton = document.getElementById('close-team-button');

            // Hide the team section and toggle buttons
            teamSection.classList.remove('open');
            teamSection.classList.add('close');

            setTimeout(function () {
                teamSection.style.display = 'none';

                // After closing animation, scroll to the about-us section
                document.getElementById('about-us-section').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 1000); // Wait for the transition to complete

            // Show Open button and hide Close button
            openButton.classList.remove('hidden');
            closeButton.classList.add('hidden');
        });
    </script>
</body>

</html>