<?php
define('func', TRUE);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require $root.'req/config.php';
require $root.'req/functions/functions.php';

$r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'token']);
tokenMatch($conn, $r['setting']);

?>
<html class="framework">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Drive</title>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
<link rel="stylesheet" href="<?php echo $root; ?>req/icons/css/font-awesome.min.css">

<?php
$r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'theme']);
$theme = $r['setting'];

if (IsMobile()) {
    echo '<link rel="stylesheet" href="'.$root.'req/css/themes/'.$theme.'/mobile.css">';
}else {
    echo '<link rel="stylesheet" href="'.$root.'req/css/themes/'.$theme.'/styles.css">';
}
?>

<script src="<?php echo $root; ?>req/js/jquery-3.4.1.min.js"></script>
<link rel="stylesheet" href="<?php echo $root; ?>req/js/jquery-ui.min.css">
<script src="<?php echo $root; ?>req/js/jquery-ui.min.js"></script>

<?php
if (IsMobile()) {
echo '<link rel="stylesheet" href="'.$root.'req/css/mobile.css">';
}
?>

<script src="https://unpkg.com/wavesurfer.js"></script>
<script src="<?php echo $root; ?>req/js/functions.js"></script>
<script src="<?php echo $root; ?>req/js/ui.js"></script>
</head>


<body oncontextmenu="return false;">

<div id="topbar">
    <?php require 'bar.php'; ?>
</div>

<?php
$admin = restrictlink( $conn, 1, $r, $admin, $username);

if (IsMobile()) {

    $result = $conn->prepare("SELECT setting FROM core_options WHERE options = 'background'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $menubg =$r['setting'];

    if($menubg != ''){
        $bg = "background:url(".$root.(substr($menubg, 3)).") no-repeat; background-size:cover !important;";
    }else{
        $bg= "background: linear-gradient( to bottom right, #f53168, #b20938, #63061e ) !important;";
    }

    $username = preg_replace("/[^a-zA-Z0-9\/_|+ .-]/", '', $_SESSION['user']);
    $result = $conn->prepare("SELECT usalt, core_username, core_avatar, core_email FROM core_users WHERE core_username = :uid");
    $result->execute([':uid' => $username]);
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $uid = $r['usalt'];

    echo '<div id="mobile" style="display:none;" ><div id="mobileinner">

        <div class="mobileupper"style="'.$bg.'">
            <a href="updateuser?id='.$uid.'" ><img class="loggedin" src="'.substr($root, 0, -3).$r['core_avatar'].'" /></a>
            <i class="fas fa-arrow-circle-left" onclick="mobilebar()"></i>
            <span>'.$username.'</span><span>'.$r['core_email'].'</span>
        </div>

        <div class="mobilenav">
            <a href="updateuser?id='.$uid.'" class="detbtn"><i class="fa fa-user"></i><span>Profile</span></a>
            <a href="'.$root.'drive/settings" class="detbtn" '.restrictlink( $conn, 1, $r, $admin, $username).'><i class="fa fa-cog"></i><span>Settings</span></a>
            <a href="'.$root.'drive/trash?id=drives" class="detbtn"><i class="fa fa-trash"></i><span>Deleted files</span></a>
        </div>

        <div class="mobilebtm">
            <form method="post" ><button type="submit" value="Logout" name="logout" class="detbtn"><i class="fas fa-power-off"></i>Logout</button></form>
            <div class="progress">
                <div class="bartext">'.$dp.'% Used</div>
                <div class="info"></div>
            </div>
        </div>

    </div></div>';

    echo '<div id="dash"><ul id="dashed">';
    plugins($conn, 1, $plugin, $root);
    echo '</ul></div>';
}
?>


<div id="tabs"> <!--tabs-->
    <ul id="tabbed">

    <?php
    $username = preg_replace("/[^a-zA-Z0-9\/_|+ .-]/", '', $_SESSION['user']);
    $result = $conn->prepare("SELECT usalt FROM core_users WHERE core_username = :uid");
    $result->execute([':uid' => $username]);
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $uid = $r['usalt'];

    if (!IsMobile()) {
        if ($admin == 'Administrator') {
            echo '<li class="'.$users.'"><a href="'.$root.'drive/updateuser?id='.$uid.'" class="nav"><i class="fa fa-users"></i></a></li>';
        }
    }
    ?>

    <li class="<?php echo $folds; ?>"><a href="<?php echo $root; ?>drive/folders?id=drives" class="nav"><i class="fa fa-folder"></i></a></li>
    <li class="<?php echo $favorites; ?>"><a href="<?php echo $root; ?>drive/favorites?id=drives" class="nav"><i class="fa fa-star"></i></a></li>

    <?php
    if (IsMobile()) {
        echo '<li id="dsh" style="display:block;"><a href="#" onclick="opendash();"><i class="fa fa-th-large"></i></a></li>';
        echo '<li id="undsh" class="active" style="display:none;"><a href="#" onclick="closedash();"><i class="fa fa-th-large"></i></a></li>';
        echo '<li class="'.$photos.'" ><a href="'.$root.'drive/photos?id=all" class="nav"><i class="fa fa-camera"></i></a></li>';

    }else {
        echo '<li class="'.$photos.'" ><a href="'.$root.'drive/photos?id=all" class="nav"><i class="fa fa-camera"></i></a></li>';

        if ($admin == 'Administrator') {
            echo '<li class="'.$settings.'" ><a href="'.$root.'drive/settings" class="nav"><i class="fa fa-cog"></i></a></li>';
            echo '<li class="'.$plugins.'" ><a href="'.$root.'drive/plugins" class="nav"><i class="fas fa-plug"></i></a></li>';
            echo '<li class="'.$trash.'" ><a href="'.$root.'drive/trash?id=drives" class="nav"><i class="fa fa-trash"></i></a></li>';
        }
    }
    ?>

    <?php
    if (IsMobile()) {
        echo '<li><form id="formed" action="#" class="up-outer" method="post" enctype="multipart/form-data"><button type="submit" name="upload" multiple="multiple" ><i class="fas fa-cloud-upload-alt"></i></button><input type="file" name="myFiles" id="myFiles" multiple=""/></form></li>';

        echo '<script>
            document.getElementById("myFiles").onchange = function() {
            $("#aniout").fadeIn( "slow" );
            document.getElementById("formed").submit();
            };
            </script>';
    }else {
        plugins($conn, 1, $plugin, $root);
    }
    ?>


    </ul>
</div> <!--tabs-->

<div id="aniout" style="display:none;">
    <div id="anicont">
        <div class="item"><img src="<?php echo $root; ?>req/css/load.png" /></div>
        <div class="circle" style="animation-delay: -3s"></div>
        <div class="circle" style="animation-delay: -2s"></div>
        <div class="circle" style="animation-delay: -1s"></div>
        <div class="circle" style="animation-delay: 0s"></div>
    </div>
</div>

    <script>
    $( document ).ajaxStart(function() {
        $( "#aniout" ).show();
    });

    $( document ).ajaxStop(function() {
        $( "#aniout" ).hide();
    });
    </script>

<div class="main"> <!--container-->
