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
        .bg-PewterBlue{
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
                    <h2 class="text-xl font-bold">Feng Hoe Tang</h2>
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
            <div class="flex flex-col justify-between">
                <h3 class="text-lg font-bold">Explore</h3>
                <ul class="mt-4 space-y-2">
                    <li><a href="Home.php" class="hover:underline">Home</a></li>
                    <li><a href="Product_Page.php" class="hover:underline">Produk</a></li>
                    <li><a href="Recipe.php" class="hover:underline">Resep</a></li>
                    <li><a href="Consultation.php" class="hover:underline">Konsultasi</a></li>
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
                    <ul class="grid grid-cols-3 gap-x-4 gap-y-2 mt-4">
                        <li><a href="mailto:info@herbilogy.com" class="text-white hover:text-yellow-500">
                                <i class="fas fa-envelope"></i> Email</a></li>
                        <li><a href="#" class="text-white hover:text-yellow-500">
                                <i class="fab fa-tiktok"></i> @herbilogy</a></li>
                        <li><a href="#" class="text-white hover:text-yellow-500">
                                <i class="fab fa-instagram"></i> @carefor.her</a></li>
                        <li><a href="#" class="text-white hover:text-yellow-500">
                                <i class="fab fa-tiktok"></i> @momilogy</a></li>
                        <li><a href="#" class="text-white hover:text-yellow-500">
                                <i class="fab fa-facebook-f"></i> Facebook</a></li>
                        <li><a href="#" class="text-white hover:text-yellow-500">
                                <i class="fab fa-instagram"></i> @partof.her</a></li>
                        <li><a href="#" class="text-white hover:text-yellow-500">
                                <i class="fab fa-instagram"></i> @momlike.her</a></li>
                        <li><a href="#" class="text-white hover:text-yellow-500">
                                <i class="fab fa-twitter"></i> Twitter</a></li>
                        <li><a href="#" class="text-white hover:text-yellow-500">
                                <i class="fab fa-youtube"></i> YouTube</a></li>
                    </ul>
                </div>

            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="mt-8 border-t border-gray-700 pt-6 text-center">
            <p>&copy; 2024 LotusBeauty Site by <a href="#" class="text-yellow-500 hover:underline">Hamba Allah</a></p>
            <ul class="flex justify-center space-x-8 mt-4">
                <li><a href="#" class="hover:underline">Terms & Conditions</a></li>
                <li><a href="#" class="hover:underline">Privacy Policy</a></li>
                <li><a href="#" class="hover:underline">Shipping and Returns</a></li>
            </ul>
        </div>
    </footer>
</body>

</html>