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
            width: 0;
            overflow: hidden;
            transition: width 0.3s ease-in-out;
        }

        .search-bar-container.open {
            width: 250px; /* Adjust width as needed */
        }

        .bg-powderBlue {
            background-color: #B0E0E6;
        }

        /* Fixed navbar styling */
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

        /* Mobile menu */
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

        <!-- Right Side (Icons + Search Bar) -->
        <div class="flex items-center space-x-4">
            <!-- Search Bar (Initially Hidden) -->
            <div id="search-bar" class="search-bar-container flex items-center bg-white shadow-md rounded-md p-1">
                <input type="text" class="w-full border-none focus:outline-none rounded-md p-2" placeholder="Search for products...">
                <button id="search-submit" class="text-green-900 hover:text-black ml-2">
                    <i class="ri-check-line ri-lg"></i>
                </button>
                <button id="search-cancel" class="text-green-900 hover:text-black ml-2">
                    <i class="ri-close-line ri-lg"></i>
                </button>
            </div>

            <!-- Search Icon Button -->
            <button id="search-toggle" class="text-green-900 hover:text-black">
                <i class="ri-search-line ri-lg"></i>
            </button>

            <button class="text-green-900 hover:text-black">
                <a href="cart.php"><i class="ri-shopping-bag-line ri-lg"></i></a>
            </button>
            <button class="text-green-900 hover:text-black">
                <a href="dashboard.php"><i class="ri-user-line ri-lg"></i></a>
            </button>

            <!-- Mobile Menu Icon (only visible on mobile) -->
            <button id="mobile-menu-button" class="block md:hidden text-green-900 hover:text-black">
                <i class="ri-menu-line ri-lg"></i>
            </button>
        </div>
    </nav>

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

        const searchToggle = document.getElementById("search-toggle");
        const searchBar = document.getElementById("search-bar");
        const searchCancel = document.getElementById("search-cancel");

        // Toggle mobile menu
        mobileMenuButton.addEventListener("click", () => {
            mobileMenu.classList.toggle("open");
        });

        // Toggle search bar
        searchToggle.addEventListener("click", () => {
            searchBar.classList.toggle("open");
            searchToggle.classList.add("hidden"); // Hide the search icon
        });

        // Handle search submit (to be implemented)
        document.getElementById("search-submit").addEventListener("click", () => {
            alert("Searching for: " + document.querySelector('#search-bar input').value);
        });

        // Handle cancel search
        searchCancel.addEventListener("click", () => {
            searchBar.classList.remove("open");
            searchToggle.classList.remove("hidden"); // Show the search icon again
            document.querySelector('#search-bar input').value = ""; // Clear the input
        });
    </script>
</body>

</html>
