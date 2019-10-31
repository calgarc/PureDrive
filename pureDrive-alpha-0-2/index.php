<?php 
require 'req/config.php';


if($conn){
}else{
ob_start();
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
header("Location: http://$host$uri/core-setup");
ob_end_flush();
exit();
}

$install  = true;
try {
$result = $conn->prepare("SELECT file_name FROM core_folders ORDER BY id DESC");
$result->execute();
}catch(Exception $e) {
$install = false;
}

if($install == true) {
ob_start();
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
header("Location: http://$host$uri/login");
ob_end_flush();
exit();
}else{
ob_start();
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
header("Location: http://$host$uri/core-setup");
ob_end_flush();
exit();
}

?>

