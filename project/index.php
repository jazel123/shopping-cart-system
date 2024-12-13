<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>
	<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1"/>
	<style>
			body {
			background-color: lightslategray;
			font-family: Arial, sans-serif;
			display: flex;
			justify-content: center;
			align-items: center;
			height: 100vh;
		}

		.container {
			width: 700px;
			height: 500px;
			background-color: rgba(255, 255, 255, 0.7);
			padding: 20px;
			border-radius: 4px;
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
		}

		.navbar {
			background-color: #333;
			color: #fff;
			padding: 10px;
		}

		.navbar-brand {
			color: #fff;
			font-weight: bold;
		}

		.well {
			background-color: #fff;
			border: 1px solid #ccc;
			border-radius: 4px;
			padding: 20px;
		}

		h3 {
			color: #337ab7;
			text-align: center;
		}
		h4 {
			text-align: center;
		}

		hr {
			border-top: 1px dotted #ccc;
		}

		form {
			margin-top: 20px;
			height: 339px;
		}

		 .label1 {
			font-weight: bold;
			text-align: center;
			height: 60px;
			
		}
		.button{
			margin: 0;
            position: absolute;
            left: 50%;
            -ms-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
		}
		.button1 {font-size: 20px;}

		.form-control {
			border-radius: 4px;
		}

		.btn-primary {
			background-color: #337ab7;
			border-color: #2e6da4;
		}

		.btn-primary:hover {
			background-color: #286090;
			border-color: #204d74;
		}

		.btn-primary:focus,
		.btn-primary.focus {
			background-color: #286090;
			border-color: #122b40;
		}

		a {
			color: #337ab7;
		}

		a:hover {
			color: #23527c;
		}
	</style>
</head>
<body>
	<div class="container">
		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<a class="navbar-brand">A CUP OF HAPPINESS</a>
			</div>
		</nav>
		<div class="well">
			<h3 class="text-primary">Login and Registration</h3>
			<hr>
			<form action="login_query.php" method="POST">
				<h4 class="text-success">Login here...</h4>
				<hr><br>
				<div class="label1">
					<label>Username</label>
					<input type="text" class="form-control" name="username" />
				</div>
				<div class="label1">
					<label>Password</label>
					<input type="password" class="form-control" name="password" />
				</div>
				<br />
				<div class="label1">
					<button class="button1" name="login">Login</button><br><br>
					<a href="registration.php">Registration</a>
				</div>
			</form>
		</div>
	</div>
</body>
</html>
