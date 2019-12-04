<?php
define('func', TRUE);

$root = '../';
require '../req/headers.php';

$searchable = 'true';
$plugins = 'active';
require '../req/index.php';

IsSession($root);
loggedin($root);
restrict($conn, $admin, $username, 1);


echo '<div id="left">';
    ui::h2($conn, 'Settings');

    echo '<ul class="side">';
      ui::sideLink($conn, 'plugins', 'Your Plugins', 'fas fa-plug', '');
      ui::sideLink($conn, 'newplugin', 'New Plugins', 'fas fa-plug', '');
    echo '</ul>';

echo '</div>
      <div id="right" class="right plugins">';

if($_FILES["plugins"]["name"]) {
    $filename = $_FILES["plugins"]["name"];
    $source = $_FILES["plugins"]["tmp_name"];
    $type = $_FILES["plugins"]["type"];

    $name = explode(".", $filename);
    $accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');

    foreach($accepted_types as $mime_type) {

        if($mime_type == $type) {
            $okay = true;
            break;
        }
    }

    $continue = strtolower($name[1]) == 'zip' ? true : false;

    if(!$continue) {
    }

    $target_path = $root."plugins/".$filename;

    if(extension_loaded('zip')) {
      if(!file_exists($root."plugins/".substr($filename, 0, -4)."/meta.php")) {
          if(move_uploaded_file($source, $target_path)) {

              $zip = new ZipArchive();
              $x = $zip->open($target_path);

              if ($x === true) {
                  $zip->extractTo($root."plugins/");
                  $zip->close();

                  unlink($target_path);
              }

              $r = select($conn, "SELECT plugin FROM core_plugins WHERE plugin= :fname", [':fname' => substr($filename, 0, -4)]);

              $plugin = $r['plugin'];
              include($root."plugins/".substr($filename, 0, -4)."/meta.php");
              $url = strtolower("plugins/".substr($filename, 0, -4)."/index");

              //install to db
              $result = $conn->prepare("INSERT INTO core_plugins (active, author, plugin, info, icon, url, version, mobile) VALUES ('0' , '".$author."', '".$plugin."' , '".$info."' , '".$icon."' , '".$url."', '".$version."'   , '".$mobile."')");
              $result->execute();
              $result->setFetchMode(PDO::FETCH_ASSOC);

          }
      }
    }else {
      $errors = '<div class="errors">Zip module is not enabled on your server</div>';
    }
}

    $r= select($conn, "SELECT COUNT(*) AS :allplugins FROM core_plugins", [':allplugins' => 'allplugins']);
    $all = $r['allplugins'];

    $r= select($conn, "SELECT COUNT(*) AS :activeplugins FROM core_plugins WHERE active= :one", [':activeplugins' => 'activeplugins', ':one' => '1']);
    $on = $r['activeplugins'];


    echo('<div class="activebtns" ><button type="submit" form="delete" onclick="submit();" name="deletebtn" class="listviewbtn" >All ('.$all.')</button> | ');
    echo('<button type="submit" form="delete" onclick="submit();" name="move" class="listviewbtn" >Active ('.$on.')</button></div>');

    echo( '<form id="formed" class="up-outer uplugin" method="post" enctype="multipart/form-data">
    <button type="submit" name="upload" multiple="multiple" class="btn"><i class="fa fa-plus"></i>Add Plugin</button>
    <input type="file" onchange="uploadFiles();" name="plugins" id="myFiles"/>
    </form>' );
    print_r($errors);

    echo('<div id="column" class="row listview plug"><div class="column-top" id="grida""><span class="otherwideleft otherwideplugin"></span><span class="name">Plugin</span><span class="description">Description</span></div>');

    echo('<form id="delete"  method="post">');


    $result = $conn->prepare("SELECT active, icon, url, plugin, info, version, author FROM core_plugins");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);

while ($r = $result->fetch()) {

    if ($r['active'] == '1') {
        $active = 'ison';
        $activebtn = 'activebtn';
        $activate = 'deactivate';

    }else {
        $active='';
        $activate='activate';
        $activebtn = '';
    }

    $author = escape(encrypt($conn,1, $r['author']));
    $version = escape(encrypt($conn,1, $r['version']));
    $info = escape(encrypt($conn,1, $r['info']));
    $icon = escape(encrypt($conn,1, $r['icon']));
    $url = escape(encrypt($conn,1, $r['url']));
    $plugin = escape(encrypt($conn,1, $r['plugin']));

    echo ('<div class="column '.$active.'" value="'.$plugin.'" id="grida"><div class="information">');
    echo ('<button class="detbtn onbtn '.$activebtn.'" type="submit" name="activate" value ="'.$plugin.'" >'.$activate.'</button>');
    echo ('<button class="detbtn onbtn type="submit" name="delplug" value ="'.$plugin.'" >Delete</button>');
    echo('<strong><span class="name">'.str_replace('-', " ",$plugin).'</span></strong> <span class="description">'.$info.'</span>
          <img src="'.$root.'plugins/'.strtolower($plugin).'/'.$icon.'" width="45px" height="60px" /></div>');

    echo ('<div class="information meta">');
    echo('<span class="name">Version '.$version.'</span> <span class="description">by '.$author.'</span>');
    echo '</div></div>';
}

    echo '</div></form></div>';

    $r = select($conn, "SELECT active FROM core_plugins WHERE plugin= :activate", [':activate' => $_POST['activate']]);
    $turnon = $r['active'];

if(isset($_POST['activate'])) {

    if ($turnon == '0') {
        update($conn, "UPDATE core_plugins SET active= :one WHERE plugin= :activate", [':activate' => $_POST['activate'], ':one' => '1']);
        header('Location: plugins');

    }else if ($turnon == '1') {
        update($conn, "UPDATE core_plugins SET active= :one WHERE plugin= :activate", [':activate' => $_POST['activate'], ':one' => '0']);
        header('Location: plugins');
    }
}
?>

</div><!--right-->

<?php
$conn = null;
?>

</div><!--main-->
</body>
</html>
