<?php
require ('config.php');
require ('functions.php');

if (isset($_POST['phpfunc'])) {
    $phpfunc = $_POST['phpfunc'];

}else if(isset($_GET['phpget'])) {
    $phpfunc = $_GET['phpget'];
}

if($phpfunc == 0) {
    share($conn, 1 );
}

else if(1 == $phpfunc) {
    imgdisp($conn, 1);
}

else if(2 == $phpfunc) {
    videodisp($conn, 1);
}

else if(3 == $phpfunc) {
    displaydet($conn, 1);
}

else if(4 == $phpfunc) {
    audiodisp($conn, 1);
}

else if(5 == $phpfunc) {
    $dispfav = '0';
    displist($conn, 1, $dispfav);
}

else if(6 == $phpfunc) {
    $dispfav = '0';
    dispgrid($conn, 1, $dispfav);
}

else if(7 == $phpfunc) {
    $dispfav = '2';
    displist($conn, 1, $dispfav);
}

else if(8 == $phpfunc) {
    $dispfav = '2';
    dispgrid($conn, 1, $dispfav);
}

else if(9 == $phpfunc) {
    echo dirlink($conn, 1);
}

else if(10 == $phpfunc) {
    addfav($conn, 1);
}

else if(11 == $phpfunc) {
    $dispfav = '2';
    deletefiles($conn, 1);
    displist($conn, 1, $dispfav);
}

else if(12 == $phpfunc) {
    $dispfav = '2';
    displayfiles($conn, 1, $dispfav);
}

else if(13 == $phpfunc) {
    pagination($conn, 1);
}

else if(14 == $phpfunc) {
    renamefiles($conn, 1);
}

else if(15 == $phpfunc) {
    dispfavfolders($conn, 1);
}

else if(16 == $phpfunc) {
    gallery($conn, 1);
}

else if(17 == $phpfunc) {
    protect($conn, 1);
}

?>
