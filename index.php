<?php

	session_start();

	require('src/log.php');

	if(!empty($_POST['email']) && !empty($_POST['password'])){

		require('src/connect.php');

		// VARIABLES
		$email          = htmlspecialchars($_POST['email']);
		$password       = htmlspecialchars($_POST['password']);

		// EMAIL SYNTAX CHECK
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {

			header('location: index.php?error=1&message=Your email address is invalid.');
			exit();

		}

		// ENCRYPT PASSWORD
		$password = "aq1".sha1($password."123")."25";

		// EMAIL ALREADY USED
		$req = $db->prepare("SELECT count(*) as numberEmail FROM user WHERE email = ?");
		$req->execute(array($email));

		while($email_verification = $req->fetch()){
			if($email_verification['numberEmail'] != 1){
				header('location: index.php?error=1&message=Unable to authenticate you correctly.');
				exit();
			}
		}

		// LOGIN
		$req = $db->prepare("SELECT * FROM user WHERE email = ?");
		$req->execute(array($email));

		while($user = $req->fetch()){

			if($password == $user['password']){

				$_SESSION['connect'] = 1;
				$_SESSION['email']   = $user['email'];

				if(isset($_POST['auto'])){
					setcookie('auth', $user['secret'], time() + 364*24*3600, '/', null, false, true);
				}

				header('location: index.php?success=1');
				exit();

			} else {

				header('location: index.php?error=1&message=Unable to authenticate you correctly.');
				exit();

			}

		}

	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Netflix</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/png" href="img/favicon.png">
</head>
<body>

	<?php include('src/header.php'); ?>
	
	<section>
		<div id="login-body">

				<?php if(isset($_SESSION['connect'])) { ?>

					<h1>Hello!</h1>
					<?php
					if(isset($_GET['success'])){
						echo'<div class="alert success">You are now connected.</div>';
					} ?>
					<p>What will you watch today?</p>
					<small><a href="logout.php">Logout</a></small>

				<?php } else { ?>
					<h1>Sign In</h1>

					<?php if(isset($_GET['error'])) {

						if(isset($_GET['message'])) {
							echo'<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
						}

					} ?>

					<form method="post" action="index.php">
						<input type="email" name="email" placeholder="Your email address" required />
						<input type="password" name="password" placeholder="Password" required />
						<button type="submit">Sign In</button>
						<label id="option"><input type="checkbox" name="auto" checked />Remember me</label>
					</form>
				

					<p class="grey">First time on Netflix? <a href="inscription.php">Sign Up</a>.</p>
				<?php } ?>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>
