<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard - Beads Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION["username"]; ?>!</h2>
    <ul>
        <li><a href="products.php">Manage Products</a></li>
        <li><a href="orders.php">View Orders</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
     <footer>
        <p>&copy; <?php echo date("Y"); ?> D'bossy Beads & Accessories</p>
    </footer>
</body>
</html>
