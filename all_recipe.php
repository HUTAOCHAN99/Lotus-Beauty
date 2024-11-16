<?php
session_start();

// Memastikan hanya admin dan dokter yang dapat mengakses
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'dokter'])) {
    header("Location: no_access.php");
    exit;
}

include 'db.php';

// Ambil data dari tabel prescription
$sql = "SELECT prescription_id, nama_resep, doctor_name, status FROM prescription";
$result = $konek->query($sql);

$recipes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recipes[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prescription_id'], $_POST['new_status'])) {
    $prescription_id = $_POST['prescription_id'];
    $new_status = $_POST['new_status'];

    // Update status di database
    $update_sql = "UPDATE prescription SET status = ? WHERE prescription_id = ?";
    $stmt = $konek->prepare($update_sql);
    $stmt->bind_param('si', $new_status, $prescription_id);

    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "Error updating status: " . $stmt->error;
    }
    $stmt->close();
}

$konek->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescriptions List</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100">
    <nav class="w-full bg-powderBlue shadow-md p-4 flex justify-between items-center mb-4">
        <!-- Tombol Kembali ke Home -->
        <a href="product_management.php" class="text-black flex items-center space-x-2">
            <i class="ri-arrow-left-line text-xl"></i>
        </a>
        <!-- Nama Halaman -->
        <h1 class="text-gray-800 font-bold text-lg">Semua Resep</h1>
        <!-- Placeholder untuk spasi antara tombol kembali dan nama halaman -->
        <div class="w-10"></div>
    </nav>

    <!-- Content -->
    <main class="p-6">
        <div class="bg-white shadow-lg rounded-lg p-4">
            <table class="w-full border-collapse border border-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">ID</th>
                        <th class="px-4 py-2 text-left">Recipe Name</th>
                        <th class="px-4 py-2 text-left">Doctor Name</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($recipes)) {
                        foreach ($recipes as $recipe) {
                            $status_label = ucfirst($recipe['status']);
                            $action_label = $recipe['status'] === 'active' ? 'Inactive' : 'Active';
                            $action_color = $recipe['status'] === 'active' ? 'text-red-500' : 'text-green-500';
                            $status_class = $recipe['status'] === 'active'
                                ? 'text-green-800'
                                : 'text-red-800';

                            echo "<tr class='border-b transition'>";
                            echo "<td class='p-4'>{$recipe['prescription_id']}</td>";
                            echo "<td class='p-4'>{$recipe['nama_resep']}</td>";
                            echo "<td class='p-4'>{$recipe['doctor_name']}</td>";
                            echo "<td class='p-4'>
                    <span class='text-xs font-medium {$status_class}'>
                        {$status_label}
                    </span>
                  </td>";
                            echo "<td class='p-4'>
                    <a href='Edit.php?type=recipe&id={$recipe['prescription_id']}'
                        class='text-blue-500 hover:underline mr-2'>
                        Edit
                    </a>
                    <form method='POST' class='inline-block'>
                        <input type='hidden' name='prescription_id' value='{$recipe['prescription_id']}'>
                        <input type='hidden' name='new_status' value='{$action_label}'>
                        <button type='submit' class='{$action_color} hover:underline'>
                            {$action_label}
                        </button>
                    </form>
                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr>
                <td colspan='5' class='p-4 text-center text-gray-500'>
                    No prescriptions available.
                </td>
              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>