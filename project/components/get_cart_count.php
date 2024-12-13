<?php
include 'connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
   
   $count_cart_items = $conn->prepare("SELECT COUNT(DISTINCT product_id) as item_count FROM `cart` WHERE user_id = ?");
   $count_cart_items->execute([$user_id]);
   $result = $count_cart_items->fetch(PDO::FETCH_ASSOC);
   $total_cart_items = $result['item_count'] ?? 0;
   
   header('Content-Type: application/json');
   echo json_encode(['count' => (int)$total_cart_items]);
} else {
   header('Content-Type: application/json');
   echo json_encode(['count' => 0]);
}
?> 