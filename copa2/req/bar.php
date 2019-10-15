<?php

if(isset($_SESSION['user'])) {
$username = $_SESSION['user'];
$result = $conn->prepare("SELECT usalt, core_username, core_avatar FROM core_users WHERE core_username='".$username."'");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);
$r = $result->fetch();
echo sprintf('<div class="profiledown"><a class="listbtn" href="updateuser?id='.$r['usalt'].'"><img class="loggedin" src="'.substr($root, 0, -3).$r['core_avatar'].'" /></a><div class="profile-content"><a class="detbtn" href="updateuser?id='.$r['usalt'].'"><i class="fa fa-user"></i>Profile</a><form method="post" ><button type="submit" value="Logout" name="logout" class="detbtn"><i class="fas fa-power-off"></i>Logout</button></form></div></div>');  
}

if (isset($_POST['logout'])){
session_unset();
session_destroy();
header("Location: ../login");
}
//var_dump(search($conn, 1));
?>

<div class="search">
<form method="post" id="search" onsubmit="return false">
<input type="text" name="srch" id="srch" placeholder="Search.." >
<button type="submit" onclick="<?php echo search($conn, 1); ?>"><i class="fa fa-search"></i></button>
</form>
</div>
<?php
//space left
$left = disk_free_space("../");
$total = disk_total_space("../");
$du = $total - $left;
//percentage of disk used - this will be used to also set the width % of the progress bar
$dp = sprintf('%.2f',($du / $total) * 100);

$left = formatSize($left);
$du = formatSize($du);
$total = formatSize($total);

function formatSize( $bytes ) {
$types = array( 'B', 'KB', 'MB', 'GB', 'TB' );
for( $i = 0; $bytes >= 1024 && $i < ( count( $types ) -1 ); $bytes /= 1024, $i++ );
return( round( $bytes, 2 ) . " " . $types[$i] );
}

?>
<div class='progress'>

<div class='bartext'><?php echo $dp; ?>% Used</div>
<div class='bar' style='width:<?php echo $dp; ?>%'></div>
<div class='info'>
</div>
</div>
<div class="logged">

</div>

<?php

$result = $conn->prepare("SELECT setting FROM core_options WHERE options = 'logo'");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);
$r = $result->fetch();
?>

<div class='logo'><img src="<?php echo(substr($root, 0, -3).$r['setting']); ?>"/></div>
