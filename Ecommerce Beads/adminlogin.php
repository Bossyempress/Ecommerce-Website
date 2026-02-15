<?php
session_start();
require_once "db.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"] ?? '';
    $password = $_POST["password"] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role = 'admin'");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && $user["password"] === $password) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];
        header("Location: dashboard.php");
        exit();
    } else {
        $msg = "Invalid admin credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login | D'bossy Beads</title>
    <link rel="stylesheet" href="style.css">
     <style>
        input {
            border-radius: 15px;
            padding: 10px;
            border: 2px solid #ccc;
            width: 90%;
            align: 'center';
        }
    </style>
    
</head>
<body>
     <div class="header">
        <h1>D'bossy Beads and Accessories</h1>
           <em>Hey Beauty, Welcome back! You've got this!!</em>
    </div>
   <div class="container">
        <h2>LOGIN</h2>
       <p style="color:red;"><?php echo $msg; ?></p>
        <form method="POST" enctype="multipart/form-data" class="product-form">
            <label>Username:</label>
            <input type="Name" name="Name" required>

            <label>Password:</label>
            <input type="password" name="password" required>
            <!--<span onclick="togglePassword()" i class="bi bi-eye" style="position: absolute; right: 70px; top: 310px; cursor: pointer-->

            <button type="submit">Login</button>
        </form>
    </div>
</body>
        </form>
    </div>
 <footer>
        <p>&copy; <?php echo date("Y"); ?> D'bossy Beads & Accessories</p>
    </footer>
</html>
