<?php
define('func', TRUE);

$root = '../';
require '../req/headers.php';

$searchable = 'false';
$settings = 'active';
require '../req/index.php';

IsSession($root);
loggedin($root);
restrict($conn, $admin, $username, 1);

//left
echo '<div id="left">';
    ui::h2($conn, 'Settings');

    echo '<ul class="side">';
      ui::sideLink($conn, 'settings', 'General', 'fas fa-sliders-h', '');
      ui::sideLink($conn, 'security', 'Security', 'fas fa-lock', '');
      ui::sideLink($conn, 'server', 'Server', 'fas fa-server', '');
    echo '</ul>';

echo '</div>';


echo '<div id="right" class="right">
  <div class="userinfo">';

    ui::h3($conn, 'Backup / Restore');

    $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'directory']);

    $directory = $r['setting'];

    $result = $conn->prepare("SELECT core_username, usalt FROM core_users");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);

    echo('<form class="form" method="post"> <label>BACKUP DRIVES</label><select class="btn locbtn" name="userbkp" id="display">');
    echo('<option value="all">All</option>');

    while ($r = $result->fetch()) {
        echo("<option value='".$r['usalt']."'>".$r['core_username']."</option>");
    }

    echo'</select>';

        ui::submitBtn($conn, 'Backup Files', 'button', 'backup');

    echo '</form>
    <form class="form" method="post">';

        ui::submitBtn($conn, 'Backup Database', 'button', 'dbkp');

        if (isset($_POST['dbkp'])) {
            $dbkp = "../".$db.'-'.date('d-m-Y').".sql";

            exec("mysqldump -u $dbusername -p $dbpassword -h $servername $db> ".$dbkp);
        }

    echo '</form>';


    ui::h3($conn, 'Install Path');

    $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'installPath']);
    echo('<form class="form" method="post"> <label>INSTALL PATH</label>');

    ui::inputSpecial($conn, $r['setting'], 'installpath', 'text', '');

    ui::submitBtn($conn, 'Update', 'button', 'update');


    ui::h3($conn, 'Server Info');

    $memlimit = substr(ini_get('memory_limit'), 0, -1);
    $memusage = substr(format(memory_get_peak_usage()), 0, -1);
    $memper = $memusage / $memlimit;
    $span = '';

    if('0.8' < $memper ) {
      echo '<p class="phpinfo2"><i class="fas fa-exclamation-triangle"></i>';
    }else {
      echo '<p class="phpinfo">';
    }

      ui::label($conn, 'MEMORY USAGE:');
      echo '<span>Memory usage is at '.sprintf('%.2f', ($memper*100)).'%</span>';
      ui::label($conn, substr(format(memory_get_peak_usage()), 0, -1));

    echo '</p>';

      if('512' > substr(ini_get('memory_limit'), 0, -1)) {
        echo '<p class="phpinfo2"><i class="fas fa-exclamation-triangle"></i>';
      }else {
        echo '<p class="phpinfo">';
      }

      ui::label($conn, 'MEMORY LIMIT:');
      echo '<span>Minimum 512M reccomended</span>';
      ui::label($conn, ini_get('memory_limit'));

    echo '</p>';

      if('7.2' > phpversion()) {
        echo '<p class="phpinfo2"><i class="fas fa-exclamation-triangle"></i>';
      }else {
        echo '<p class="phpinfo">';
      }

      ui::label($conn, 'PHP VERSION:');
      echo '<span>PHP 7.2 or later required</span>';
      ui::label($conn, phpversion());

    echo '</p><p class="phpinfo">';

      ui::label($conn, 'POST MAX SIZE:');
      ui::label($conn, ini_get('post_max_size'));

    echo '</p><p class="phpinfo">';

      ui::label($conn, 'MAX UPLOAD SIZE:');
      ui::label($conn, ini_get('upload_max_filesize'));

    echo '</p>';

      if(extension_loaded('gd')) {
        echo '<p class="phpinfo">';
        $gd = 'Enabled';

      }else {
        echo '<p class="phpinfo2"><i class="fas fa-exclamation-triangle"></i>';
        $gd = 'Dsabled';
      }

      ui::label($conn, 'GD MODULE:');
      echo '<span>Required for thumbnail support</span>';
      ui::label($conn, $gd);

    echo '</p>';

      if(extension_loaded('zip')) {
        echo '<p class="phpinfo">';
        $zip = 'Enabled';
      }else {
        echo '<p class="phpinfo2"><i class="fas fa-exclamation-triangle"></i>';
        $zip = 'Dsabled';
      }

      ui::label($conn, 'ZIP MODULE:');
      echo '<span>Required for backup and plugin support</span>';
      ui::label($conn, $zip);

    echo '</p>';

    if (isset($_POST['update'])) {

        $installpath = encrypt($conn, 1, $_POST['installpath']);
        $r = update($conn, "UPDATE core_options SET setting = :setting WHERE options = :option", [':option' => 'installPath', ':setting' => $installpath]);

        header("Location: server");
    }


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

  </div>
</div><!--right-->


<?php
$conn = null;
?>

</div><!--main-->
</body>
</html>
