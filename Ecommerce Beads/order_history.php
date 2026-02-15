<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION["username"];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE username = ? ORDER BY order_date DESC");
$stmt->execute([$username]);
$orders = $stmt->fetchAll();
?>

<h2>Your Order History</h2>
<table border="1" cellpadding="6">
    <tr>
        <th>Product</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Preferred Color</th>
        <th>Order Date</th>
    </tr>
    <?php foreach ($orders as $order): ?>
        <tr>
            <td><?php echo htmlspecialchars($order['product_name']); ?></td>
            <td><?php echo $order['quantity']; ?></td>
            <td>₦<?php echo number_format($order['price'], 2); ?></td>
            <td><?php echo htmlspecialchars($order['preferred_color']); ?></td>
            <td><?php echo $order['order_date']; ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<a href="index.php">← Back to Shop</a>
