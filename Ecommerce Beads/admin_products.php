<?php
require_once 'db.php';
session_start();

// Optional: protect the page with admin login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: adminlogin.php");
     exit();
 }

$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | All Products</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>D'bossy Beads - Admin Dashboard</h1>
    </div>

    <div class="container">
        <h2>All Uploaded Products</h2>

        <?php if (empty($products)): ?>
            <p>No products found.</p>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="price">₦<?php echo number_format($product['price'], 2); ?></p>
                        <p class="category"><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
                        <p class="desc"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
