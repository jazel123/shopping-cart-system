<!DOCTYPE html>
<?php
	require 'components/connect.php';
	session_start();
 
	if(!ISSET($_SESSION['user'])){
		header('location:index.php');
	}

	if(isset($_COOKIE['user_id'])){
		$user_id = $_COOKIE['user_id'];
	}else{
		setcookie('user_id', create_unique_id(), time() + 60*60*24*30);
	}
?>
<html lang="en">
	<head>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>
		<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1"/>
	</head>
<body>
	<?php include 'components/header.php'; ?>
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<a class="navbar-brand" href="https://sourcecodester.com">Sourcecodester</a>
		</div>
	</nav>
	<div class="col-md-3"></div>
	<div class="col-md-6 well">
		<h3 class="text-primary">PHP - PDO Login and Registration</h3>
		<hr style="border-top:1px dotted #ccc;"/>
		<div class="col-md-2"></div>
		<div class="col-md-8">
			<h3>Welcome!</h3>
			<br />
			<?php
				$id = $_SESSION['user'];
				$sql = $conn->prepare("SELECT * FROM `member` WHERE `mem_id`='$id'");
				$sql->execute();
				$fetch = $sql->fetch();
			?>
			<center><h4><?php echo $fetch['firstname']." ". $fetch['lastname']?></h4></center>
			<a href = "logout.php">Logout</a>
		</div>
	</div>
</body>
</html>