<?php
require 'req/headers.php';
require 'req/config.php';
//echo $_SESSION['user'];
$_SESSION['sorted'] = 'folder_name'; 
$_SESSION['sortedf'] = 'file_name'; 
?>
<!DOCTYPE html>
<head>
<title>Login</title>
<link rel="stylesheet" type="text/css" href="req/setup.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
<link rel="stylesheet" href="../req/icons/css/font-awesome.min.css">
<link rel="stylesheet" href="icons/css/font-awesome.min.css">
</head>

<body>

<?php

$result = $conn->prepare("SELECT setting FROM core_options WHERE options = 'background'");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);
$r = $result->fetch();

if($r['setting'] != ''){
$bg = "background:url(".(substr($r['setting'], 3)).") no-repeat; background-size:cover !important;";
}else{
$bg= "background: linear-gradient( to bottom right, #f53168, #b20938, #63061e ) !important;";
}

?>

<div class='grad' style="<?php echo $bg; ?>">

<div class="main">
<?php

$result = $conn->prepare("SELECT setting FROM core_options WHERE options = 'logo'");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);
$r = $result->fetch();

?>


<img src="<?php echo(substr($r['setting'], 3)); ?>"/>

<div class="form"> <!--form-->

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
error_reporting(E_ALL | E_WARNING | E_NOTICE);
ini_set('display_errors', TRUE);
ob_start();
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
header("Location: http://$host$uri/drive/folders?id=drives");
ob_end_flush();
exit();
}else {
echo '<div class="connect2">Wrong username or password</div>';
}
}


?>

<form action="<?php echo htmlspecialchars();?>" method="post">

<div class="in">
<i class="fa fa-user"></i><p class="p">|</p><input type="text" name="user" id="user" placeholder="username">
</div>

<div class="in">
<i class="fa fa-key"></i><p class="p">|</p><input type="password" name="pass" id="pass" placeholder="password">
</div>

<input type="submit" value="Login" name="submit" class="button"/> 
<a href="#"> Forgot password?</a>
</form>

<!--<button type="button" class="button"/>Sign Up</button> -->

</div> <!--form-->
</div>

<?php

$conn = null;

?> 


</grad>
</body>
</html>
