<?php
$root = '../';
require '../req/headers.php';

if(!isset($_SESSION['user'])) {
header("Location: ../login"); //die();
}

if ($_SESSION['user'] == '0'){
header("Location: ../login"); //die();
}
require '../req/index.php';

loggedin($root);
restrict($conn, $admin, $username, 1);
?>



<div id="left"><!--left-->

    <div class="nuser">
    <a href="newuser" class="dropbtn"><i class="fa fa-user-plus"></i>New</a>
    </div>

</div><!--left-->

<div id="right" class="right"><!--right-->
    <div class="form">
        
        <h1>New User</h1>
        <form action="#" method="post">
        <label>Username <span>(required)</span></label><input type="text" class="newuser" name="user" id="user" placeholder="Username">
        <label>Password <span>(required)</span></label><input type="password" class="newuser" name="pass" id="pass" placeholder="Password">
        <label>Email <span>(required)</span></label><input type="text" class="newuser" class="newuser" name="email" id="email" placeholder="email@domain.com">
        <label>First Name</label><input type="text" class="newuser" name="first" id="first" placeholder="first name">
        <label>Last Name</label><input type="text" class="newuser" name="last" id="last" placeholder="last name">
        <label>Send email notification</label><input type="checkbox" name="emailed" id="emailed" value="Yes"/>
        <input type="submit" value="Setup" name="submit" class="button" /> 
        </form>

    </div>
</div><!--right-->

<?php

$user = $_POST['user'];
if (!preg_match("/^[a-zA-Z ]*$/",$user)) {
$fail = 1;
$errmsg = '<div class="">Username may contain letters and numbers</div>';
}

$email = $_POST['email'];
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
$fail = 1;
$errmsg = '<div class="">Wrong email format</div>';
}

$password = $_POST['pass'];
if (strlen($password) < 8) {
$fail = 1;
$errmsg = '<div class="">Password must be at least 8 charactors long</div>';
}

if (!preg_match("#[0-9]+#", $password)) {
$fail = 1;
$errmsg = '<div class="">Password must have a upercase letter and a number</div>';
}

echo $errmsg;

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
    $result = $conn->prepare("INSERT INTO core_users (usalt, core_username, core_firstname, core_lastname, core_pass, core_email, salt, core_avatar, user_type, disp_type) VALUES ('".salted()."','".$_POST["user"]."','".$_POST["first"]."','".$_POST["last"]."','$user_password','".$_POST["email"]."','$salted','$avatar','standard', 'listview')");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    header("Location: users");
}

$conn = null;

?>

</div><!--main-->
</body>
</html>
