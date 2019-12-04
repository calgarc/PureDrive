<?php
define('func', TRUE);

require ('config.php');
require ('functions/functions.php');

if (isset($_POST['phpfunc'])) {
    $phpfunc = $_POST['phpfunc'];

}else if(isset($_GET['phpget'])) {
    $phpfunc = $_GET['phpget'];
}

if(1 == $phpfunc) {
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
    GetAvatar($conn, 1);
}

else if(18 == $phpfunc) {
    share($conn, 1 );
}

else if(19 == $phpfunc) {
    deleteMulti($conn, 1 );
}

else if(20 == $phpfunc) {
    pdfdisp($conn, 1);
}

else if(21 == $phpfunc) {
    emptytrash($conn, 1);
}

else if(22 == $phpfunc) {
    $dispfav = '3';
    dispgrid($conn, 1, $dispfav);
}

else if(23 == $phpfunc) {
    spaceLeft($conn, 1);
}

else if(24 == $phpfunc) {
    restoreFiles($conn, 1);
}

else if(25 == $phpfunc) {
    displatest($conn, 1);
}

else if(26 == $phpfunc) {
    trashDelete($conn, 1);
}


?>
