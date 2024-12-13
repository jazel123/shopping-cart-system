<?php
session_start(); // Make sure this is at the very top

// Display success message if exists
if(isset($_SESSION['success_msg'])) {
    echo "<div class='success-msg'>" . $_SESSION['success_msg'] . "</div>";
    unset($_SESSION['success_msg']); // Clear the message after displaying
}

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   setcookie('user_id', create_unique_id(), time() + 60*60*24*30);
   $user_id = $_COOKIE['user_id'];
}

if(isset($_POST['add_to_cart'])){
    $id = create_unique_id();
    $product_id = $_POST['product_id'];
    $qty = $_POST['qty'];
    
    // Get product details
    $select_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
    $select_product->execute([$product_id]);
    $fetch_product = $select_product->fetch(PDO::FETCH_ASSOC);
    
    if($fetch_product){
        // Check if product already exists in cart
        $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND product_id = ?");
        $check_cart->execute([$user_id, $product_id]);
        
        if($check_cart->rowCount() > 0){
            // Update quantity if product exists
            $update_cart = $conn->prepare("UPDATE `cart` SET qty = qty + ? WHERE user_id = ? AND product_id = ?");
            $update_cart->execute([$qty, $user_id, $product_id]);
        } else {
            // Insert new item if product doesn't exist
            $insert_cart = $conn->prepare("INSERT INTO `cart`(id, user_id, product_id, price, qty) VALUES(?,?,?,?,?)");
            $insert_cart->execute([$id, $user_id, $product_id, $fetch_product['price'], $qty]);
        }
        
        // Set success message in session
        $success_msg[] = 'Item added to cart successfully!';
        
        // Redirect back to same page to refresh cart count
        header('location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>View Products</title>
   <link rel="stylesheet" href="css/style.css">
   <style>
   .success-msg {
    background: #4CAF50;
    color: white;
    padding: 10px;
    text-align: center;
    margin: 10px 0;
    border-radius: 5px;
}
   </style>
</head>
<body>

<?php include 'components/header.php'; ?>

<section class="products">
   <h1 class="heading">all products</h1>
   <div class="box-container">
   <?php 
      $select_products = $conn->prepare("SELECT * FROM `products`");
      $select_products->execute();
      if($select_products->rowCount() > 0){
         while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="POST" class="box">
      <input type="hidden" name="product_id" value="<?= $fetch_product['id']; ?>">
      <img src="uploaded_files/<?= $fetch_product['image']; ?>" class="image" alt="">
      <h3 class="name"><?= $fetch_product['name'] ?></h3>
      <div class="flex">
         <p class="price">₱<?= $fetch_product['price'] ?></p>
         <input type="number" name="qty" required min="1" value="1" max="99" maxlength="2" class="qty">
      </div>
      <input type="submit" name="add_to_cart" value="add to cart" class="btn">
   </form>
   <?php
         }
      }else{
         echo '<p class="empty">no products added yet!</p>';
      }
   ?>
   </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<?php include 'components/alert.php'; ?>

</body>
</html>