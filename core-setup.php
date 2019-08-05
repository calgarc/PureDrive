<!DOCTYPE html>
<head>
<meta charset="UTF-8">
<title>setup</title>
<link rel="stylesheet" type="text/css" href="req/setup.css">


</head>

<body>

<div class="main">
<img src="req/ccdark.png" />



<?php
error_reporting (E_ALL ^ E_NOTICE);
error_reporting(E_ERROR | E_PARSE);
error_reporting(E_NONE);
$filename = 'req/config.php';
$config = '<!DOCTYPE html>
<head>
<meta charset="UTF-8">
<title>CONFIG</title>

</head>

<body>

<?php
$servername = "localhost";
$username ="'.$_POST["user"].'" ;
$password = "'.$_POST["pass"].'";
$db = "'.$_POST["data"].'";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    }
catch(PDOException $e)
    {
   
    error_reporting (E_ALL ^ E_NOTICE);
    error_reporting(E_ERROR | E_PARSE);
    error_reporting(E_NONE);
    }

?> 

</body>
</html>';


// Let's make sure the file exists and is writable first.
if (isset($_POST['submit'])){
if (is_writable($filename)) {
    // In our example we're opening $filename in append mode.
    // The file pointer is at the bottom of the file hence
    // that's where $config will go when we fwrite() it.
    if (!$handle = fopen($filename, 'w')) {
         echo '<div class="connect2">Cannot open file ($filename)</div>';
         exit;
    }

    // Write $config to our opened file.
    if (fwrite($handle, $config) === FALSE) {
        echo '<div class="connect2">Cannot write to file ($filename)</div>';
        exit;
    }
    header('Location: core-setup-2');
    fclose($handle);

} else {
    echo '<div class="connect2">The file $filename is not writable</div>';
}
}
?>

<div class="form setup"> <!--form-->
<h1>SETUP</h1>

<p>Connect to database</p>

<form action="<?php echo htmlspecialchars();?>" method="post">
<input type="text" name="user" id="user" placeholder="Database username"></input>
<input type="text" name="pass" id="pass" placeholder="Datsbase password"></input>
<input type="text" name="data" id="data" placeholder="Database"></input>
<input type="submit" value="Next" name="submit" class="button"></input>

</form>
</div></div>
</body>
</html>
