<?php
require_once 'db.php';
session_start();

// Optional: restrict access to logged-in admins
// if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== 'admin') {
//     header("Location: login.php");
//     exit();
// }

$id = $_GET["id"] ?? null;
if (!$id) {
    header("Location: products.php");
    exit();
}

// Fetch product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo "Product not found!";
    exit();
}

$msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $desc = trim($_POST["description"]);
    $price = trim($_POST["price"]);
    $category = trim($_POST["category"]);
    $colors = trim($_POST["colors"]);
    $stock = $_POST["stock"] ?? null;

    $image = $product['image']; // keep old image unless new uploaded

    // If new image uploaded
    if (!empty($_FILES["image"]["name"])) {
        $newImage = str_replace(' ', '_', basename($_FILES["image"]["name"]));
        $tmp = $_FILES["image"]["tmp_name"];
        $targetPath = "images/" . $newImage;

        if (move_uploaded_file($tmp, $targetPath)) {
            // delete old image
            if (!empty($product['image']) && file_exists("images/" . $product['image'])) {
                unlink("images/" . $product['image']);
            }
            $image = $newImage;
        } else {
            $msg = "❌ Failed to upload new image. Keeping existing one.";
        }
    }

    $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, image=?, category=?, available_colors=?, stock=? WHERE id=?");
    $stmt->execute([$name, $desc, $price, $image, $category, $colors, $stock, $id]);

    $msg = "✅ Product updated successfully!";

    // Re-fetch updated product
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product | D'bossy Beads</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .edit-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: #fff5f9;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #b22222;
        }

        form label {
            font-weight: bold;
        }

        form input, form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        form button {
            background-color: #b22222;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #ff69b4;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 30px;
            text-decoration: none;
            color: #555;
        }

        .message {
            text-align: center;
            color: green;
            font-weight: bold;
        }

        .product-img {
            max-width: 100%;
            height: auto;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <div class="edit-container">
        <h2>Edit Product</h2>

        <?php if (!empty($msg)): ?>
            <p class="message"><?php echo $msg; ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label>Product Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>

            <label>Description:</label>
            <textarea name="description" rows="4" required><?php echo htmlspecialchars($product['description']); ?></textarea>

            <label>Price (₦):</label>
            <input type="number" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" step="0.01" required>

            <label>Category:</label>
            <input type="text" name="category" value="<?php echo htmlspecialchars($product['category']); ?>" required>

            <label>Colors:</label>
            <input type="text" name="colors" value="<?php echo htmlspecialchars($product['available_colors']); ?>" required>

            <label>Stock:</label>
            <input type="number" name="stock" value="<?php echo htmlspecialchars($product['stock'] ?? 0); ?>">

            <label>Current Image:</label><br>
            <img src="images/<?php echo htmlspecialchars($product['image']); ?>" class="product-img" alt="Current Product Image"><br>

            <label>Change Image:</label>
            <input type="file" name="image" accept="image/*">

            <button type="submit">Update Product</button>
        </form>

        <a href="products.php" class="back-link">← Back to Products</a>
    </div>

</body>
</html>
