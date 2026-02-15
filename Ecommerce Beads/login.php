<?php
require_once 'db.php';
session_start();

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];

        if ($user["role"] == 'admin') {
            header("Location: dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $msg = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login - Beads Shop</title>
    <link rel="stylesheet" href="style.css">
     <style>
        input {
            border-radius: 15px;
            padding: 10px;
            border: 2px solid #ccc;
            width: 750px;
        }
        button{
            border-radius: 15px;
            padding: 10px;
            border: 2px solid #ccc;
            width: 350px; 
        }
    </style>
</head>
<body>
     <div class="header">
        <h1>D'bossy Beads and Accessories</h1>
           <em>Hey Beauty, Welcome back!!!</em>
    </div>
   <div class="container">
        <h2>LOGIN</h2>
        <p style="color:red;"><?php echo $msg; ?></p>
        <form method="POST" enctype="multipart/form-data" class="product-form">
            <label>Username:</label>
            <input type="Name" name="username" required>

            <label>Password:</label>
            <input type="password" name="password" required>
            <!--<span onclick="togglePassword()" i class="bi bi-eye" style="position: absolute; right: 70px; top: 310px; cursor: pointer;">👁️</span>-->

            <button type="submit">Login</button>
            <p>Don't have an account yet? <a href="Register.php">Register here</a></p>
        </form>
    </div>
     <footer>
        <p>&copy; <?php echo date("Y"); ?> D'bossy Beads & Accessories</p>
    </footer>
</body>
</html>
