<?php
/*

Misc functions
--------------------------------------

1. Misc functions
    1.1 UserId()
    1.2 GetAvatar()
    1.3 id()
    1.4 DirLocation()
    1.5 NewCwd()
    1.6 limit()
    1.7 IsMobile()
    1.8 DirSize()
    1.9 format()
    1.10 thumbnail()
    1.11 EnableThumbs()
    1.12 DispUsers()
    1.13 Supported)
    1.14 SupportedFiles()
    1.15 zip()

2. Database functions
    2.1 select()
    2.2 update()

--------------------------------------

*/

//Required files
if(!defined('inc')) {
   die();
}


/*

1 Misc functions

*/

//get user id
function UserId(PDO $conn, $r) {
    $result = $conn->prepare("SELECT usalt FROM core_users WHERE core_username = :ses");
    $result->execute([':ses' => $_SESSION['user']]);
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();

    $usersalt = $r['usalt'];

    return $usersalt;
}

//get user avatar and profile
function GetAvatar(PDO $conn, $r) {

    $username = $_SESSION['user'];

    $r = select($conn, "SELECT usalt, core_username, core_avatar FROM core_users WHERE core_username = :uname", [':uname' => $username]);

    if(isset($_GET['nid'])) {
        $uid = encrypt($conn, 1, escape($_GET['nid']));
        $avatar = $_GET['src'];

    }else {
        $uid = $r['usalt'];
        $avatar = substr($root, 0, -3).$r['core_avatar'];
    }

    echo ('<a class="listbtn" href="updateuser?id='.$uid.'"><img class="loggedin" src="'.$avatar.'" /></a>
    <div class="profile profile-content" value="'.$uid.'">
    <a class="detbtn" href="updateuser?id='.$uid.'"><i class="fa fa-user"></i>Profile</a>
    <form method="post" ><button type="submit" value="Logout" name="logout" class="detbtn"><i class="fas fa-power-off"></i>Logout</button></form>
    </div>');
}


//current id
function id() {
    return sanitize(escape($_GET['id']));
}


//directory location
function DirLocation(PDO $conn, $r) {

    if('true' == auth($conn, 1)) {
        $usersalt = UserId($conn, 1 );

        $r = select($conn , "SELECT setting FROM core_options WHERE options= :dir", [':dir' => 'directory' ]);

        $dirlocf = $r['setting']."/";
        return $dirlocf;
    }
}


//current working directory
function NewCwd(PDO $conn, $r) {

    $r = select($conn, "SELECT file_type, dir_id, file_name FROM core_folders WHERE file_name = :id", [':id' => id()]);

    if('drives' != $r['dir_id']) {
        $getid = subsalt(id()).'/';
    }else {
        $getid = subsalt(id());
    }

    $oldcwd = $r['file_type'];

    if ('drives' == $r['dir_id']) {
        $cwd = subsalt($r['dir_id']);
    }else {
        $cwd = subsalt($r['dir_id']).'/';
    }

    $currentcwd = $r['file_name'];

    if('Directory' == $oldcwd) {
        $newcwd = subsalt(DirLocation($conn, 1).UserId($conn, 1).'/'.$cwd.$currentcwd);
    }else {
        $newcwd = DirLocation($conn, 1).UserId($conn, 1);
    }

    return $newcwd;

}


//user drive limit
function limit(PDO $conn, $r) {
    $usalt = sanitize(UserId($conn, 1));

    $r = select($conn, "SELECT uplimit FROM core_users WHERE usalt = :uid", [':uid' => $usalt] );

    $uplimit = $r['uplimit'];

    $size = DirSize(DirLocation($conn, 1).$usalt);
    $format = $uplimit * 1048576000;
    return $format;
}


//mobile
function IsMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}


//directory size
function DirSize($dir) {
    $count_size = 0;
    $count = 0;
    $dir_array = scandir($dir);

        foreach($dir_array as $key=>$filename){
            if($filename!=".." && $filename!="."){

                if(is_dir($dir."/".$filename)){
                    $new_foldersize = DirSize($dir."/".$filename);
                    $count_size = $count_size+ $new_foldersize;

                }else if(is_file($dir."/".$filename)){
                    $count_size = $count_size + filesize($dir."/".$filename);
                    $count++;
                }

            }
        }
    return $count_size;
}


//format file size
function format($bytes){
    $kb = 1024;
    $mb = $kb * 1024;
    $gb = $mb * 1024;
    $tb = $gb * 1024;

    if (($bytes >= 0) && ($bytes < $kb)) {
        return $bytes . ' B';

    }else if (($bytes >= $kb) && ($bytes < $mb)) {
        return ceil($bytes / $kb) . ' KB';

    }else if (($bytes >= $mb) && ($bytes < $gb)) {
        return ceil($bytes / $mb) . ' MB';

    }else if (($bytes >= $gb) && ($bytes < $tb)) {
        return ceil($bytes / $gb) . ' GB';

    }else if ($bytes >= $tb) {
        return ceil($bytes / $tb) . ' TB';

    } else {
        return $bytes . ' B';
    }
}


//thumbnails
function thumbnail($src, $dest, $desired_width) {

        if (exif_imagetype($src) == IMAGETYPE_GIF) {
            $source_image = imagecreatefromgif($src);
        }else if (exif_imagetype($src) == IMAGETYPE_PNG) {
            $source_image = imagecreatefrompng($src);
        }else if (exif_imagetype($src) == IMAGETYPE_JPEG) {
            $source_image = imagecreatefromjpeg($src);
        }else if (exif_imagetype($src) == IMAGETYPE_BMP) {
            $source_image = imagecreatefrombmp($src);
        }


        $width = imagesx($source_image);
        $height = imagesy($source_image);

        $desired_height = floor($height * ($desired_width / $width));

        $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);

        imagejpeg($virtual_image, $dest);
}


//enable thumbs
function EnableThumbs(PDO $conn, $r) {

    $r = select($conn , "SELECT setting FROM core_options WHERE options = :gd", [':gd' => 'gd' ]);

    $gd = $r['setting'];

    return $gd;
}


//display users
function DispUsers(PDO $conn, $r) {
    echo '<ul class="side">';
    $result = $conn->prepare("SELECT usalt, core_username FROM core_users ORDER BY id ASC");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);

    while ($r = $result->fetch()) {
        echo sprintf('<li class="dir">
        <a href="updateuser?id='.$r['usalt'].'"><i class="fa fa-user"></i>'.$r['core_username'].'</a></li>');
    }

    echo '</ul>';
}


//supported
function Supported(){
    $types = json_encode($support);
}


//supported file types
function SupportedFiles(PDO $conn, $r) {

  $r = select($conn, "SELECT setting FROM core_options WHERE options = :sup", [':sup' =>'supported']);

  $supported = $r['setting'];

  return $supported;

}


//create a zip of a directory
function zip($dlfile, $pathsource){

if (!extension_loaded('zip') || !file_exists($pathsource)) {
    return false;
}

$zip = new ZipArchive();

if (!$zip->open($dlfile, ZIPARCHIVE::CREATE)) {
    return false;
}

$pathsource = str_replace('\\', '/', realpath($pathsource));

if (is_dir($pathsource) === true) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pathsource), RecursiveIteratorIterator::SELF_FIRST);

    foreach ($files as $file){
    $file = str_replace('\\', '/', $file);

        if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )

        continue;
        $file = realpath($file);

            if (is_dir($file) === true) {
                $zip->addEmptyDir(str_replace($pathsource . '/', '', $file . '/'));
            }else if (is_file($file) === true) {
                $zip->addFromString(str_replace($pathsource . '/', '', $file), file_get_contents($file));
            }
    }

}else if (is_file($pathsource) === true) {
    $zip->addFromString(basename($pathsource), file_get_contents($pathsource));
}

return $zip->close();
}

/*

2. Database functions

*/

//selct from and update data to db
// $sql = query, $data = prepared statements
function select(PDO $conn, $sql, $data)  {

  $r = 1;

  if('true' == auth($conn, 1)) {

    $sql = escape($sql);

    $result = $conn->prepare($sql);
    $result->execute($data);
    $result->setFetchMode(PDO::FETCH_ASSOC);

    return $result->fetch();
  }
}

//update from db
function update(PDO $conn, $sql, $data)  {

  $r = 1;

  if('true' == auth($conn, 1)) {

    $sql = escape($sql);

    $result = $conn->prepare($sql);
    $result->execute($data);
    $result->setFetchMode(PDO::FETCH_ASSOC);

  }
}

?>
