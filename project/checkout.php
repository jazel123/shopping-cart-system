<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   setcookie('user_id', create_unique_id(), time() + 60*60*24*30);
}

if(isset($_POST['place_order'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $address = $_POST['address'];
   $address = filter_var($address, FILTER_SANITIZE_STRING);
   $method = $_POST['method'];
   $method = filter_var($method, FILTER_SANITIZE_STRING);

   $verify_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $verify_cart->execute([$user_id]);

   if(isset($_GET['buy_now'])){
      // Handle single item purchase
      $cart_id = $_GET['buy_now'];
      $get_cart_item = $conn->prepare("SELECT * FROM `cart` WHERE id = ? AND user_id = ?");
      $get_cart_item->execute([$cart_id, $user_id]);
      if($get_cart_item->rowCount() > 0){
         $fetch_cart = $get_cart_item->fetch(PDO::FETCH_ASSOC);
         
         $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, address, method, product_id, price, qty, date) VALUES(?,?,?,?,?,?,?,?,?,?)");
         $insert_order->execute([
            $user_id, 
            $name, 
            $number, 
            $email, 
            $address, 
            $method,
            $fetch_cart['product_id'],
            $fetch_cart['price'],
            $fetch_cart['qty'],
            date('Y-m-d')
         ]);

         // Delete the item from cart
         $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ? AND user_id = ?");
         $delete_cart_item->execute([$cart_id, $user_id]);

         header('location:orders.php');
         exit();
      }
   } else {
      // Handle full cart purchase
      if($verify_cart->rowCount() > 0){
         while($fetch_cart = $verify_cart->fetch(PDO::FETCH_ASSOC)){
            $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, address, method, product_id, price, qty, date) VALUES(?,?,?,?,?,?,?,?,?,?)");
            $insert_order->execute([
               $user_id, 
               $name, 
               $number, 
               $email, 
               $address, 
               $method,
               $fetch_cart['product_id'],
               $fetch_cart['price'],
               $fetch_cart['qty'],
               date('Y-m-d')
            ]);
         }

         // Empty the cart after order placement
         $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
         $delete_cart->execute([$user_id]);

         header('location:orders.php');
         exit();
      }
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Checkout</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">

   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/header.php'; ?>

<section class="checkout">
   <h1 class="heading">Checkout Summary</h1>
   <div class="row">
      <form action="" method="POST">
         <h3>Billing Details</h3>
         <div class="flex">
            <div class="box">
               <p>your name <span>*</span></p>
               <input type="text" name="name" required maxlength="50" placeholder="enter your name" class="input">
               <p>your number <span>*</span></p>
               <input type="number" name="number" required maxlength="10" placeholder="enter your number" class="input" min="0" max="9999999999">
               <p>your email <span>*</span></p>
               <input type="email" name="email" required maxlength="50" placeholder="enter your email" class="input">
               <p>payment method <span>*</span></p>
               <select name="method" class="input" required>
                  <option value="cash on delivery">cash on delivery</option>
                  <option value="credit or debit card">credit or debit card</option>
                  <option value="net banking">net banking</option>
                  <option value="UPI or wallets">UPI or RuPay</option>
               </select>
               <p>address type <span>*</span></p>
               <select name="address_type" class="input" required> 
                  <option value="home">home</option>
                  <option value="office">office</option>
               </select>
            </div>
            <div class="box">
               <p>address line 01 <span>*</span></p>
               <input type="text" name="flat" required maxlength="50" placeholder="e.g. flat & building number" class="input">
               <p>address line 02 <span>*</span></p>
               <input type="text" name="street" required maxlength="50" placeholder="e.g. street name & locality" class="input">
               <p>city name <span>*</span></p>
               <input type="text" name="city" required maxlength="50" placeholder="enter your city name" class="input">
               <p>country name <span>*</span></p>
               <input type="text" name="country" required maxlength="50" placeholder="enter your country name" class="input">
               <p>pin code <span>*</span></p>
               <input type="number" name="pin_code" required maxlength="6" placeholder="e.g. 123456" class="input" min="0" max="999999">
            </div>
         </div>
         <input type="submit" value="place order" name="place_order" class="btn">
      </form>

      <div class="summary">
         <h3 class="title">Cart Items</h3>
         <?php
            $grand_total = 0;
            if(isset($_GET['buy_now'])){
               $cart_id = $_GET['buy_now'];
               $select_cart = $conn->prepare("SELECT c.*, p.name, p.image FROM `cart` c 
                                           INNER JOIN `products` p ON c.product_id = p.id 
                                           WHERE c.id = ? AND c.user_id = ?");
               $select_cart->execute([$cart_id, $user_id]);
               
               if($select_cart->rowCount() > 0){
                  $fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC);
                  $sub_total = $fetch_cart['price'] * $fetch_cart['qty'];
                  $grand_total = $sub_total;
         ?>
         <div class="flex">
            <img src="uploaded_files/<?= $fetch_cart['image']; ?>" class="image" alt="">
            <div>
               <h3 class="name"><?= $fetch_cart['name']; ?></h3>
               <p class="price">₱<?= $fetch_cart['price']; ?> x <?= $fetch_cart['qty']; ?></p>
            </div>
         </div>
         <?php
               }
            }
         ?>
         <div class="grand-total">
            <span>Grand Total:</span>
            <p>₱<?= $grand_total; ?></p>
         </div>
      </div>
   </div>
</section>

<style>
.summary {
   background: #fff;
   border-radius: 8px;
   padding: 20px;
   box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.summary .title {
   font-size: 1.4rem;
   color: #333;
   margin-bottom: 20px;
}

.summary .flex {
   display: flex;
   gap: 20px;
   align-items: center;
   margin-bottom: 20px;
   padding-bottom: 15px;
   border-bottom: 1px solid #eee;
}

.summary .image {
   width: 100px;
   height: 100px;
   object-fit: cover;
   border-radius: 8px;
}

.summary .name {
   font-size: 1.2rem;
   color: #333;
   margin-bottom: 10px;
}

.summary .price {
   color: #666;
   font-size: 1.1rem;
}

.grand-total {
   display: flex;
   justify-content: space-between;
   align-items: center;
   padding-top: 15px;
   border-top: 2px solid #eee;
   margin-top: 20px;
}

.grand-total span {
   font-size: 1.2rem;
   color: #333;
}

.grand-total p {
   font-size: 1.4rem;
   color: #333;
   font-weight: bold;
}

.row {
   display: grid;
   grid-template-columns: 1.5fr 1fr;
   gap: 30px;
   max-width: 1200px;
   margin: 0 auto;
   padding: 20px;
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<script src="js/script.js"></script>

<?php include 'components/alert.php'; ?>

</body>
</html>