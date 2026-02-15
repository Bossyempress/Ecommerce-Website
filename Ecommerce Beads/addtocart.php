<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'] ?? null;
    $preferredColor = trim($_POST['preferred_color'] ?? '');
    $quantity = intval($_POST['quantity'] ?? 1);

    if ($productId && $preferredColor && $quantity > 0) {
        // Fetch product details from database
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if ($product) {
            $item = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'color' => $preferredColor,
                'quantity' => $quantity,
            ];

            // Add to session cart
            $_SESSION['cart'][] = $item;
            $_SESSION['message'] = 'Item added to cart!';
        }
    }
}

header("Location: index.php");
exit;
