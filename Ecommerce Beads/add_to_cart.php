<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['product_id'];
    $name = $_POST['product_name'];
    $price = $_POST['price'];
    $color = $_POST['color'];
    $quantity = $_POST['quantity'];
    $image = $_POST['image'];

    $item = [
        'id' => $id,
        'name' => $name,
        'price' => $price,
        'color' => $color,
        'quantity' => $quantity,
        'image' => $image
    ];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $_SESSION['cart'][] = $item;
    echo 'Item added';
}
