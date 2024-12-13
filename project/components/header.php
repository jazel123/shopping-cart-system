<?php
   if (session_status() === PHP_SESSION_NONE) {
      session_start();
   }
?>
<header class="header">

   <section class="flex">
      <a href="#" class="logo">COFFEE</a>

      <nav class="navbar">
         <?php
            if(isset($_SESSION['user'])) {
               // Get user role from session
               $user_id = $_SESSION['user'];
               $get_user = $conn->prepare("SELECT * FROM `member` WHERE mem_id = ?");
               $get_user->execute([$user_id]);
               $user = $get_user->fetch();
               
               // Only show add product link for admin
               if(isset($user['role']) && $user['role'] === 'admin'){
                  echo '<a href="add_product.php">add product</a>';
               }
            }
         ?>
         <a href="view_products.php">view products</a>
         <a href="orders.php">my orders</a>
         <a href="logout.php">Logout</a>
         <?php
            if(isset($_SESSION['user'])) {
               $user_id = $_SESSION['user'];
               $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
               $count_cart_items->execute([$user_id]);
               $total_cart_items = $count_cart_items->rowCount();
            } else {
               $total_cart_items = 0;
            }
         ?>
         <a href="shopping_cart.php" class="cart-btn">
            Cart (<?= $total_cart_items; ?>)
         </a>
      </nav>

      <div id="menu-btn" class="fas fa-bars"></div>
   </section>

</header>