<?php
define('func', TRUE);

$root = '../';
require '../req/headers.php';

$searchable = 'true';
$users = 'active';
require '../req/index.php';

IsSession($root);
loggedin($root);
restrict($conn, $admin, $username, 1);


echo '<div id="left"><!--left-->
    <div class="nuser">';

      ui::sideBtn($conn, 'newuser', 'New', 'fa fa-user-plus', 'dropbtn');

echo '</div>
      </div>';

echo '<div id="right" class="right"><!--right-->
      <div class="userinfo">';

        ui::h3($conn, 'NEW USER');

        echo '<form action="#" method="post">';

        ui::label($conn, 'USERNAME:');
        echo '<span>(required)</span>';
        ui::inputText($conn, '', 'user', 'text', 'Username');


        ui::label($conn, 'PASSWORD:');
        echo '<span>(required)</span>';
        ui::inputText($conn, '', 'pass', 'password', 'Password');


        ui::label($conn, 'EMAIL:');
        echo '<span>(required)</span>';
        ui::inputText($conn, '', 'email', 'text', 'email@domain.com');


        ui::label($conn, 'FIRST NAME:');
        ui::inputText($conn, '', 'first', 'text', 'First name');


        ui::label($conn, 'LAST NAME:');
        ui::inputText($conn, '', 'last', 'text', 'Last name');

        // <label>Send email notification</label><input type="checkbox" name="emailed" id="emailed" value="Yes"/>

        ui::submitBtn($conn, 'Setup', 'button', 'submit');

        echo '</form>';

  echo '</div>
        </div>';

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

$pass =  '';

if(isset($_POST['pass'])){
    $pass = escape($_POST['pass']);
}

$pass = password_hash( $pass, PASSWORD_DEFAULT);
$user_password = $pass.dataHash($pass);

$user = sanitize(escape($_POST['user']));

//create user
$avatar = ('../req/css/profile.png');
$email = $_POST["email"];
$fname = sanitize(escape($_POST["first"]));
$lname = sanitize(escape($_POST["last"]));
$uname = sanitize(escape($_POST["user"]));

if (isset($_POST['submit'])){
    $result = $conn->prepare("INSERT INTO core_users (usalt, core_username, core_firstname, core_lastname, core_pass, core_email, core_avatar, user_type, disp_type, uplimit) VALUES (:uid, :uname, :fname, :lname, :up, :email , :av, 'standard', 'listview', '0.5')");
    $result->execute([':uid' => salted(), ':up' => $user_password, ':av' => $avatar, ':uname' => $uname, ':lname' => $lname, ':fname' => $fname, ':email' => $email]);
    $result->setFetchMode(PDO::FETCH_ASSOC);

    mkdir('../../drive/'.dataHash($usalt), 0755, true);
    header("Location: users");
}

$conn = null;
?>

</div><!--main-->
</body>
</html>
