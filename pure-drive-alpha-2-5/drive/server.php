<?php
define('func', TRUE);

$root = '../';
require '../req/headers.php';

$searchable = 'false';
$settings = 'active';
require '../req/index.php';

IsSession();
loggedin($root);
restrict($conn, $admin, $username, 1);
?>

<div id="left"><!--left-->
    <h2>Settings</h2>
    <ul class="side">
    <li class="dir"><a href="settings"><i class="fas fa-sliders-h"></i>General</a></li>
    <li class="dir"><a href="security"><i class="fas fa-lock"></i>Security</a></li>
    <li class="dir"><a href="server"><i class="fas fa-server"></i>Server</a></li>
    </ul>
</div><!--left-->



<div id="right" class="right"><!--right-->

 <div class="userinfo">
    <h3>Backup / Restore</h3>

    <?php
    $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'directory']);

    $directory = $r['setting'];

    $result = $conn->prepare("SELECT core_username, usalt FROM core_users");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);

    echo('<form class="form" method="post"> <label>Backup Drives</label><select class="btn locbtn" name="userbkp" id="display">');
    echo('<option value="all">All</option>');

    while ($r = $result->fetch()) {
        echo("<option value='".$r['usalt']."'>".$r['core_username']."</option>");
    }
    ?>

    </select>
    <input type="submit" value="Backup Files" name="backup" onclick="$('#aniout').show();" class="button">

    <?php

    if (isset($_POST['backup'])){
        $bkp = $_POST['userbkp'];

        if ($bkp == all) {
            $dir ='';
            $zip = 'drives.zip';

        }else {
            $dir = $_POST['userbkp'];
            $zip = $_POST['userbkp'].'.zip';
        }

        $source = $zip;
        $dlfile = encrypt($conn, 1, $source);

        $source = $directory.'/'.$dir.'/';
        $pathsource = encrypt($conn, 1, $source);

        zip($dlfile, $pathsource);
        echo '<script type="text/javascript">';
        echo 'location.href = "'.$dlfile.'"; ';
        echo '</script>';
    }
    ?>
    </form>

    <form class="form" method="post">
    <input type="submit" value="Backup Database" name="dbkp" class="button">

    <?php
    if (isset($_POST['dbkp'])) {
        $dbkp = "../".$db.'-'.date('d-m-Y').".sql";

        exec("mysqldump -u $dbusername -p $dbpassword -h $servername $db> ".$dbkp);
    }
    ?>
    </form>
</div>

</div><!--right-->


<?php
$conn = null;
?>

</div><!--main-->
</body>
</html>
