<?php
session_start();

require('src/log.php');

if(!isset($_SESSION['connect'])){
    header('location: index.php');
    exit();
}

if(!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password_two'])){

    require('src/connect.php');

    // VARIABLES
    $email          = htmlspecialchars($_POST['email']);
    $password       = htmlspecialchars($_POST['password']);
    $password_two   = htmlspecialchars($_POST['password_two']);

    // PASSWORD = PASSWORD TWO
    if($password != $password_two){

        header('location: signup.php?error=1&message=Your passwords do not match.');
        exit();

    }

    // VALID EMAIL ADDRESS
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){

        header('location: signup.php?error=1&message=Your email address is invalid.');
        exit();

    }

    // EMAIL ALREADY USED
    $req = $db->prepare("SELECT count(*) as numberEmail FROM user WHERE email = ?");
    $req->execute(array($email));

    while($email_verification = $req->fetch()){

        if($email_verification['numberEmail'] != 0){

            header('location: signup.php?error=1&message=Your email address is already used by another user.');
            exit();

        }

    }

    // HASH
    $secret = sha1($email).time();
    $secret = sha1($secret).time();

    // PASSWORD ENCRYPTION
    $password = "aq1".sha1($password."123")."25";

    // INSERT INTO DATABASE
    $req = $db->prepare("INSERT INTO user(email, password, secret) VALUES(?,?,?)");
    $req->execute(array($email, $password, $secret));

    header('location: signup.php?success=1');
    exit();

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
            <h1>Sign Up</h1>

            <?php if(isset($_GET['error'])){

                if(isset($_GET['message'])) {

                    echo'<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';

                }

            } else if(isset($_GET['success'])) {

                echo'<div class="alert success">You are now registered. <a href="index.php">Log in</a>.</div>';

            } ?>

            <form method="post" action="signup.php">
                <input type="email" name="email" placeholder="Your email address" required />
                <input type="password" name="password" placeholder="Password" required />
                <input type="password" name="password_two" placeholder="Retype your password" required />
                <button type="submit">Sign Up</button>
            </form>

            <p class="grey">Already on Netflix? <a href="index.php">Log in here</a>.</p>
        </div>
    </section>

    <?php include('src/footer.php'); ?>
</body>
</html>
