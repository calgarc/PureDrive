<?php
//Start the session
session_start();
ini_set("memory_limit","256M");

error_reporting(0);

//Discard the session
$_SESSION['timeout'] = time();

$now = time();

if (isset($_SESSION['discard_after']) && $now > $_SESSION['discard_after']) {
    session_unset();
    session_destroy();
    session_start();
}

$_SESSION['discard_after'] = $now + 43200;
?>
