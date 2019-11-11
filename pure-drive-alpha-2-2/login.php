<?php
require 'req/headers.php';
require 'req/config.php';
//echo $_SESSION['user'];
$_SESSION['sorted'] = 'file_name'; 
$_SESSION['sortedf'] = 'file_name'; 
?>
<!DOCTYPE html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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

//db password
$user = $_POST['user'];
$result = $conn->prepare("SELECT core_pass FROM core_users WHERE core_username = :user");
$result->execute([':user' => $user]);
$result->setFetchMode(PDO::FETCH_ASSOC);
$result->bindParam(':core_pass', $dbhash); 

while ($r = $result->fetch()) {
$dbhash = $r['core_pass'];
}

//salts
$result = $conn->prepare("SELECT usalt, salt FROM core_users WHERE core_username = :user");
$result->execute([':user' => $user]);
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

$tfaform = '';

if (isset($_POST['submit'])){

$result = $conn->prepare("SELECT setting FROM core_options WHERE options = 'enable2fa'");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);
$r = $result->fetch();
$istfa = $r['setting'];

$result = $conn->prepare("SELECT ip, core_email FROM core_users WHERE core_username = '".$_POST['user']."'");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);
$r = $result->fetch();
$email = $r['core_email'];
    
    if ($istfa  == 'Enable') {
        if($_SESSION['user'] == $user){
        
            if (isset($_POST['tfa'])){
                if ($_POST['tfa'] == $_SESSION['tfa']) {
                $result = $conn->prepare("UPDATE core_users SET ip = '".ip()."' WHERE core_username= '".$_POST['user']."'");
                $result->execute();
                $result->setFetchMode(PDO::FETCH_ASSOC);
                
                $_SESSION['user'] = $user;
                $host  = $_SERVER['HTTP_HOST'];
                $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                header("Location: http://$host$uri/drive/folders?id=drives");
                ob_end_flush();
                $t = '1';
                }
            }
        
                if ($r['ip'] != ip()) {
                
                $tfa = salted();
                $_SESSION['tfa'] = $tfa;
                
                if ($t != '1') {
                mail($email, "Authentication code", $tfa);
                }
                $e = 'false';
                
                $tfaform = '<div class="in"><i class="fa fa-key"></i><p class="p">|</p><input type="password" name="tfa" placeholder="Authentication code"></div>';
                echo '<div class="connect2">Unrecognized device. Please enter authentication code sent to your email.</div>';
                
                }else if ($r['ip'] == ip()) {
                $e = 'true';
                }
        }
    }else {
    $e = 'true';
    }

    if ($e == 'true') {
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
}



function salted($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;  
}

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


?>

<form action="<?php echo htmlspecialchars();?>" method="post" id="loginf">

<div class="in">
<i class="fa fa-user"></i><p class="p">|</p><input type="text" name="user" id="user" placeholder="username">
</div>

<div class="in">
<i class="fa fa-key"></i><p class="p">|</p><input type="password" name="pass" id="pass" placeholder="password">
</div>

<?php
echo $tfaform;
?>

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
