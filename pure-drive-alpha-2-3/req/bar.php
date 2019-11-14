<?php
if(isset($_SESSION['user'])) {
    $username = $_SESSION['user'];
    
    $result = $conn->prepare("SELECT usalt, core_username, core_avatar, uplimit FROM core_users WHERE core_username = :uname");
    $result->execute([':uname' => $username]);
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $uid = $r['usalt'];
     
    if (ismobile()) {
        echo ('<div class="profiledown mobilemenu profile" onclick="mobilebar()" value="'.$uid.'"><i class="fas fa-bars" /></i></div>');
    }else {
        echo('<div class="profiledown" >');
        protect($conn, 1);  
        echo('</div>');
    }
}

if (isset($_POST['logout'])){
    session_unset();
    session_destroy();
    header("Location: ../login");
}
//var_dump(search($conn, 1));

if($searchable == 'true') {
    echo '<div class="search">
        <form method="post" id="search" onsubmit="return false">
        <input type="text" name="srch" id="srch" placeholder="Search.." >
        <button type="submit" onclick="'.search($conn, 1).'"><i class="fa fa-search"></i></button>
        </form>
        </div>';
}

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


$size = dirsize(dirloc($conn, 1).$r['usalt']);
$uplimit = limit($conn, 1);
$sleft = ($size / $uplimit) * 100;
$limit = $r['uplimit'];


if($uplimit * 0.9 < $size) {
    $bar = 'bar3';
}else if ($uplimit * 0.7 < $size){
    $bar = 'bar2';
}else {
    $bar = 'bar';
}

?>

<div class='progress'>
    <div class='bartext'><?php echo format($size).' of '.$limit.' GB Used'; ?></div>
    <div class='<?php echo $bar; ?>' style='width:<?php echo $sleft; ?>%'></div>
    <div class='info'></div>
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
