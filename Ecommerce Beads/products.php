<?php
// Debugging: show errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db.php';
session_start();

$msg = "";

// Handle search and filter
$search = $_GET['search'] ?? '';
$categoryFilter = $_GET['category_filter'] ?? '';

$sql = "SELECT * FROM products WHERE 1";
$params = [];

if (!empty($search)) {
    $sql .= " AND name LIKE ?";
    $params[] = '%' . $search . '%';
}

if (!empty($categoryFilter)) {
    $sql .= " AND category = ?";
    $params[] = $categoryFilter;
}

$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();


// Handle upload form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $price = trim($_POST["price"]);
    $category = trim($_POST["category"]);
    $available_colors = $_POST['available_colors']?? '';
    $description = trim($_POST["description"]);

    $imageName = str_replace(' ', '_', basename($_FILES["image"]["name"]));
    $targetDir = "images/";
    $targetFile = $targetDir . $imageName;

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        $stmt = $pdo->prepare("INSERT INTO products (name, price, category, description, available_colors, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $price, $category, $description, $available_colors, $imageName]);
        $msg = "✅ Product uploaded successfully!";
    } else {
        $msg = "❌ Failed to upload image.";
    }
}

// Fetch all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add & View Products | D'bossy Beads</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>D'bossy Beads and Accessories</h1>
    </div>

    <div class="container">
        <h2>Add a New Product</h2>

        <?php if (!empty($msg)): ?>
            <p class="message"><?php echo $msg; ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="product-form">
            <label>Product Name:</label>
            <input type="text" name="name" required>

            <label>Price (₦):</label>
            <input type="number" name="price" required>

            <label>Category:</label>
            <select name="category" required>
                <option value="">-- Select Category --</option>
                <option value="Bags">Bags</option>
                <option value="Jewelry">Jewelry</option>
                <option value="Accessories">Accessories</option>
            </select>

            <label>Description:</label>
            <textarea name="description" rows="4" required></textarea>

            <label>Available Colors (comma-separated)</label>
            <input type="text" name="colors" placeholder="e.g. Red, Blue, Custom via WhatsApp" 
value="<?= isset($product) ? htmlspecialchars($product['available_colors']) : '' ?>" required>      

            <label>Product Image:</label>
            <input type="file" name="image" accept="image/*" required>

            <button type="submit">Upload Product</button>
        </form>
   <h2 style="margin-top: 60px;">Search & Filter Products</h2>

<form method="GET" class="filter-form">
    <input type="text" name="search" placeholder="Search by name..." value="<?php echo htmlspecialchars($search); ?>">
<!--<input type="number" name="quantity" min="1" value="1">-->

    <select name="category_filter">
        <option value="">All Categories</option>
        <option value="Bags" <?php if ($categoryFilter === 'Bags') echo 'selected'; ?>>Bags</option>
        <option value="Jewelry" <?php if ($categoryFilter === 'Jewelry') echo 'selected'; ?>>Jewelry</option>
        <option value="Accessories" <?php if ($categoryFilter === 'Accessories') echo 'selected'; ?>>Accessories</option>
    </select>

    <button type="submit">Apply</button>
</form>


        <h2 style="margin-top: 50px;">All Uploaded Products</h2>

        <?php if (empty($products)): ?>
            <p>No products yet. Add some above.</p>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                   <div class="product-card">
    <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="product">
    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
    <p class="price">₦<?php echo number_format($product['price'], 2); ?></p>
    <p class="category"><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
    <p class="desc"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
    <p><strong>Colors:</strong> <?php echo htmlspecialchars((string) $product['available_colors']); ?></p>
    <div class="product-actions">
        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="edit-btn">✏️ Edit</a>
        <a href="deleteproduct.php?id=<?php echo $product['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this product?');">🗑️ Delete</a>
    </div>
</div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
     <footer>
        <p>&copy; <?php echo date("Y"); ?> D'bossy Beads & Accessories</p>
    </footer>
</body>
</html>
