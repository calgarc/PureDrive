<?php
require 'req/headers.php';
require 'req/config.php';
//echo $_SESSION['user'];
?>
<!DOCTYPE html>
<head>
<meta charset="UTF-8">
<title>Login</title>
<link rel="stylesheet" type="text/css" href="req/setup.css">


</head>

<body>

<div class="main">
<?php

$result = $conn->prepare("SELECT setting FROM core_options WHERE options = 'logo'");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);
$r = $result->fetch();

?>


<img src="<?php echo(substr($r['setting'], 3)); ?>"/>

<div class="form"> <!--form-->
<h1>LOGIN</h1>

<?php
error_reporting (E_ALL ^ E_NOTICE);
error_reporting(E_ERROR | E_PARSE);


//if (isset($_POST['submit'])){
//login
//$_SESSION['user'] = $user;
//$password = $_POST['pass'];
//}

//db password
$user = $_POST['user'];
$result = $conn->prepare("SELECT core_pass FROM core_users WHERE core_username='".$user."'");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);
$result->bindParam(':core_pass', $dbhash); 
while ($r = $result->fetch()) {
$dbhash = $r['core_pass'];
}

//salts
$result = $conn->prepare("SELECT salt FROM core_users WHERE core_username='".$user."'");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);
$result->bindParam(':salt', $salty); 
while ($r = $result->fetch()) {
$salty = $r['salt'];
}
$rest = substr($dbhash, 0, -16);
$password = $_POST['pass'];
$user_pass = $password . $salty;

//check
if($rest == hash('sha512', $user_pass)){
$_SESSION['user'] = $user;
}else {
$_SESSION['user'] = '0';
}

if (isset($_POST['submit'])){
if($_SESSION['user'] == $user){
header("Location: drive/folders?id=drives");
}else {
echo '<div class="connect2">Wrong username or password</div>';
}
}


?>

<form action="<?php echo htmlspecialchars();?>" method="post">
<input type="text" name="user" id="user" placeholder="username">
<input type="password" name="pass" id="pass" placeholder="password">
<input type="submit" value="Login" name="submit" class="button"/> 
</form>
<!--<button type="button" class="button"/>Sign Up</button> -->

</div> <!--form-->
</div>

<?php

$conn = null;

?> 

</body>
</html>
