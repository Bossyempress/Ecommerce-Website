<?php
require_once 'db.php';
session_start();

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"] ?? '';
    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';
    $confirm = $_POST["confirm"] ?? '';

    if ($password !== $confirm) {
        $msg = "Passwords do not match!";
    } else {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            $msg = "Username already taken!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'customer')");
            $stmt->execute([$username, $email, $hashed]);
            $msg = "Registration successful!";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register - Beads Shop</title>
    <link rel="stylesheet" href="style.css">
    <style>
        input {
            border-radius: 15px;
            padding: 10px;
            border: 2px solid #ccc;
            width: 700px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>D'bossy Beads and Accessories</h1>
           <em>Where Beauty and Class Meet...</em>
    </div>
    <!-- <h2>Register</h2>
    <p style="color:red;"><?php echo $msg; ?></p>
    <form method="POST" align="center">
        <input type="text" name="username" placeholder="Username" required><br><br>
        <input type="email" name="email" placeholder="Email Address" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <button type="submit">Register</button>
    </form>-->

    <div class="container">
        <h2>REGISTER</h2>
        <p style="color:red;"><?php echo $msg; ?></p>
       <form method="POST" enctype="multipart/form-data" class="product-form">
    <label>Username:</label>
    <input type="name" name="username" required>

    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Password:</label>
    <input type="password" name="password" required>

    <label>Confirm Password:</label>
    <input type="password" name="confirm" required>

    <button type="submit">Register</button>
</form>
<p>Already registered? <a href="login.php">Login here</a></p>

    </div>
     <footer>
        <p>&copy; <?php echo date("Y"); ?> D'bossy Beads & Accessories</p>
    </footer>
</body>
</html>
