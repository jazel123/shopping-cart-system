<?php
include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   setcookie('user_id', create_unique_id(), time() + 60*60*24*30);
   $user_id = $_COOKIE['user_id'];
}

// Handle Delete Item
if(isset($_POST['delete_item'])){
   $cart_id = $_POST['cart_id'];
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ? AND user_id = ?");
   $delete_cart_item->execute([$cart_id, $user_id]);
   header('location: shopping_cart.php');
   exit();
}

// Handle Update Quantity
if(isset($_POST['update_cart'])){
   $cart_id = $_POST['cart_id'];
   $qty = $_POST['qty'];
   $update_qty = $conn->prepare("UPDATE `cart` SET qty = ? WHERE id = ? AND user_id = ?");
   $update_qty->execute([$qty, $cart_id, $user_id]);
   header('location: shopping_cart.php');
   exit();
}

// Handle Empty Cart
if(isset($_POST['empty_cart'])){
   $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
   $delete_cart->execute([$user_id]);
   header('location: shopping_cart.php');
   exit();
}

$select_cart = $conn->prepare("SELECT c.*, p.name, p.image, p.price 
                             FROM `cart` c 
                             INNER JOIN `products` p ON c.product_id = p.id 
                             WHERE c.user_id = ?");
$select_cart->execute([$user_id]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Shopping Cart</title>
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/header.php'; ?>

<section class="products">
   <h1 class="heading">Shopping Cart</h1>

   <div class="box-container">
   <?php
      $grand_total = 0;
      if($select_cart->rowCount() > 0){
         while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
            $sub_total = ($fetch_cart['qty'] * $fetch_cart['price']);
   ?>
   <form action="" method="POST" class="box">
      <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
      <div class="product-card">
         <div class="image-container">
            <img src="uploaded_files/<?= $fetch_cart['image']; ?>" class="image" alt="">
         </div>
         <div class="details-container">
            <h3 class="name"><?= $fetch_cart['name']; ?></h3>
            <div class="price-details">
               <p class="price">Price: ₱<?= $fetch_cart['price']; ?></p>
               <p class="sub-total">Sub total: ₱<?= $sub_total; ?></p>
            </div>
            <div class="quantity-container">
               <span>Quantity: </span>
               <div class="qty-control">
                  <input type="number" name="qty" required min="1" value="<?= $fetch_cart['qty']; ?>" max="99" maxlength="2" class="qty">
                  <button type="submit" name="update_cart" class="update-btn">Update</button>
               </div>
            </div>
            <div class="button-container">
               <input type="submit" value="Delete" name="delete_item" class="delete-btn" onclick="return confirm('delete this item?');">
               <a href="checkout.php?buy_now=<?= $fetch_cart['id']; ?>" class="buy-now-btn">Buy Now</a>
            </div>
         </div>
      </div>
   </form>
   <?php
            $grand_total += $sub_total;
         }
   ?>
   </div>

   <div class="cart-total">
      <p class="total-items">Total Items: <span><?= $select_cart->rowCount(); ?></span></p>
      <p>Grand total: <span>₱<?= $grand_total; ?></span></p>
      <div class="cart-buttons">
         <a href="checkout.php" class="btn <?= ($grand_total > 0) ? '' : 'disabled' ?>">proceed to checkout</a>
      </div>
      <div class="empty-cart-container">
         <form action="" method="POST">
            <input type="submit" value="empty cart" name="empty_cart" class="empty-cart-btn" onclick="return confirm('empty your cart?');">
         </form>
      </div>
   </div>
   <?php
      }else{
         echo '<p class="empty">your cart is empty!</p>';
      }
   ?>
</section>

<style>
.box {
   background: #fff;
   border-radius: 8px;
   padding: 30px;
   margin-bottom: 40px;
   box-shadow: 0 2px 5px rgba(0,0,0,0.1);
   width: 100%;
   max-width: 400px;
   margin-left: auto;
   margin-right: auto;
}

.box-container {
   max-width: 1200px;
   margin: 0 auto;
   padding: 40px;
   display: flex;
   flex-wrap: wrap;
   gap: 40px;
   justify-content: center;
}

.product-card {
   display: flex;
   flex-direction: column;
   align-items: center;
   gap: 20px;
}

.image-container {
   width: 200px;
   height: 200px;
   margin-bottom: 15px;
}

.image {
   width: 100%;
   height: 100%;
   object-fit: cover;
   border-radius: 8px;
}

.details-container {
   width: 100%;
   text-align: center;
   display: flex;
   flex-direction: column;
   gap: 15px;
}

.name {
   font-size: 1.8rem;
   color: #333;
   margin: 0;
}

.price-details {
   margin: 10px 0;
}

.price, .sub-total {
   font-size: 1.2rem;
   color: #666;
   margin: 8px 0;
}

.quantity-container {
   display: flex;
   flex-direction: column;
   align-items: center;
   gap: 10px;
}

.qty-control {
   display: flex;
   align-items: center;
   gap: 10px;
}

.qty {
   width: 80px;
   padding: 8px;
   border: 1px solid #ddd;
   border-radius: 4px;
   text-align: center;
   font-size: 1.1rem;
}

.update-btn {
   background: #4CAF50;
   color: white;
   padding: 8px 20px;
   border: none;
   border-radius: 4px;
   cursor: pointer;
   font-size: 1.1rem;
}

.delete-btn {
   background: #ff4444;
   color: white;
   padding: 12px;
   border: none;
   border-radius: 4px;
   cursor: pointer;
   width: 45%;
   text-align: center;
   font-size: 1.1rem;
}

.buy-now-btn {
   background: #4CAF50;
   color: white;
   padding: 12px;
   border: none;
   border-radius: 4px;
   cursor: pointer;
   width: 45%;
   text-align: center;
   text-decoration: none;
   font-size: 1.1rem;
}

.buy-now-btn:hover {
   background: #45a049;
}

.cart-total {
   background: #fff;
   padding: 30px;
   border-radius: 8px;
   box-shadow: 0 2px 5px rgba(0,0,0,0.1);
   max-width: 800px;
   margin: 40px auto;
   text-align: center;
}

.cart-buttons {
   display: flex;
   justify-content: center;
   margin: 20px 0;
}

.cart-total p {
   font-size: 1.4rem;
   text-align: center;
   margin: 0;
}

.cart-total span {
   font-weight: bold;
   font-size: 1.6rem;
   color: #333;
}

.btn {
   background: #4CAF50;
   color: white;
   padding: 15px 30px;
   border-radius: 4px;
   text-decoration: none;
   text-align: center;
   font-size: 1.1rem;
   width: 200px;
}

.btn.disabled {
   opacity: 0.5;
   pointer-events: none;
}

.empty-cart-container {
   margin-top: 20px;
   border-top: 1px solid #eee;
   padding-top: 20px;
}

.empty-cart-btn {
   background: #ff4444;
   color: white;
   padding: 15px 30px;
   border: none;
   border-radius: 4px;
   cursor: pointer;
   font-size: 1.1rem;
   width: 200px;
}

.button-container {
   display: flex;
   gap: 10px;
   justify-content: center;
   margin-top: 15px;
}

.total-items {
   font-size: 1.4rem;
   text-align: center;
   margin-bottom: 10px !important;
}

.total-items span {
   font-weight: bold;
   color: #333;
}
</style>

</body>
</html>