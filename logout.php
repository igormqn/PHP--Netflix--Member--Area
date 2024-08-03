<?php

	session_start(); // INITIALISE SESSION
	session_unset(); // DESACTIVATE LA SESSION
	session_destroy(); // DESTROY LA SESSION
	setcookie('auth', '', time()-1, '/', null, false, true); // DESTROY COOKIE

	header('location: index.php');
	exit();

?>