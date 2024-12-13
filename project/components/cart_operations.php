<?php
include 'connect.php';

function addToCart($user_id, $product_id, $qty) {
    global $conn;
    
    $id = create_unique_id();
    
    // Get product price
    $select_price = $conn->prepare("SELECT * FROM `products` WHERE id = ? LIMIT 1");
    $select_price->execute([$product_id]);
    $fetch_price = $select_price->fetch(PDO::FETCH_ASSOC);

    // Insert into cart
    $insert_cart = $conn->prepare("INSERT INTO `cart`(id, user_id, product_id, price, qty) VALUES(?,?,?,?,?)");
    return $insert_cart->execute([$id, $user_id, $product_id, $fetch_price['price'], $qty]);
} 