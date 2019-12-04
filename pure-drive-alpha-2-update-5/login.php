<?php
define('func', TRUE);
define('inc', TRUE);

ini_set('session.cookie_httponly', true);

if(isset($_SESSION)) {
  session_unset();
  session_destroy();
  session_regenerate_id(true);
}

require 'req/headers.php';
require 'req/config.php';
require 'req/functions/security.inc.php';
session_regenerate_id(true);

?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="stylesheet" type="text/css" href="req/css/setup.css">
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
$background = $r['setting'];

if('' != $background){
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
        $logo = (substr($r['setting'], 3));

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

            $rest = substr($dbhash, 0, -128);
            $pass = escape($_POST['pass']);
            $password = password_verify($pass, $rest);

            if(isset($_POST['pass'])){
                if(1 == $password) {
                    $_SESSION['user'] = $user;
                }else {
                    $_SESSION['user'] = '0';
                }
            }

            $tfaform = '';

            if (isset($_POST['submit'])){

                $result = $conn->prepare("SELECT setting FROM core_options WHERE options = 'enable2fa'");
                $result->execute();
                $result->setFetchMode(PDO::FETCH_ASSOC);
                $r = $result->fetch();
                $istfa = $r['setting'];

                $result = $conn->prepare("SELECT ip, core_email FROM core_users WHERE core_username = :user");
                $result->execute([':user' => $_POST['user']]);
                $result->setFetchMode(PDO::FETCH_ASSOC);
                $r = $result->fetch();
                $email = $r['core_email'];
                $ip = $r['ip'];

                  if ('Enabled' == $istfa) {
                    if($_SESSION['user'] == $user){

                        if (isset($_POST['tfa'])){
                            if ($_POST['tfa'] == $_SESSION['tfa']) {

                                $result = $conn->prepare("UPDATE core_users SET ip = '".ip()."' WHERE core_username= :user");
                                $result->execute([':user' => $_POST['user']]);
                                $result->setFetchMode(PDO::FETCH_ASSOC);

                                $_SESSION['user'] = $user;
                                $host  = $_SERVER['HTTP_HOST'];
                                $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

                                session_regenerate_id(true);

                                header("Location: http://$host$uri/drive/folders?id=drives");
                                ob_end_flush();
                                $t = '1';
                            }
                        }

                        if ($ip != ip()) {

                            $tfa = salted();
                            $_SESSION['tfa'] = $tfa;
                            $headers  = 'MIME-Version: 1.0' . "\r\n";
                            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

                            $tfb = '<html><body>
                            <h2 style="font-family:arial; text-align:center; font-size:30px;">Authentication code</h2>
                            <h3 style="font-family:arial; color:#999999; font-size:48px; padding:5px; text-align:center;">'.$tfa.'</h3>
                            <br /> <p style="text-align:center;">If you did not try to login please ignore this message. We also reccomend you change your password as a safety precaution.</p>
                            </body></html>';

                            if ('1' != $t) {
                                mail($email, "Authentication code", $tfb, $headers);
                            }

                            $e = 'false';

                            $tfaform = '<div class="in"><i class="fa fa-key"></i><p class="p">|</p><input type="password" name="tfa" placeholder="Authentication code"></div>';
                            echo '<div class="connect2">Unrecognized device. Please enter authentication code sent to your email.</div>';

                        }else if ($r['ip'] == ip()) {
                            $_SESSION['ip'] = ip();
                            $e = 'true';
                        }
                    }

                }else if ($r['ip'] != ip()) {

                  $headers  = 'MIME-Version: 1.0' . "\r\n";
                  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

                  $tfb = '<html><body>
                  <h2 style="font-family:arial; text-align:center; font-size:30px;">New login attempt</h2>
                  <br /> <p style="text-align:center;">Did you just try to login to your account. If not we reccomend you change your password.</p>
                  </body></html>';

                  mail($email, "New login attempt", $tfb, $headers);

                  $e = 'true';

                }else {
                  $_SESSION['ip'] = ip();
                  $e = 'true';
                }

                if ('true' == $e) {
                    if($_SESSION['user'] == $user){
                    error_reporting(E_ALL | E_WARNING | E_NOTICE);
                    ini_set('display_errors', TRUE);
                    ob_start();

                    $_SESSION['token'] = token();
                    $_SESSION['timer'] = time() + 10;

                    $result = $conn->prepare("SELECT setting FROM core_options WHERE options = 'installPath'");
                    $result->execute();
                    $result->setFetchMode(PDO::FETCH_ASSOC);
                    $r = $result->fetch();
                    $uri = $r['setting'];

                    session_regenerate_id(true);

                    header("Location: $uri/drive/folders?id=drives");
                    ob_end_flush();
                    exit();

                    }else {
                    echo '<div class="connect2">Wrong username or password</div>';
                    }
                }
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

        </div> <!--form-->
    </div> <!--main-->

<?php
$conn = null;
?>

</div> <!--grad-->
</body>
</html>
