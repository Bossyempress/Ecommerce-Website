<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'customer') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Account - Beads Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION["username"]; ?>!</h2>
    <p>This is your customer dashboard.</p>

    <ul>
        <li><a href="index.php">Go to Homepage</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html>
