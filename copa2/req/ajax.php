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
displist($conn, 1, $dispfav, $nid);
//dispgrid($conn, 1, $dispfav);
}

if($phpfunc == 6) {
$dispfav = '0';
//displist($conn, 1, $dispfav);
dispgrid($conn, 1, $dispfav);
}

if($phpfunc == 7) {
$dispfav = '2';
$nid = $_POST['nid'];
displist($conn, 1, $dispfav, $nid);
}

if($phpfunc == 8) {
//$dispfav = '2';
displist($conn, 1, $dispfav, $nid);
}

?>
