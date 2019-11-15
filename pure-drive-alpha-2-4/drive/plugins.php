<?php
$root = '../';
require '../req/headers.php';

if(!isset($_SESSION['user'])) {
    header("Location: ../login"); //die();
}

if ($_SESSION['user'] == '0'){
    header("Location: ../login"); //die();
}
$searchable = 'true';
$plugins = 'active';
require '../req/index.php';

loggedin($root);
restrict($conn, $admin, $username, 1);
?>

<div id="left"><!--left-->

    <div class="folders">
    <h2>Plugins</h2>

    <ul class="side">
    <li class="dir"><a href="plugins">Your Plugins</a></li>
    <li class="dir"><a href="newplugin">New Plugins</a></li>
    </ul>
    </div>

</div><!--left-->


<div id="right" class="right plugins"><!--right-->

<?php 
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
    
    if(!file_exists($root."plugins/".substr($filename, 0, -4)."/meta.php")) {
        if(move_uploaded_file($source, $target_path)) {
            $zip = new ZipArchive();
            $x = $zip->open($target_path);
            
            if ($x === true) {
                $zip->extractTo($root."plugins/");
                $zip->close();

                unlink($target_path);
            }
        
            $result = $conn->prepare("SELECT plugin FROM core_plugins WHERE plugin='".substr($filename, 0, -4)."'");
            $result->execute();
            $result->setFetchMode(PDO::FETCH_ASSOC);
            $r = $result->fetch();
            
            $plugin = $r['plugin'];
            include($root."plugins/".substr($filename, 0, -4)."/meta.php");
            $url = strtolower("plugins/".substr($filename, 0, -4)."/index");
            
            //install to db
            $result = $conn->prepare("INSERT INTO core_plugins (active, author, plugin, info, icon, url, version, mobile) VALUES ('0' , '".$author."', '".$plugin."' , '".$info."' , '".$icon."' , '".$url."', '".$version."'   , '".$mobile."')");
            $result->execute();
            $result->setFetchMode(PDO::FETCH_ASSOC);
        
    
        }
    
    }
}

    $result = $conn->prepare("SELECT COUNT(*) AS allplugins FROM core_plugins");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $all = $r['allplugins'];

    $result = $conn->prepare("SELECT COUNT(*) AS activeplugins FROM core_plugins WHERE active='1'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $on = $r['activeplugins'];


    echo('<div class="activebtns" ><button type="submit" form="delete" onclick="submit();" name="deletebtn" class="listviewbtn" >All ('.$all.')</button> | ');
    echo('<button type="submit" form="delete" onclick="submit();" name="move" class="listviewbtn" >Active ('.$on.')</button></div>');

    echo( '<form id="formed" class="up-outer uplugin" method="post" enctype="multipart/form-data">
    <button type="submit" name="upload" multiple="multiple" class="btn"><i class="fa fa-plus"></i>Add Plugin</button>
    <input type="file" name="plugins" id="myFiles"/>
    <script>
    document.getElementById("myFiles").onchange = function() {
    document.getElementById("formed").submit();
    };
    </script>
    </form>' );

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

    $icon = $r['icon'];
    $url = $r['url'];
    $plugin = $r['plugin'];

    echo ('<div class="column '.$active.'" value="'.$r['plugin'].'" id="grida"><div class="information">');
    echo ('<button class="detbtn onbtn '.$activebtn.'" type="submit" name="activate" value ="'.$r['plugin'].'" >'.$activate.'</button>');
    echo ('<button class="detbtn onbtn type="submit" name="delete" value ="'.$r['plugin'].'" >Delete</button>');
    echo('<strong><span class="name">'.$r['plugin'].'</span></strong> <span class="description">'.$r['info'].'</span></div>');
    
    echo ('<div class="information meta">');
    echo('<span class="name">Version '.$r['version'].'</span> <span class="description">by '.$r['author'].'</span>');
    echo '</div></div>';
}
    
    echo '</div></form></div>';

    $result = $conn->prepare("SELECT active FROM core_plugins WHERE plugin='".$_POST['activate']."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $turnon = $r['active'];

if(isset($_POST['activate'])) {
    
    if ($turnon == '0') {
        $result = $conn->prepare( "UPDATE core_plugins SET active='1' WHERE plugin='".$_POST['activate']."' ");
        $result->execute();
        $result->setFetchMode(PDO::FETCH_ASSOC);
        header('Location: plugins');
    
    }else if ($turnon == '1') {
        $result = $conn->prepare( "UPDATE core_plugins SET active='0' WHERE plugin='".$_POST['activate']."' ");
        $result->execute();
        $result->setFetchMode(PDO::FETCH_ASSOC);
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
