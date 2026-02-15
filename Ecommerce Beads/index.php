<?php
session_start();
require_once 'db.php';

$username = $_SESSION["username"] ?? null;
$role = $_SESSION["role"] ?? null;

// Handle search and filter
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 6;
$offset = ($page - 1) * $limit;

// Base query
$sql = "SELECT * FROM products WHERE 1";
$params = [];

// Add search condition
if (!empty($search)) {
    $sql .= " AND name LIKE ?";
    $params[] = "%$search%";
}
// Add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $id = $_POST['product_id'];
    $name = $_POST['product_name'];
    $price = floatval($_POST['price']);
    $image = ['product_image'];
    $color = $_POST['preferred_color'] ?? 'Not selected';
    $quantity = intval($_POST['quantity'] ?? 1);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

// Add category filter
if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

// Count total for pagination
$countSql = str_replace("SELECT *", "SELECT COUNT(*) as total", $sql);
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$totalRows = $stmt->fetch()['total'];
$totalPages = ceil($totalRows / $limit);

// Add LIMIT clause
$sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get distinct categories for filter
$catStmt = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category ASC");
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shop | D'bossy Beads & Accessories</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .welcome-box {
            background: #fff0f5;
            padding: 20px;
            text-align: center;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .product-card {
            background: #fffafa;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
        }
        .product-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 5px;
        }
        nav {
            background: #ffe4e1;
            padding: 10px;
            text-align: center;
        }
        nav a {
            margin: 0 10px;
            color: #b22222;
            text-decoration: none;
        }
        footer {
            text-align: center;
            padding: 15px;
            background: #fceeee;
            margin-top: 20px;
        }
        select,
input[type="submit"] {
  margin-top: 10px;
  padding: 8px;
  width: 90%;
  border-radius: 6px;
  border: 1px solid #ccc;
}
.custom-color-note {
  margin-top: 10px;
  color: green;
  font-size: 0.9em;
}
.custom-color-note a {
  color: #007bff;
  text-decoration: underline;
}
.success-popup {
    position: relative;
    margin-top: 20px; /* space below marquee */
    text-align: center;
    font-size: 16px;
    color: #155724;
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    padding: 10px 20px;
    border-radius: 8px;
    width: fit-content;
    max-width: 90%;
    margin-left: auto;
    margin-right: auto;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    animation: fadeInOut 4s ease-in-out forwards;
    z-index: 999;
}

@keyframes fadeInOut {
    0%   { opacity: 0; transform: translateY(-10px); }
    10%  { opacity: 1; transform: translateY(0); }
    90%  { opacity: 1; }
    100% { opacity: 0; transform: translateY(-10px); }
}

    </style>
</head>
<body>

<?php if ($role === 'customer'): ?>
    <div class="welcome-box">
        <h1 style="font-family: 'Georgia', cursive; font-size: 32px; color: #b22222;">
            🎀 D'bossy Beads and Accessories
        </h1>
        <h2>Hey Beautiful, <?php echo htmlspecialchars($username); ?>!</h2>
        <p>Welcome to D'bossy Beads and Accessories — explore your world of beauty!</p>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
    <div style="background: #d4edda; color: #155724; padding: 10px; text-align: center;">
        <?php 
        echo $_SESSION['message']; 
        unset($_SESSION['message']); 
        ?>
    </div>
<?php endif; ?>

<?php endif; ?>

<nav style="display: flex; justify-content: space-between; align-items: center; background: linear-gradient(45deg, #f08080, #ffc0cb); padding: 10px;">
    <div style="flex: 1;">
        <marquee behavior="scroll" direction="left" scrollamount="4" style="font-weight: bold; color: #fff;">
            💖 Welcome to D'bossy Beads and Accessories! Shop our colorful and handmade beaded bags, jewelry, and more. 💖
            For any complaintsor contributions kindly reach to us on 080123456789 💖
        </marquee>
    </div>
    <div style="flex-shrink: 0;">
        <a href="cart.php" style="margin-right: 10px; font-weight: bold;">🛒 View Cart</a>
        <a href="logout.php" style="font-weight: bold;">Logout</a>
    </div>
</nav>
<?php if (isset($_GET['success'])): ?>
<div class="success-popup">
    ✅ Product added to cart!
</div>
<script>
    setTimeout(() => {
        document.querySelector('.success-popup').style.display = 'none';
    }, 3000);
</script>
<?php endif; ?>


<div class="container">
    <h2 style="text-align:center;">Our Products</h2>

    <!-- Search & Filter Form -->
    <form method="GET" style="text-align:center; margin-bottom: 20px;">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search products">
        <select name="category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat; ?>" <?php if ($category == $cat) echo 'selected'; ?>>
                    <?php echo $cat; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filter</button>
    </form>
    <div class="product-grid">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $product): 
    ?>
                <div class="product-card">
                    <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="product">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p><strong>₦<?php echo number_format($product['price'], 2); ?></strong></p>
                    <p class="category"><?php echo htmlspecialchars($product['category']); ?></p>
                    <p class="desc"><?php echo substr(htmlspecialchars($product['description']), 0, 60); ?>...</p>
                   
                   <form method="POST" action="cart.php" class="add-to-cart-form" data-product-id="<?= $product['id']; ?>">
    <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
    <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
    <input type="hidden" name="product_price" value="<?= htmlspecialchars($product['price']) ?>">
    <input type="hidden" name="product_image" value="<?= htmlspecialchars($product['image']) ?>">
   
               <label for="color_<?= $product['id']; ?>">Choose a Color:</label>
   <select name="color" class="color-select" required onchange="checkCustom(this)">
        <option value="">Select Color</option>
        <?php 
        $colors = explode(',', $product['available_colors']);
        foreach ($colors as $color):
            $color =trim($color);
           echo "<option value=\"$color\">$color</option>";
         endforeach; ?>
         <option value="custom-whatsapp" href="https://wa.me/2348031234567?text=Hello%20I%20want%20to%20customize%20this%20bag" target="_blank">Custom via WhatsApp</option>

    </select><br><br>
    <label>Quantity:</label>
    <input type="number" name="quantity" value="1" min="1" required>

    <button type="submit" name="add_to_cart">🛒 Add to Cart</button>
</form>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align:center;">No products found.</p>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <div style="text-align:center; padding-top: 20px;">
        <p>Page <?php echo $page; ?> of <?php echo $totalPages; ?></p>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&page=<?php echo $i; ?>"
               style="margin: 0 5px; <?php echo ($i == $page) ? 'font-weight: bold;' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
</div>

<footer>
    <p>&copy; <?php echo date("Y"); ?> D'bossy Beads & Accessories</p>
</footer>
<script>
function checkCustom(selectElement) {
    const form = selectElement.closest('form');
    const selectedValue = selectElement.value;

    if (selectedValue === "custom-whatsapp") {
        const name = form.querySelector('[name="product_name"]').value;
        const quantity = form.querySelector('[name="quantity"]').value;
        const image = form.querySelector('[name="product_image"]').value;

        const message = `Hello! I’d like to customize this item:\n\n` +
            `👜 Product: ${name}\n` +
            `📦 Quantity: ${quantity}\n` +
            `🎨 Color: Custom\n` +
            `📸 Image: https://yourdomain.com/images/${image}`;

        const phoneNumber = "2348031234567"; // Replace with your WhatsApp number
        const url = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
        window.open(url, '_blank');

        // Reset selection
        selectElement.value = "";
    }
}
</script>


</body>
</html>
