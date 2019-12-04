<?php
if(!defined('func')) {
   die();
}

if (isset($_POST['logout'])){
    session_unset();
    session_destroy();
    session_regenerate_id(true);
    header("Location: ../login");
}

if(isset($_SESSION['user'])) {
    $username = $_SESSION['user'];

    $r = select($conn, "SELECT usalt, core_username, core_avatar, uplimit FROM core_users WHERE core_username = :uname", [':uname' => $username]);
    $limit = $r['uplimit'];
    $uid = $r['usalt'];

    if (IsMobile()) {
        echo ('<div class="profiledown mobilemenu profile" onclick="mobilebar()" value="'.$uid.'"><i class="fas fa-bars" /></i></div>');
    }else {
        echo('<div class="profiledown" >');
        GetAvatar($conn, 1, $root);
        echo('</div>');
    }
}

if($searchable == 'true') {
    echo '<div class="search">
        <form method="post" id="search" onsubmit="return false">
        <input type="text" name="srch" id="srch" placeholder="Search.." >
        <button type="submit" onclick="'.search($conn, 1).'"><i class="fa fa-search"></i></button>
        </form>
        </div>';
}

echo '<div class="progress">';
spaceLeft($conn);
echo '</div>';
?>

<div class="logged">
</div>

<?php
$result = $conn->prepare("SELECT setting FROM core_options WHERE options = 'logo'");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);
$r = $result->fetch();
?>

<div class='logo'><img src="<?php echo(substr($root, 0, -3).$r['setting']); ?>"/></div>
