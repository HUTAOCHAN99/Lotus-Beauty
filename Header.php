<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Navbar with Search</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        /* For smooth mobile menu and search bar toggle */
        .mobile-menu,
        .search-bar {
            display: none;
            transition: max-height 0.3s ease-in-out;
            margin-top: 1rem; /* Add margin to create space below the navbar */
        }

        .mobile-menu.open,
        .search-bar.open {
            display: block;
            max-height: 300px;
        }

        .bg-powderBlue {
            background-color: #B0E0E6;
        }

        .text-creamyBeige {
            color: #FFF5E1;
        }

        /* Fixed navbar styling */
        .navbar-fixed {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50; /* Ensure it's above other content */
        }

        /* Add padding to the top of the body to avoid overlap with the fixed navbar */
        body {
            padding-top: 4rem; /* Adjust based on the height of your navbar */
        }

        /* Style for the mobile menu */
        .mobile-menu {
            position: fixed; /* Make the mobile menu fixed */
            top: 4rem; /* Position below the navbar */
            left: 0;
            right: 0;
            z-index: 40; /* Ensure it's below the search bar */
        }

        /* Style for the search bar */
        .search-bar {
            z-index: 45; /* Ensure it's below the navbar but above other content */
        }
    </style>
</head>

<body class="bg-green">
    <!-- Navbar -->
    <nav class="bg-powderBlue shadow-md py-4 px-8 flex justify-between items-center navbar-fixed">
        <!-- Left Side (Logo + Menu) -->
        <div class="flex items-center space-x-8">
            <!-- Logo -->
            <div class="flex items-center justify-center">
                <img src="img/icon/icon_shop.png" alt="icon-shop" width="120px">
            </div>

            <!-- Main Menu (Hidden on Mobile) -->
            <div class="hidden md:flex space-x-6">
                <a href="Home.php" class="text-green-900 hover:font-bold">Home</a>
                <a href="Product_Page.php" class="text-green-900 hover:font-bold">Produk</a>
                <a href="#" class="text-green-900 hover:font-bold">Shop</a>
                <a href="Consultation_Page.php" class="text-green-900 hover:font-bold">Konsultasi</a>
                <a href="Recipe.php" class="text-green-900 hover:font-bold">Resep</a>
                <a href="#" class="text-green-900 hover:font-bold">About Us</a>
            </div>
        </div>

        <!-- Right Side (Icons) -->
        <div class="flex items-center space-x-6">
            <button class="text-green-900 hover:text-black">
                <a href="dashboard.php"><i class="ri-user-line ri-lg"></i></a>
            </button>

            <button class="text-green-900 hover:text-black">
                <a href="cart.php"><i class="ri-shopping-bag-line ri-lg"></i></a>
            </button>

            <!-- Search Icon Button -->
            <button id="search-button" class="text-green-900 hover:text-black">
                <i class="ri-search-line ri-lg"></i>
            </button>

            <!-- Mobile Menu Icon (only visible on mobile) -->
            <button id="mobile-menu-button" class="block md:hidden text-green-900 hover:text-black">
                <i class="ri-menu-line ri-lg"></i>
            </button>
        </div>
    </nav>

    <!-- Search Bar (Initially Hidden) -->
    <div id="search-bar" class="search-bar bg-white shadow-md py-4 px-8">
        <div class="max-w-2xl mx-auto flex items-center space-x-4">
            <!-- Search Input -->
            <input type="text" class="w-full border border-gray-300 rounded-md p-2" placeholder="Search for products...">
            <!-- Search Icon inside Search Bar -->
            <button class="text-green-900 hover:text-black">
                <i class="ri-search-line ri-2x"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Menu (Initially Hidden) -->
    <div id="mobile-menu" class="mobile-menu md:hidden bg-white shadow-md py-4 px-8">
        <a href="Home.php" class="block py-2 text-green-900 hover:font-bold">Home</a>
        <a href="Product_Page.php" class="block py-2 text-green-900 hover:font-bold">Produk</a>
        <a href="Purchase.php" class="block py-2 text-green-900 hover:font-bold">Shop</a>
        <a href="Consultation_Page.php" class="block py-2 text-green-900 hover:font-bold">Konsultasi</a>
        <a href="Recipe.php" class="block py-2 text-green-900 hover:font-bold">Resep</a>
        <a href="#" class="block py-2 text-green-900 hover:font-bold">About Us</a>
    </div>
    <div class="block p-4"></div>
    <script>
        const mobileMenuButton = document.getElementById("mobile-menu-button");
        const mobileMenu = document.getElementById("mobile-menu");

        const searchButton = document.getElementById("search-button");
        const searchBar = document.getElementById("search-bar");

        // Toggle mobile menu
        mobileMenuButton.addEventListener("click", () => {
            mobileMenu.classList.toggle("open");
        });

        // Toggle search bar
        searchButton.addEventListener("click", () => {
            searchBar.classList.toggle("open");
        });
    </script>
</body>

</html>
