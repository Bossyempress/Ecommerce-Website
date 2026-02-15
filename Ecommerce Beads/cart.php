<?php
include 'db.php';
session_start();

$cart = $_SESSION['cart'] ?? [];
 $grandTotal = 0;


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $id = $_POST['product_id'];
    $name = $_POST['product_name'];
    $price = floatval($_POST['product_price']);
    $image = $_POST['product_image'];
    $color = $_POST['color'] ?? 'Not selected';
    $quantity = intval($_POST['quantity'] ?? 1);
    

    $cartItem = [
        'product_id' => $id,
        'name' => $name,
        'price' => $price,
        'image' => $image,
        'color' => $color,
        'quantity' => $quantity
    ];

    $_SESSION['cart'][] = $cartItem;

    header("Location: index.php?success=1");
    exit;
}

// Handle clear cart
if (isset($_GET['clear_cart'])) {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit;
}

// Handle remove item
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    header("Location: cart.php");
    exit;
}
// Handle quantity update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $index => $newQty) {
            $newQty = max(1, intval($newQty)); // Ensure minimum quantity is 1
            if (isset($_SESSION['cart'][$index])) {
                $_SESSION['cart'][$index]['quantity'] = $newQty;
            }
        }
        // Refresh the page to show updated totals
        header("Location: cart.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Cart | D'bossy Beads and Accessories</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom right, #f8e1f4, #fff);
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            background: linear-gradient(to right, #ff9a9e, #fad0c4);
            color: #333;
            padding: 15px;
            border-radius: 15px;
        }
        .cart-container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 0 10px #ccc;
        }
        .cart-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-right: 20px;
            border-radius: 10px;
        }
        .cart-details {
            flex: 1;
        }
        .cart-controls {
            text-align: right;
        }
        .cart-controls form {
            display: inline;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 10px;
            background: #ff9a9e;
            color: white;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn:hover {
            background: #ff758c;
        }
        .total {
            font-weight: bold;
            margin-top: 20px;
        }
        .whatsapp {
            display: inline-block;
            background-color: #25D366;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h2>🛒 Your Cart</h2>
    <div class="cart-container">
        <a href="index.php" class="btn">← Continue Shopping</a>
        <a href="cart.php?clear_cart=true" class="btn" style="background-color: #ccc;">🗑️ Clear Cart</a>
        <br><br><br>
        <?php if (empty($cart)): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <form method="POST">
                <?php foreach ($cart as $index => $item): ?>
    <?php
        $product_id = $item['product_id'] ?? 0;
        $quantity = $item['quantity'] ?? 1;
        $color = $item['color'] ?? 'Not selected';

        $product = null;
        $total = 0;

        if ($product_id > 0) {
            $query = mysqli_query($conn, "SELECT * FROM products WHERE id = $product_id");
            if ($query && mysqli_num_rows($query) > 0) {
                $product = mysqli_fetch_assoc($query);
                $price = $product['price']; 
                }
                /*$total = $price * $quantity;*/
               /* $grandTotal += $total;*/
            }

                        // Prepare WhatsApp message
                        $productName = urlencode($product['name']);
                        $productImage = "http://yourdomain.com/uploads/" . urlencode($product['image']); // replace with actual domain
                       $waMessage = urlencode("Hello, I’d like a custom color for:\n\n$productName\nQuantity: $quantity\nPreferred Color: Custom\nImage: $productImage");
                       $waLink = "https://wa.me/2348031234567?text=$waMessage"; // Replace with real number
                       ?>       
                    <div class="cart-item">
                        <img src="images/<?= $product['image'] ?>" alt="Product Image">
                        <div class="cart-details">
                            <strong><?= htmlspecialchars($product['name']) ?></strong><br>
                            Color: <?= htmlspecialchars($color) ?><br>
                            Price: ₦<?= number_format($product['price']) ?><br>
                           <?php $itemTotal = $product['price'] * $quantity; ?>
                           Total: ₦<?= number_format($itemTotal) ?><br>
                           <?php $grandTotal += $itemTotal; ?>

                            Quantity: <input type="number" name="quantities[<?= $index ?>]" value="<?= $quantity ?>" min="1" style="width: 60px;">
                            <?php if (strtolower($color) === 'custom via whatsapp'): ?>
                                <br><a href="<?= $waLink ?>" class="whatsapp" target="_blank">📩 Custom via WhatsApp</a>
                            <?php endif; ?>
                        </div>
                        <div class="cart-controls">
                            <a href="cart.php?remove=<?= $index ?>" class="btn" style="background-color: #f44336;">❌ Remove</a>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="total">Grand Total: ₦<?= number_format($grandTotal) ?></div>
                <button type="submit" name="update_cart" class="btn">✅ Update Cart</button>
                <a href="checkout.php" class="btn">Proceed to Checkout →</a>
            </form>
        <?php endif; ?>
    </div>
    <script>
function checkCustom(selectElement) {
    if (selectElement.value === 'custom-whatsapp') {
        const form = selectElement.closest('form');
        const name = form.querySelector('[name="product_name"]').value;
        const quantity = form.querySelector('[name="quantity"]').value;
        const image = form.querySelector('[name="product_image"]').value;

        const message = `Hello! I’d like to customize this item:\n\n` +
            `👜 Product: ${name}\n` +
            `📦 Quantity: ${quantity}\n` +
            `🎨 Color: Custom\n` +
            `📸 Image: ${image}`;

        const phone = '2348031234567';
        const whatsappURL = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
        window.open(whatsappURL, '_blank');

        selectElement.value = ''; // Reset dropdown
    }
}
</script>

</body>
</html>
