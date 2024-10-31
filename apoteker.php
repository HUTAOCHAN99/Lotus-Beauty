<h1>Apoteker Chat</h1>

<?php
// Ambil daftar pengguna (customer) yang menghubungi apoteker
$userQuery = "SELECT DISTINCT u.user_id, u.username FROM messages m JOIN users u ON m.user_id = u.user_id WHERE m.recipient_id = ?";
$userStmt = $konek->prepare($userQuery);
$userStmt->bind_param("i", $_SESSION['user_id']); // user_id apoteker dari session
$userStmt->execute();
$userResult = $userStmt->get_result();
?>

<div>
    <h2>Pengguna yang Menghubungi</h2>
    <?php while ($user = $userResult->fetch_assoc()) : ?>
        <a href="?user_id=<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['username']); ?></a><br>
    <?php endwhile; ?>
</div>

<?php
// Menampilkan pesan dari pengguna yang dipilih
$selectedUserId = isset($_GET['user_id']) ? $_GET['user_id'] : null;
if ($selectedUserId) {
    $messageQuery = "SELECT m.*, u.username FROM messages m JOIN users u ON m.user_id = u.user_id WHERE m.recipient_id = ? AND m.user_id = ? ORDER BY m.created_at ASC";
    $messageStmt = $konek->prepare($messageQuery);
    $messageStmt->bind_param("ii", $_SESSION['user_id'], $selectedUserId);
    $messageStmt->execute();
    $messageResult = $messageStmt->get_result();

    echo "<h3>Pesan dari " . htmlspecialchars($selectedUserId) . "</h3>";
    while ($message = $messageResult->fetch_assoc()) {
        echo "<strong>" . htmlspecialchars($message['username']) . ":</strong> " . htmlspecialchars($message['message_text']) . "<br>";
    }

    // Input untuk mengirim pesan
    ?>
    <form method="POST" action="send_message.php">
        <input type="hidden" name="user_id" value="<?php echo $selectedUserId; ?>">
        <input type="text" name="message_text" placeholder="Ketik pesan di sini..." required>
        <button type="submit">Kirim</button>
    </form>
    <?php
}
?>
