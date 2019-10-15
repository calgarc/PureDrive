<!DOCTYPE html>
<head>
<meta charset="UTF-8">
<title>Drive</title>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
<link rel="stylesheet" href="<?php echo $root; ?>req/icons/css/font-awesome.min.css">
<link rel="stylesheet" href="<?php echo $root; ?>req/css/styles.css">
<link rel="stylesheet" href="icons/css/font-awesome.min.css">
<link rel="stylesheet" href="css/styles.css">
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="<?php echo $root; ?>req/js/jquery-3.4.1.min.js"></script>
<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.4.0.min.js"></script>
<script src="https://unpkg.com/wavesurfer.js"></script>
<script src="<?php echo $root; ?>req/js/functions.js"></script>
</head>

<body oncontextmenu="return false;">

<?php
require 'config.php';
require 'functions.php';
?>

<div id="topbar">
<?php require 'bar.php'; ?>
</div>

<div id="tabs"> <!--tabs-->
<ul id="tabbed">
<li class="<?php echo $users; ?>" <?php restrictlink( $conn, 1, $r, $admin, $username); ?>><a href="<?php echo $root; ?>drive/users"><i class="fa fa-users"></i></a></li>
<li class="<?php echo $folds; ?>"><a href="<?php echo $root; ?>drive/folders?id=drives"><i class="fa fa-folder"></i></a></li>
<li class="<?php echo $favorites; ?>"><a href="<?php echo $root; ?>drive/favorites?id=drives"><i class="fa fa-star"></i></a></li>
<li class="<?php echo $settings; ?>" <?php restrictlink( $conn, 1, $r, $admin, $username); ?>><a href="<?php echo $root; ?>drive/settings"><i class="fa fa-cog"></i></a></li>
<li class="<?php echo $plugins; ?>" <?php restrictlink( $conn, 1, $r, $admin, $username); ?>><a href="<?php echo $root; ?>drive/plugins"><i class="fa fa-plus-square"></i></a></li>
<li class="<?php echo $calendar; ?>"><a href="<?php echo $root; ?>drive/calendar"><i class="fa fa-calendar"></i></a></li>

<?php
plugins($conn, 1, $plugin, $root);
?>


</ul>
</div> <!--tabs-->

<div class="main"> <!--container-->


