<!DOCTYPE html>
<head>
<meta charset="UTF-8">
<title>CONFIG</title>

</head>

<body>

<?php
$servername = "localhost";
$username ="DB USERNAME" ;
$password = "DB PASSWORD";
$db = "DB NAME";

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
</html>
