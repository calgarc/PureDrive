<!DOCTYPE html>
<head>
<meta charset="UTF-8">
<title>setup</title>
<link rel="stylesheet" type="text/css" href="req/setup.css">


</head>

<body>

<div class="main">


<div class="form"> <!--form-->
<h1>SIGNUP</h1>

<?php
//database connection
require 'req/config.php';
?>

<form action="#" method="post">
<input type="text" name="user" id="user" placeholder="Username">
<input type="password" name="pass" id="pass" placeholder="Password">
<input type="text" name="email" id="email" placeholder="email@domain.com">
<input type="submit" value="Setup" name="submit" class="button" /> 
</form>

</div> <!--form-->
</div>

<?php

//salts
    function generateRandomPass($length = 16) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%?';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;  
}

$pass =  false;
if(isset($_POST['pass'])){
    $pass = $_POST['pass'];
 } 

$salted = generateRandomPass();
$user_password = hash('sha512', $pass . $salted ) . $salted;

//create user
$avatar = ('../req/css/profile.png');
if (isset($_POST['submit'])){
$result = $conn->prepare("INSERT INTO core_users (core_username, core_pass, core_email, salt, core_avatar) VALUES ('".$_POST["user"]."','$user_password','".$_POST["email"]."','$salted','$avatar')");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);
header("Location: login");
}

$conn = null;

?> 

</body>
</html>
