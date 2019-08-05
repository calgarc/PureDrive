<!DOCTYPE html>
<head>
<title>cloudsource</title>
<meta charset="UTF-8">
</head>
<body>


<?php 
require 'req/config.php';


if($conn){
}else{
header("Location: core-setup");
}

$install  = true;
try {
$result = $conn->prepare("SELECT folder_name FROM core_folders ORDER BY id DESC");
$result->execute();
}catch(Exception $e) {
$install = false;
}

if($install == true) {
header("Location: login");
}else{
header("Location: core-setup");
}

?>


</body>
</html>

