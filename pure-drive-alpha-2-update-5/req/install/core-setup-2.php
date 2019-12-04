<?php
define('func', TRUE);
define('inc', TRUE);

require '../headers.php';
require '../config.php';
require '../functions/security.inc.php';
$install  = true;

if($conn){
  try {
      $result = $conn->prepare("SELECT file_name FROM core_folders ORDER BY id DESC");
      $result->execute();
  }catch(Exception $e) {
      $install = false;
  }
}else {
  $install = false;
}

if(true == $install){
  ob_start();
  $host  = $_SERVER['HTTP_HOST'];
  $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
  header("Location: ../../login");
  ob_end_flush();
  exit();
}

$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>setup</title>
    <link rel="stylesheet" type="text/css" href="../css/setup.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" href="../icons/css/font-awesome.min.css">
</head>

<body>

<div class='grad' style="background: linear-gradient( to bottom right, #f53168, #b20938, #63061e ) !important;">
    <div class="main">

    <img src="../css/cc.png" />

    <div class="form setup"> <!--form-->
        <h1>SETUP</h1>

        <p>Create an Admin account</p>

        <?php
        //database connection
        if($conn) {
          echo '<div class="connect">Connection activated</div>';
        }else {
          echo '<div class="connect2">Connection unsuccessful</div>';
        }

        $errmsg = array();

        if('7.2' > phpversion()) {
          $fail = 1;
          $errmsg[] = '<div class="connect2">PHP 7.2 or later required</div>';
        }

        $pass =  '';

        if(isset($_POST['pass'])){
            $pass = escape($_POST['pass']);
        }

        $pass = password_hash( $pass, PASSWORD_DEFAULT);
        $user_password = $pass.dataHash($pass);

        $user = sanitize(escape($_POST['user']));

        if (!preg_match("/^[a-zA-Z ]*$/",$user)) {
            $fail = 1;
            $errmsg[] = '<div class="connect2">Username may contain letters and numbers</div>';
        }

        $email = $_POST['email'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $fail = 1;
            $errmsg[] = '<div class="connect2">Wrong email format</div>';
        }

        $password = sanitize(escape($_POST['pass']));

        if (strlen($password) < 8) {
            $fail = 1;
            $errmsg[] = '<div class="connect2">Password must be at least 8 charactors long</div>';
        }

        if (!preg_match("#[0-9]+#", $password)) {
            $fail = 1;
            $errmsg[] = '<div class="connect2">Password must have a upercase letter and a number</div>';
        }

//create user
$avatar = ('../req/css/profile.png');

if (isset($_POST['submit'])){
    if (0 == $fail){

      $host  = sanitize(escape($_POST['location']));

      $result = $conn->prepare("CREATE TABLE IF NOT EXISTS core_users (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, core_username VARCHAR(60) NOT NULL, core_pass VARCHAR(255) NOT NULL, core_email VARCHAR(255) NOT NULL, core_firstname VARCHAR(255), core_lastname VARCHAR(255), core_avatar VARCHAR(255) NOT NULL, user_type VARCHAR(255) NOT NULL,  disp_type VARCHAR(255) NOT NULL, usalt VARCHAR(5) NOT NULL, uplimit VARCHAR(255), ip VARCHAR(255), reg_date TIMESTAMP)");
      $result->execute();
      $result->setFetchMode(PDO::FETCH_ASSOC);


      $result = $conn->prepare("CREATE TABLE IF NOT EXISTS core_folders (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, file_name VARCHAR(255) NOT NULL, file_type VARCHAR(255) NOT NULL, folder_fav VARCHAR(2) NOT NULL, user_id VARCHAR(5) NOT NULL, dir_id VARCHAR(255) NOT NULL, cwd VARCHAR(255), file_size INT(255), trash VARCHAR(1), reg_date TIMESTAMP)");
      $result->execute();
      $result->setFetchMode(PDO::FETCH_ASSOC);


      $result = $conn->prepare("CREATE TABLE IF NOT EXISTS core_files (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, file_name VARCHAR(255) NOT NULL, file_type VARCHAR(255) NOT NULL, file_size INT(255), folder_fav VARCHAR(255) NOT NULL, user_id VARCHAR(5) NOT NULL, dir_id VARCHAR(255) NOT NULL, cwd VARCHAR(255), trash VARCHAR(1), reg_date TIMESTAMP)");
      $result->execute();
      $result->setFetchMode(PDO::FETCH_ASSOC);

      $result = $conn->prepare("CREATE TABLE IF NOT EXISTS core_plugins (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, plugin VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, active VARCHAR(255) NOT NULL, mobile VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, icon VARCHAR(255) NOT NULL, info VARCHAR(255) NOT NULL, version VARCHAR(255) NOT NULL, reg_date TIMESTAMP)");
      $result->execute();
      $result->setFetchMode(PDO::FETCH_ASSOC);

      $result = $conn->prepare("CREATE TABLE IF NOT EXISTS core_options (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, options VARCHAR(255) NOT NULL, setting VARCHAR(255) NOT NULL, reg_date TIMESTAMP)");
      $result->execute();
      $result->setFetchMode(PDO::FETCH_ASSOC);

      $result = $conn->prepare("INSERT INTO core_options (options, setting) VALUES
         ('installPath', '$host'),
         ('enctype', 'AES-128-CBC'),
         ('enableEncryption', 'Enabled'),
         ('theme', 'Pure v1'),
         ('directory', '../drive'),
         ('logo', '../req/css/cc.png'),
         ('uploadSize', '32'),
         ('lang', 'English'),
         ('icontype', 'Thumbnails'),
         ('supported', ''),
         ('search', 'no'),
         ('background', ''),
         ('dispnum', '10'),
         ('enable2fa', 'Disable'),
         ('token', '15'),
         ('gd', 'Disable')");
      $result->execute();
      $result->setFetchMode(PDO::FETCH_ASSOC);

        $usalt = sanitize(escape(salted()));
        $result = $conn->prepare("INSERT INTO core_users (usalt, core_username, core_pass, core_email, core_avatar, user_type, disp_type, uplimit, ip) VALUES (:uid,'".$_POST["user"]."',:up,'".$_POST["email"]."', :av,'Administrator','listview', '1', '".ip()."')");
        $result->execute([':uid' => $usalt, ':up' => $user_password, ':av' => $avatar]);
        $result->setFetchMode(PDO::FETCH_ASSOC);


        mkdir('../../drive/'.dataHash($usalt).'/documents', 0755, true);

        $doc = '../documentation/pure-drive-architecture.pdf';
        $docopy = '../../drive/'.dataHash($usalt).'/pure-drive-architecture.pdf';
        copy($doc, $docopy);

        $result = $conn->prepare("INSERT INTO core_files (file_name, folder_fav, user_id, dir_id, file_type, file_size, cwd, trash) VALUES ('pure-drive-architecture.pdf-".salted()."', '0', '".$usalt."', 'drives', 'application/pdf', '5253836', '../drive/".dataHash($usalt)."', '0')");
        $result->execute();
        $result->setFetchMode(PDO::FETCH_ASSOC);

        $result = $conn->prepare("INSERT INTO core_folders (file_name, folder_fav, user_id, dir_id, file_type, file_size, cwd, trash) VALUES ('documents-".salted()."', '0', '".$usalt."', 'drives', 'Directory', '0', '../drive/".dataHash($usalt)."', '0')");
        $result->execute();
        $result->setFetchMode(PDO::FETCH_ASSOC);

        $host  = sanitize(escape($_POST['location']));
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        header("Location: http://$host/drive/folders?id=drives");
    }else {
        foreach($errmsg as $errors){
        echo $errors;
        }
    }
}

if((isset($_SESSION['user']) == $user)){
    $_SESSION['user'] = $user;
}

?>

        <form action="" method="post">
        <div class="in"><i class="fa fa-user"></i><p class="p">|</p><input type="text" name="user" id="user" placeholder="Username" required></div>
        <div class="in"><i class="fa fa-key"></i><p class="p">|</p><input type="password" name="pass" id="pass" placeholder="Password" required></div>
        <div class="in"><i class="fa fa-envelope"></i><p class="p">|</p><input type="text" name="email" id="email" placeholder="email@domain.com" required></div>

        <p>Installation path</p>
        <div class="in"><i class="far fa-hdd"></i><p class="p">|</p><input type="text" name="location" id="location" value="<?php echo $host; ?>" placeholder="Install Path" required></div>
        <input type="submit" value="Setup" name="submit" class="button"/>
        </form>

    </div> <!--form-->
</div>

<?php
  $conn = null;
?>

</div>
</body>
</html>
