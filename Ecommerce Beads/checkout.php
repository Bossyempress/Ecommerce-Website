<?php
require_once 'db.php';
session_start();

if (empty($_SESSION["cart"])) {
    echo "Cart is empty";
    header("Location: cart.php");
    exit();
}
 
$cartItems = $_SESSION["cart"];
$total = 0;

foreach ($cartItems as $product_id => $item) {
    $qty = isset($item["quantity"]) ? (int)$item["quantity"] : 1;
    $total += $item["price"] * $qty;
}

$msg = "";

// Handle checkout
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $address = trim($_POST["address"]);
    $user_id = $_SESSION["user_id"] ?? null;

    foreach ($cartItems as $item) {
        $qty = isset($item["quantity"]) ? (int)$item["quantity"] : 1;
        $preferredColor = $item["color"] ?? null;

        $stmt = $pdo->prepare("INSERT INTO orders (user_id, product_id, quantity, total_price, preferred_color, status) 
                               VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([
    $user_id,
    $product_id,
    $qty,
    $item["price"] * $qty,
    $preferredColor
]);

    }

    $_SESSION["cart"] = []; // Clear cart
    $msg = "🎉 Thank you $name! Your order has been placed successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Checkout</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Checkout</h2>
<a href="cart.php">← Back to Cart</a>

<?php if ($msg): ?>
    <p style="color:green;"><?php echo $msg; ?></p>
<?php else: ?>
    <form method="POST">
    <h3>Delivery Information</h3>
    <input type="text" name="name" placeholder="Your Name" required><br><br>
    <input type="number" name="Phine number" placeholder="Your Phone Number" require><br><br>
    <textarea name="address" placeholder="Delivery Address" required></textarea><br><br>

    <h3>Order Summary</h3>
    <div class="cart-container">
        <?php foreach ($cartItems as $item): ?>
    <?php
        $name = htmlspecialchars($item["name"]);
        $image = htmlspecialchars($item["image"]);
        $price = floatval($item["price"]);
        $quantity = isset($item["quantity"]) ? intval($item["quantity"]) : 1;
        $color = isset($item["color"]) ? htmlspecialchars($item["color"]) : 'Not selected';
        $itemTotal = $price * $quantity;
    ?>

            <div class="cart-card">
        <img src="images/<?php echo $image; ?>" alt="Product Image">
        <div class="details">
            <h4><?php echo $name; ?></h4>
            <p><strong>Color:</strong> <?php echo $color; ?></p>
            <p><strong>Price:</strong> ₦<?php echo number_format($price, 2); ?></p>
            <p><strong>Quantity:</strong> <?php echo $quantity; ?></p>
            <p><strong>Total:</strong> ₦<?php echo number_format($itemTotal, 2); ?></p>
        </div>
            </div>
        <?php endforeach; ?>
    </div>

    <p><strong>Grand Total: ₦<?php echo number_format($total, 2); ?></strong></p>
    <button type="submit">✅ Place Order</button>
</form>

<?php endif; ?>

<footer>
    <p>&copy; <?php echo date("Y"); ?> D'bossy Beads & Accessories</p>
</footer>
</body>
</html>
