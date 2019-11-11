<?php
require 'req/headers.php';
?>

<!DOCTYPE html>
<head>
<meta charset="UTF-8">
<title>setup</title>
<link rel="stylesheet" type="text/css" href="req/setup.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
<link rel="stylesheet" href="../req/icons/css/font-awesome.min.css">
<link rel="stylesheet" href="icons/css/font-awesome.min.css">

</head>

<body >
<div class='grad' style="background: linear-gradient( to bottom right, #f53168, #b20938, #63061e ) !important;">
<div class="main">
<img src="req/cc.png" />

<div class="form setup"> <!--form-->
<h1>SETUP</h1>

<p>Create an Admin account</p>

<?php
//database connection
require 'req/config.php';
if($conn) {
echo '<div class="connect">Connection activated</div>';
}else {
 echo '<div class="connect2">Connection unsuccessful</div>';
}

//extra salts
    function salted($length = 5) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;  
}
?>

<form action="" method="post">
<div class="in"><i class="fa fa-user"></i><p class="p">|</p><input type="text" name="user" id="user" placeholder="Username" required></div>
<div class="in"><i class="fa fa-key"></i><p class="p">|</p><input type="password" name="pass" id="pass" placeholder="Password" required></div>
<div class="in"><i class="fa fa-envelope"></i><p class="p">|</p><input type="text" name="email" id="email" placeholder="email@domain.com" required></div>
<input type="submit" value="Setup" name="submit" class="button"/> 
</form>

</div> <!--form-->
</div>

<?php
function ip(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
    $ip = $_SERVER['HTTP_CLIENT_IP'];

    }else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            
    }else{
    $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

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



//create tables
if (isset($_POST['submit'])){
$result = $conn->prepare("CREATE TABLE IF NOT EXISTS core_users (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, core_username VARCHAR(60) NOT NULL, core_pass VARCHAR(255) NOT NULL, core_email VARCHAR(255) NOT NULL, salt VARCHAR(255) NOT NULL, core_firstname VARCHAR(255), core_lastname VARCHAR(255), core_avatar VARCHAR(255) NOT NULL, user_type VARCHAR(255) NOT NULL,  disp_type VARCHAR(255) NOT NULL, usalt VARCHAR(5) NOT NULL, uplimit VARCHAR(255), reg_date TIMESTAMP)");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);


$result = $conn->prepare("CREATE TABLE IF NOT EXISTS core_folders (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, file_name VARCHAR(255) NOT NULL, file_type VARCHAR(255) NOT NULL, folder_fav VARCHAR(2) NOT NULL, user_id VARCHAR(5) NOT NULL, dir_id VARCHAR(255) NOT NULL, cwd VARCHAR(255), file_size VARCHAR(255), reg_date TIMESTAMP)");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);


$result = $conn->prepare("CREATE TABLE IF NOT EXISTS core_files (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, file_name VARCHAR(255) NOT NULL, file_type VARCHAR(255) NOT NULL, file_size VARCHAR(255), folder_fav VARCHAR(255) NOT NULL, user_id VARCHAR(5) NOT NULL, dir_id VARCHAR(255) NOT NULL, cwd VARCHAR(255) , reg_date TIMESTAMP)");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);

$result = $conn->prepare("CREATE TABLE IF NOT EXISTS core_plugins (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, plugin VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, active VARCHAR(255) NOT NULL, mobile VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, icon VARCHAR(255) NOT NULL, info VARCHAR(255) NOT NULL, version VARCHAR(255) NOT NULL, reg_date TIMESTAMP)");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);

$result = $conn->prepare("CREATE TABLE IF NOT EXISTS core_options (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, options VARCHAR(255) NOT NULL, setting VARCHAR(255) NOT NULL, reg_date TIMESTAMP)");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);

$result = $conn->prepare("INSERT INTO core_options (options, setting) VALUES ('enctype', 'AES-128-CBC')");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);

$result = $conn->prepare("INSERT INTO core_options (options, setting) VALUES ('enableEncryption', 'Enabled')");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);

$result = $conn->prepare("INSERT INTO core_options (options, setting) VALUES ('theme', 'Pure v1')");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);

$result = $conn->prepare("INSERT INTO core_options (options, setting) VALUES ('directory', '../drive')");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);

$result = $conn->prepare("INSERT INTO core_options (options, setting) VALUES ('logo', '../req/cc.png')");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);

$result = $conn->prepare("INSERT INTO core_options (options, setting) VALUES ('uploadSize', '512')");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);

$result = $conn->prepare("INSERT INTO core_options (options, setting) VALUES ('lang', 'English')");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);

$result = $conn->prepare("INSERT INTO core_options (options, setting) VALUES ('icontype', 'Thumbnails')");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);

$result = $conn->prepare("INSERT INTO core_options (options, setting) VALUES ('supported', '')");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);

$result = $conn->prepare("INSERT INTO core_options (options, setting) VALUES ('search', 'no')");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);

$result = $conn->prepare("INSERT INTO core_options (options, setting) VALUES ('background', '')");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);

$result = $conn->prepare("INSERT INTO core_options (options, setting) VALUES ('dispnum', '10')");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);

$result = $conn->prepare("INSERT INTO core_options (options, setting) VALUES ('enable2fa', 'Disable')");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);
 
}

$result = $conn->prepare("SELECT usalt FROM core_users WHERE core_username = :ses");
$result->execute([':ses' => $_SESSION['user']]);
$result->setFetchMode(PDO::FETCH_ASSOC);
$r = $result->fetch();
$usalt = $r['usalt'];
    
$result = $conn->prepare("SELECT setting FROM core_options WHERE options='directory'");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);
$r = $result->fetch();

$dirloc = $r['setting']."/".$usalt;
    
mkdir($dirloc, 0755, true);


$user = $_POST['user'];
if (!preg_match("/^[a-zA-Z ]*$/",$user)) {
$fail = 1;
$errmsg = '<div class="connect2">Username may contain letters and numbers</div>';
}

$email = $_POST['email'];
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
$fail = 1;
$errmsg = '<div class="connect2">Wrong email format</div>';
}

$password = $_POST['pass'];
if (strlen($password) < 8) {
$fail = 1;
$errmsg = '<div class="connect2">Password must be at least 8 charactors long</div>';
}
if (!preg_match("#[0-9]+#", $password)) {
$fail = 1;
$errmsg = '<div class="connect2">Password must have a upercase letter and a number</div>';
}

//create user
$avatar = ('../req/css/profile.png');

if (isset($_POST['submit'])){
if ($fail == 0){
$usalt = salted();
$result = $conn->prepare("INSERT INTO core_users (usalt, core_username, core_pass, core_email, salt, core_avatar, user_type, disp_type, uplimit, ip) VALUES ('".$usalt."','".$_POST["user"]."','".$user_password."','".$_POST["email"]."','".$salted."','".$avatar."','Administrator','listview', '1', '".ip()."')");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);
// header("Location: drive/folders?id=drives");



mkdir('drive/'.$usalt, 0755, true);

$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
header("Location: http://$host$uri/drive/folders?id=drives");
}else {
echo $errmsg;
}
}

if((isset($_SESSION['user']) == $user)){
$_SESSION['user'] = $user; 
}

$conn = null;

?> 

</div>
</body>
</html>
