<?php
define('func', TRUE);
require '../config.php';
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
?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>setup</title>
    <link rel="stylesheet" type="text/css" href="../css/setup.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" href="../icons/css/font-awesome.min.css">
</head>

<body >

<div class='grad' style="background: linear-gradient( to bottom right, #f53168, #b20938, #63061e ) !important;">
    <div class="main">

        <img src="../css/cc.png" />

        <?php
        error_reporting (E_ALL ^ E_NOTICE);
        error_reporting(E_ERROR | E_PARSE);
        error_reporting(E_NONE);

        $filename = '../config.php';
        $config = '<?php
        if(!defined("func")) {
           die();
        }

        $servername = "localhost";
        $dbusername ="'.$_POST["user"].'" ;
        $dbpassword = "'.$_POST["pass"].'";
        $db = "'.$_POST["data"].'";

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$db", $dbusername, $dbpassword);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            }
        catch(PDOException $e)
            {

            error_reporting (E_ALL ^ E_NOTICE);
            error_reporting(E_ERROR | E_PARSE);
            error_reporting(E_NONE);
            }

        ?>';


        if (isset($_POST['submit'])){
            if (is_writable($filename)) {
                if (!$handle = fopen($filename, 'w')) {
                    echo '<div class="connect2">Cannot open file ($filename)</div>';
                    exit;
                }

                if (fwrite($handle, $config) === FALSE) {
                    echo '<div class="connect2">Cannot write to file ($filename)</div>';
                    exit;
                }

                $host  = $_SERVER['HTTP_HOST'];
                $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                header("Location: http://$host$uri/core-setup-2");
                fclose($handle);

            }else {
                echo '<div class="connect2">The file $filename is not writable</div>';
            }
        }
        ?>

        <div class="form setup"> <!--form-->
            <h1>SETUP</h1>

            <p>Connect to database</p>

            <form action="<?php echo htmlspecialchars();?>" method="post">
                <div class="in"><i class="fa fa-database"></i><p class="p">|</p><input type="text" name="data" id="data" placeholder="Database Name"></div>
                <div class="in"><i class="fa fa-user"></i><p class="p">|</p><input type="text" name="user" id="user" placeholder="Database username"></div>
                <div class="in"><i class="fa fa-key"></i><p class="p">|</p><input type="password" name="pass" id="pass" placeholder="Datsbase password"></div>
                <input type="submit" value="Next" name="submit" class="button">
            </form>
        </div>
    </div>

</div>
</body>
</html>
