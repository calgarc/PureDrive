<?php
require 'config.php';
require ('functions.php');

if (isset($_POST['phpfunc'])) {
$phpfunc = $_POST['phpfunc'];
}else if(isset($_GET['phpget'])) {
$phpfunc = $_GET['phpget'];
}

if($phpfunc == 0) {
share($conn, 1 );
}

if($phpfunc == 1) {
imgdisp($conn, 1);
}

if($phpfunc == 2) {
videodisp($conn, 1);
}

if($phpfunc == 3) {
displaydet($conn, 1);
}

if($phpfunc == 4) {
audiodisp($conn, 1);
}

if($phpfunc == 5) {
$dispfav = '0';
displist($conn, 1, $dispfav);
//dispgrid($conn, 1, $dispfav);
}

if($phpfunc == 6) {
$dispfav = '0';
//displist($conn, 1, $dispfav);
dispgrid($conn, 1, $dispfav);
}

if($phpfunc == 7) {
$dispfav = '2';
displist($conn, 1, $dispfav);
}

if($phpfunc == 8) {
$dispfav = '2';
dispgrid($conn, 1, $dispfav);
}

if($phpfunc == 9) {
echo dirlink($conn, 1);
}

if($phpfunc == 10) {
addfav($conn, 1);
}

if($phpfunc == 11) {
$dispfav = '2';
deletefiles($conn, 1);
displist($conn, 1, $dispfav);
}

if($phpfunc == 12) {
$dispfav = '2';
displayfiles($conn, 1, $dispfav);
}

if($phpfunc == 13) {
pagination($conn, 1);
}

if($phpfunc == 14) {
renamefiles($conn, 1);
}

if($phpfunc == 15) {
dispfavfolders($conn, 1);
}

?>
