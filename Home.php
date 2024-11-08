<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Utama</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
    <style>
        /* Pastikan navbar selalu berada di lapisan atas */
        nav {
            z-index: 1000;
        }

        /* Sembunyikan tombol panah carousel pada awalnya */
        .swiper-button-next,
        .swiper-button-prev {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        /* Tampilkan tombol panah hanya saat mouse berada di atas carousel */
        .swiper-container:hover .swiper-button-next,
        .swiper-container:hover .swiper-button-prev {
            opacity: 1;
        }

        /* Mengatur ukuran elemen Swiper dengan kelas .home */
        .swiper-container.home {
            max-width: 800px;
            /* Sesuaikan ukuran yang diinginkan */
            height: 500px;
            /* Tinggi sesuai kebutuhan */
        }

        .swiper-container.home img {
            object-fit: cover;
            width: 100%;
            height: 100%;
        }

        /* Style untuk arrow buttons */
        .swiper-button-next,
        .swiper-button-prev {
            color: white;
            /* Warna tombol */
            width: 36px;
            /* Lebar tombol */
            height: 36px;
            /* Tinggi tombol */
            background: rgba(0, 0, 0, 0.5);
            padding: 1.5rem;
            /* Warna latar belakang */
            border-radius: 100%;
            /* Membuat tombol bulat */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
            /* Pastikan tombol di atas */
        }


        /* Posisi tombol panah */
        .swiper-button-next {
            right: 10px;
            /* Adjust as needed */
        }

        .swiper-button-prev {
            left: 10px;
            /* Adjust as needed */
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Bagian Atas -->
    <?php include('Header.php'); ?>
    <header class="bg-gray-100 p-4">
        <!-- Carousel Event -->
        <div class="swiper-container home-swiper home m-auto max-w-4xl rounded-lg shadow-lg overflow-hidden relative">
            <div class="swiper-wrapper">
                <?php
                $directory = "img/event/";
                $images = glob($directory . "*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE);
                foreach ($images as $image) {
                    echo '<div class="swiper-slide">';
                    echo '<img src="' . $image . '" alt="Gambar Promo" class="w-full h-auto object-cover rounded-lg">';
                    echo '</div>';
                }
                ?>
            </div>
            <div class="absolute bottom-4 right-4 z-20">
                <a href="event.php" class="mt-2 px-4 py-2 bg-orange-500 rounded-lg text-white">Lihat Semua</a>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination"></div>
        </div>

    </header>


    <!-- Bagian Ikon Grid -->
    <section class="grid grid-cols-3 gap-4 p-4 border-lg bg-gray-200">
        <div class="flex flex-col items-center">
            <!-- Ikon produk obat -->
            <a href="Product_Page.php"><i class="ri-medicine-bottle-line text-3xl text-blue-500"></i></a>
            <p class="text-sm text-gray-700 mt-2">Produk</p>
        </div>
        <div class="flex flex-col items-center">
            <!-- Ikon resep -->
            <a href="Recipe.php">
                <i class="ri-file-text-line text-3xl text-blue-500"></i>
            </a>
            <p class="text-sm text-gray-700 mt-2">Resep</p>
        </div>
        <div class="flex flex-col items-center">
            <!-- Ikon keranjang belanja -->
            <a href="cart.php">
                <i class="ri-shopping-cart-line text-3xl text-blue-500"></i>
            </a>
            <p class="text-sm text-gray-700 mt-2">Keranjang</p>
        </div>
    </section>

    <!-- Carousel Promosi -->
    <!-- Carousel Promosi -->
    <div class="flex justify-between items-center px-8 py-4">
        <h2 class="text-2xl font-bold">Promo Menarik Untukmu</h2>
        <a href="event.php" class="text-orange-500 hover:text-slate-500 transition">Lihat Semua</a>
    </div>


    <div class="swiper-container home-swiper home m-auto max-w-4xl rounded-lg shadow-lg overflow-hidden relative p-4">
        <div class="swiper-wrapper">
            <?php
            $directory = "src/images/carousel/";
            $images = glob($directory . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);
            foreach ($images as $image) {
                echo '<div class="swiper-slide">';
                echo '<img src="' . $image . '" alt="Gambar Promo" class="w-full h-auto object-cover rounded-lg">';
                echo '</div>';
            }
            ?>
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination"></div>
    </div>
    <!-- Recomendation -->
    <?php include('Recomendation.php'); ?>
    <!-- Recomemdation End-->

    <!-- Navigasi Bawah -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white shadow-md p-4 flex justify-around z-1000">
        <div class="flex flex-col items-center">
            <a href="Home.php"><i class="ri-home-4-line text-xl text-gray-700"></i></a>
            <p class="text-xs text-gray-700">Home</p>
        </div>
        <div class="flex flex-col items-center">
            <a href="Recipe.php"><i class="ri-file-list-3-line text-xl text-gray-700"></i></a>
            <p class="text-xs text-gray-700">Resep</p>
        </div>
        <?php if ($role !== 'admin'): ?>
            <div class="flex flex-col items-center">
                <a href="Consultation_Page.php"><i class="ri-message-line text-xl text-gray-700"></i></a>
                <p class="text-xs text-gray-700">Konsultasi</p>
            </div>
            <div class="flex flex-col items-center">
            <?php endif; ?>
            <a href="dashboard.php" class="flex flex-col items-center">
                <i class="ri-user-line text-xl text-gray-700"></i>
                <p class="text-xs text-gray-700">Akun</p>
            </a>

        </div>
    </nav>

    <!-- Footer -->
    <?php include('Footer.php'); ?>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <script>
        const homeSwiper = new Swiper('.home-swiper', {
            loop: true,
            autoplay: { delay: 3000 },
            pagination: { el: '.swiper-pagination', clickable: true },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    </script>

</body>

</html>