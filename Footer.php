<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Herbilogy Footer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-PewterBlue {
            background-color: #8aa6b3;
        }
    </style>
</head>

<body>
    <footer class="bg-PewterBlue text-white pl-4 pr-4 py-10">
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Address and Join as Reseller -->
            <div class="flex flex-col justify-between">
                <div>
                    <h2 class="text-xl font-bold">LotusBeauty</h2>
                    <p class="mt-4">
                        Jalan Kebahagiaan No. 42<br>
                        (Dekat dengan Taman Canda dan Tawa)<br>
                        Komplek Suka Hati, Bogor, Indonesia<br>
                        Kode Pos: 12345 (Jangan bingung, ini bukan kode rahasia!)
                    </p>

                    <p class="mt-2">Operational Hours: Mon-Fri 9:00-17:00</p>
                </div>
                <button class="mt-4 px-6 py-2 bg-white text-green-900 font-semibold rounded">Join as reseller</button>
            </div>

            <!-- Explore Links -->
            <?php
            // Mendapatkan nama file saat ini menggunakan basename
            $currentPage = basename($_SERVER['PHP_SELF']);
            ?>

            <div class="flex flex-col justify-between">
                <h3 class="text-lg font-bold">Explore</h3>
                <ul class="mt-4 space-y-2">
                    <li>
                        <!-- Jika sudah berada di Home.php, arahkan ke #home, jika tidak arahkan ke Home.php -->
                        <?php if ($currentPage == 'Home.php'): ?>
                            <a href="#" class="hover:underline">Home</a> <!-- Mengarah ke bagian atas -->
                        <?php else: ?>
                            <a href="Home.php" class="hover:underline">Home</a> <!-- Mengarah ke Home.php -->
                        <?php endif; ?>
                    </li>
                    <li>
                        <!-- Jika sudah berada di Product_Page.php, arahkan ke #product, jika tidak arahkan ke Product_Page.php -->
                        <?php if ($currentPage == 'Product_Page.php'): ?>
                            <a href="#" class="hover:underline">Produk</a> <!-- Mengarah ke bagian produk -->
                        <?php else: ?>
                            <a href="Product_Page.php" class="hover:underline">Produk</a>
                            <!-- Mengarah ke Product_Page.php -->
                        <?php endif; ?>
                    </li>
                    <li>
                        <!-- Jika sudah berada di Recipe.php, arahkan ke #recipe, jika tidak arahkan ke Recipe.php -->
                        <?php if ($currentPage == 'Recipe.php'): ?>
                            <a href="#" class="hover:underline">Resep</a> <!-- Mengarah ke bagian resep -->
                        <?php else: ?>
                            <a href="Recipe.php" class="hover:underline">Resep</a> <!-- Mengarah ke Recipe.php -->
                        <?php endif; ?>
                    </li>
                    <li>
                        <!-- Jika sudah berada di Consultation_Page.php, arahkan ke #consultation, jika tidak arahkan ke Consultation_Page.php -->
                        <?php if ($currentPage == 'Consultation_Page.php'): ?>
                            <a href="#c" class="hover:underline">Konsultasi</a>
                            <!-- Mengarah ke bagian konsultasi -->
                        <?php else: ?>
                            <a href="Consultation_Page.php" class="hover:underline">Konsultasi</a>
                            <!-- Mengarah ke Consultation_Page.php -->
                        <?php endif; ?>
                    </li>
                </ul>
            </div>



            <!-- Stay Connected and Social Media -->
            <div class="flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-bold">Stay Connected</h3>
                    <p class="mt-4">Sign up to receive inspiration, tips from chefs, & more!</p>
                    <div class="mt-4 flex">
                        <input type="email" placeholder="Enter your email address"
                            class="p-2 rounded-l w-full text-black">
                        <button class="bg-yellow-500 p-2 rounded-r">Subscribe</button>
                    </div>
                </div>

                <!-- Social Media Links -->
                <div class="mt-8">
                    <h4 class="font-semibold">Other Social Media</h4>
                    <ul
                        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-x-4 gap-y-4 mt-4 text-xl">
                        <li><a href="mailto:info@lotusbeauty.com" target="_blank" rel="noopener noreferrer"
                                class="text-white hover:text-yellow-500 transition-colors duration-300">
                                <i class="fas fa-envelope text-2xl"></i></a></li>
                        <li><a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer"
                                class="text-white hover:text-yellow-500 transition-colors duration-300">
                                <i class="fab fa-instagram text-2xl"></i></a></li>
                        <li><a href="https://www.tiktok.com/@" target="_blank" rel="noopener noreferrer"
                                class="text-white hover:text-yellow-500 transition-colors duration-300">
                                <i class="fab fa-tiktok text-2xl"></i></a></li>
                        <li><a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer"
                                class="text-white hover:text-yellow-500 transition-colors duration-300">
                                <i class="fab fa-facebook-f text-2xl"></i></a></li>
                        <li><a href="https://twitter.com/" target="_blank" rel="noopener noreferrer"
                                class="text-white hover:text-yellow-500 transition-colors duration-300">
                                <i class="fab fa-twitter text-2xl"></i></a></li>
                        <li><a href="https://www.youtube.com/c/" target="_blank" rel="noopener noreferrer"
                                class="text-white hover:text-yellow-500 transition-colors duration-300">
                                <i class="fab fa-youtube text-2xl"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Footer Bottom -->
        <div class="mt-8 border-t border-gray-700 pt-6 text-center">
            <p>&copy; 2024 LotusBeauty Site by <a href="AboutUs.php" class="text-yellow-500 hover:underline">LotusBeauty Group</a>
            </p>
        </div>
    </footer>
</body>

</html>