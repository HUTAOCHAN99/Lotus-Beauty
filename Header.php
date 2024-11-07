<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php'; // Pastikan koneksi ke database sudah benar

// Mengambil role dari session jika ada
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Navbar with Search</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        /* Smooth transition for search bar */
        .search-bar-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 0;
            opacity: 0;
            visibility: hidden;
            overflow: hidden;
            transition: width 0.3s ease-in-out, opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
        }

        .search-bar-container.open {
            width: 250px;
            opacity: 1;
            visibility: visible;
        }

        .bg-powderBlue {
            background-color: #B0E0E6;
        }

        .navbar-fixed {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
        }

        body {
            padding-top: 4rem;
        }

        .mobile-menu {
            display: none;
            transition: max-height 0.3s ease-in-out;
            margin-top: 1rem;
        }

        .mobile-menu.open {
            display: block;
            max-height: 300px;
        }
    </style>
</head>

<body class="bg-green">
    <nav class="bg-powderBlue shadow-md py-4 px-8 flex justify-between items-center navbar-fixed ">
        <div class="flex items-center space-x-8">
            <div class="flex items-center justify-center">
                <img src="img/icon/icon_shop.png" alt="icon-shop" width="120px">
            </div>

            <div class="hidden md:flex space-x-6">
                <a href="Home.php" class="text-green-900 hover:font-bold">Home</a>
                <a href="Product_Page.php" class="text-green-900 hover:font-bold">Produk</a>
                
                <!-- Hanya tampilkan link Konsultasi jika role bukan 'admin' -->
                <?php if ($role !== 'admin'): ?>
                    <a href="Consultation_Page.php" class="text-green-900 hover:font-bold">Konsultasi</a>
                <?php endif; ?>
                
                <a href="Recipe.php" class="text-green-900 hover:font-bold">Resep</a>
                <a href="AboutUs.php" class="text-green-900 hover:font-bold">About Us</a>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            <div id="search-bar" class="search-bar-container bg-white shadow-md rounded-md p-1">
                <form action="Product_Page.php" method="GET" class="flex items-center">
                    <input type="text" name="search" class="w-full border-none focus:outline-none rounded-md p-2" placeholder="Search for products...">
                    <button type="submit" class="text-green-900 hover:text-black ml-2">
                        <i class="ri-search-line ri-lg"></i>
                    </button>
                    <button id="search-cancel" type="button" class="text-green-900 hover:text-black ml-2">
                        <i class="ri-close-line ri-lg"></i>
                    </button>
                </form>
            </div>

            <button id="search-toggle" class="text-green-900 hover:text-black">
                <i class="ri-search-line ri-lg"></i>
            </button>
            <button class="text-green-900 hover:text-black">
                <a href="cart.php"><i class="ri-shopping-bag-line ri-lg"></i></a>
            </button>
            <button class="text-green-900 hover:text-black">
                <a href="dashboard.php"><i class="ri-user-line ri-lg"></i></a>
            </button>

            <button id="mobile-menu-button" class="block md:hidden text-green-900 hover:text-black">
                <i class="ri-menu-line ri-lg"></i>
            </button>
        </div>
    </nav>

    <div id="mobile-menu" class="mobile-menu md:hidden bg-white shadow-md py-4 px-8">
        <a href="Home.php" class="block py-2 text-green-900 hover:font-bold">Home</a>
        <a href="Product_Page.php" class="block py-2 text-green-900 hover:font-bold">Produk</a>
        <a href="Purchase.php" class="block py-2 text-green-900 hover:font-bold">Shop</a>
        
        <!-- Hanya tampilkan link Konsultasi jika role bukan 'admin' -->
        <?php if ($role !== 'admin'): ?>
            <a href="Consultation_Page.php" class="block py-2 text-green-900 hover:font-bold">Konsultasi</a>
        <?php endif; ?>
        
        <a href="Recipe.php" class="block py-2 text-green-900 hover:font-bold">Resep</a>
        <a href="AboutUs.php" class="block py-2 text-green-900 hover:font-bold">About Us</a>
    </div>
    <div class="block my-4"></div>
    <script>
        const mobileMenuButton = document.getElementById("mobile-menu-button");
        const mobileMenu = document.getElementById("mobile-menu");
        const searchToggle = document.getElementById("search-toggle");
        const searchBar = document.getElementById("search-bar");
        const searchCancel = document.getElementById("search-cancel");

        mobileMenuButton.addEventListener("click", () => {
            mobileMenu.classList.toggle("open");
        });

        searchToggle.addEventListener("click", () => {
            searchBar.classList.add("open");
            searchToggle.classList.add("hidden");
        });

        searchCancel.addEventListener("click", () => {
            setTimeout(() => {
                searchBar.classList.remove("open");
                searchToggle.classList.remove("hidden");
                document.querySelector('#search-bar input').value = "";
            }, 300);
        });
    </script>

</body>
</html>
