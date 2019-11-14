<?php
/*
Core functions for Pure Drive.

*/

error_reporting (E_ALL ^ E_NOTICE);
error_reporting(E_ERROR | E_PARSE);
$oldmask = umask(0);

//user id
function userid(PDO $conn, $r) {
    $result = $conn->prepare("SELECT usalt FROM core_users WHERE core_username='".$_SESSION['user']."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $usersalt = $r['usalt'];

    return $usersalt;
}

//current id
function id() {
    return $_GET['id'];
}

//directory location
function dirloc(PDO $conn, $r) {
    $usersalt = userid($conn, 1 );
    $result = $conn->prepare("SELECT setting FROM core_options WHERE options='directory'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    
    $dirlocf = $r['setting']."/";
    return $dirlocf;
}

//cwd
function newcwd(PDO $conn, $r) {
    $result = $conn->prepare("SELECT file_type, dir_id, file_name FROM core_folders WHERE file_name='".$_GET['id']."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();

    if($r['dir_id']  != 'drives') {
    $getid = substr(id(), 0, -6).'/';
    }else {
    $getid = substr(id(), 0, -6);
    }

    $oldcwd = $r['file_type'];
    
    if ($r['dir_id'] == 'drives') {
    $cwd = substr($r['dir_id'], 0, -6);
    }else {
    $cwd = substr($r['dir_id'], 0, -6).'/';
    }
    
    $currentcwd = $r['file_name'];

    if($oldcwd == 'Directory') {
    $newcwd = substr(dirloc($conn, 1).userid($conn, 1).'/'.$cwd.$currentcwd, 0, -6);
    }else {
    $newcwd = dirloc($conn, 1).userid($conn, 1);
    }

    return $newcwd;

}

//directory links
function dirlink(PDO $conn, $r) {

    if(isset($_GET['nid'])) {
    $id = $_GET['id'];
    $uid = $_GET['nid'];
    }else {
    $id = $_GET['id'];
    }
    
    $result = $conn->prepare("SELECT disp_type FROM core_users WHERE usalt='".$uid."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $grid = $r['disp_type'];
    
    if (isset($_POST['grid'])) {
    $grid = 'gridview';
    }
    
    if (isset($_POST['list'])) {
    $grid = 'listview';
    }

    if ($grid == 'listview') {
    $links = 'dispfiles();';
    $return = 'listhier()';
    }else if($grid == 'gridview') {
    $links = 'dispgridfiles();';
    $return = 'gridhier()';
    }
    

    $result = $conn->prepare("SELECT file_type, dir_id, file_name FROM core_folders WHERE file_name='".$id."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();

    if($r['dir_id']  != 'drives') {
    $getid = substr(id(), 0, -6).'/';
    }else {
    $getid = substr(id(), 0, -6);
    }

    $oldcwd = $r['file_type'];
    $cwd = substr($r['dir_id'], 0, -6);
    $currentcwd = $r['file_name'];

    if ($cwd != '') {
    $prev = '<span class="dirlink"><a value ="'.$r['dir_id'].'" onmouseover="'.$links.'" class="listlinks"><i class="fas fa-folder"></i>'.$cwd.'</a></span><span class="dirbtn"><i class="fas fa-chevron-right"></i></span>';
    }

    if($oldcwd == 'Directory') {
    $newcwd = substr(dirloc($conn, 1).$uid.'/'.$cwd.'/'.$currentcwd, 0, -6);
    $linkname ='<span class="dirlink"><a onmouseover="'.$return.'" class="returnbtn"><i class="fas fa-home"></i>Root</a></span><span class="dirbtn"><i class="fas fa-chevron-right"></i></span>'.$prev.'<span class="dirlink"><a value="'.$id.'" onmouseover="'.$links.'" class="listlinks"><i class="fas fa-folder"></i>'.substr($currentcwd, 0, -6).'</a></span>';
    
    }else {
    $newcwd = dirloc($conn, 1).$uid;
    $linkname = '<span class="dirlink"><a onmouseover="'.$return.'" class="returnbtn"><i class="fas fa-home"></i>Root</a></span>';
    }

    return $linkname;
}

//upload files    
    $usalt = userid($conn, 1);

    $result = $conn->prepare("SELECT setting FROM core_options WHERE options='directory'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();

    $dirlocf = $r['setting']."/".$usalt;
    $current = substr($_GET['id'], 0, -6);

    $result = $conn->prepare("SELECT setting FROM core_options WHERE options='uploadSize'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $maxsize = $r['setting'].'000000';

    if(isset($_FILES['myFiles'])){
        $errors= array();
            foreach($_FILES['myFiles']['tmp_name'] as $key => $tmp_name ){
                $file_name = $_FILES['myFiles']['name'][$key];
                $file_size =$_FILES['myFiles']['size'][$key];
                $file_tmp =$_FILES['myFiles']['tmp_name'][$key];
                $file_type=$_FILES['myFiles']['type'][$key];
                $file_ext=strtolower(end(explode('.',$_FILES['myFiles']['name'])));
                
                if($file_size > $maxsize){
                $errors= '<div class="errors">'.format($maxsize).' Max file size </div>';
                }

                $supported = array("jpeg","jpg","png");
                $result = $conn->prepare("SELECT setting FROM core_options WHERE options = 'supported'");
                $result->execute();
                $result->setFetchMode(PDO::FETCH_ASSOC);
                $r = $result->fetch();
                $supported = array_merge($supported, array_map('trim', explode(",", $r['setting'])));
                    
                if($r['setting'] != '') {
                    if(in_array($file_ext,$supported)=== false){
                    $errors= '<div class="errors">File type not supported </div>';
                    }
                }
                
                require('filetypes.php');
                    
                if(empty($errors)==true){

                $file_name = str_replace(" ","_",strtolower($file_name));
                $file_name = preg_replace("/[^a-zA-Z0-9\/_|+ .-]/", '', $file_name);

                move_uploaded_file($file_tmp, newcwd($conn, 1)."/".$file_name);


                $result = $conn->prepare("INSERT INTO core_files (file_name, folder_fav, user_id, dir_id, file_type, file_size, cwd) VALUES ('".$file_name.'-'.salted()."','0','".$usalt."','".$_GET['id']."','".$file_type."','".$file_size."', '".newcwd($conn, 1)."')");
                $result->execute();
                $result->setFetchMode(PDO::FETCH_ASSOC);
                header('Location: ?id='.$_GET['id'].'');
                }else{
                //print_r($errors);
                }
            }   
    }

//make directory
//function makedir(PDO $conn, $r) {
    $usalt = userid($conn, 1);

    $result = $conn->prepare("SELECT setting FROM core_options WHERE options='directory'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();

    $dirloc = $r['setting']."/".$usalt;

    if($_GET['id'] == 'drives') {
    $source = str_replace(" ","_",strtolower($_POST['folder']));
    $foldername = encrypt($conn, 1, $source);
    
    $source = preg_replace("/[^a-zA-Z0-9\/_|+ .-]/", '', $foldername);
    $foldername = encrypt($conn, 1, $source);
    
    }else {
    $source = substr($_GET['id'], 0, -6).'/'.str_replace(" ","_",strtolower($_POST['folder']));
    $foldername = encrypt($conn, 1, $source);
    
    $source = preg_replace("/[^a-zA-Z0-9\/_|+ .-]/", '', $foldername);
    $foldername = encrypt($conn, 1, $source);
    }

    $result = $conn->prepare("SELECT dir_id, file_name FROM core_folders WHERE file_name='".$_GET['id']."' ");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();

    if ($r['dir_id'] != 'drives') {
    $active = $dirloc.'/'.substr($r['dir_id'], 0, -6)."/".$foldername;
    }else {
    $active = $dirloc."/".$foldername;
    }

    if (isset($_POST['create'])){
        if(is_dir($foldername)) {
        }else {
        mkdir($active, 0755, true);
        }
    }

    $source = str_replace(" ","_",strtolower($_POST['folder']));
    $folderid = encrypt($conn, 1, $source);
    
    $source = preg_replace("/[^a-zA-Z0-9\/_|+ .-]/", '', $folderid);
    $folderid = encrypt($conn, 1, $source);
    
    $source = substr($_GET['id'], 0, -6);
    $folders = encrypt($conn, 1, $source);

    if (isset($_POST['create'])){
//     if ($folders == substr($r['file_name'], 0, -6)) {
//     //exists
//     }else{
    $result = $conn->prepare( "INSERT INTO core_folders (file_name, folder_fav, user_id, dir_id, file_type, cwd) VALUES ('".$folderid.'-'.salted()."','0','".$usalt."','".$_GET['id']."', 'Directory', '".newcwd($conn, 1)."')");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
//     header('Location: ?id='.$_GET['id'].'');
//     }
    }

//     $main = 'drives';
//     if (isset($_POST['create'])){
//     if ($_GET['id'] == $main) {
//     $result = $conn->prepare( "INSERT INTO core_folders (file_name, folder_fav, user_id, dir_id, file_type, cwd) VALUES ('".$folderid.'-'.salted()."','0','".$usalt." ','".$main."','Directory', '".newcwd($conn, 1)."')");
//     $result->execute();
//     $result->setFetchMode(PDO::FETCH_ASSOC);
// //     header('Location: ?id='.$main.'');
//     }
//     }
//}

//active folder
function active(PDO $conn, $r) {
    $result = $conn->prepare("SELECT dir_id, file_name FROM core_folders WHERE file_name='".$_GET['id']."' ");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();

    if ($r['dir_id'] != 'drives') {
    $active = dirloc($conn, 1).userid($conn, 1 );
    }else {
    $active = substr(dirloc($conn, 1).userid($conn, 1 ).'/'.$getid.$r['file_name'], 0, -6);
    }
    
    return $active;

}

//file types
function filetypes($filetype, $icons) {

    if($filetype == 'image/jpg') {
    $icons = 'fa fa-image';
    }elseif($filetype == 'image/jpeg') {
    $icons = 'fa fa-image';
    }elseif($filetype == 'image/png') {
    $icons = 'fa fa-image';
    }elseif($filetype == 'image/gif') {
    $icons = 'fa fa-image';
    }elseif($filetype == 'image/ico') {
    $icons = 'fa fa-image';
    }elseif($filetype == 'image/bmp') {
    $icons = 'fa fa-image';
    }elseif($filetype == 'image/svg+xml') {
    $icons = 'fa fa-image';
    }elseif($filetype == 'Directory') {
    $icons = 'fa fa-folder';
    }elseif ($filetype == 'text') {
    $icons = 'fa fa-file';
    }elseif ($filetype == 'text/odt') {
    $icons = 'fa fa-file';
    }elseif ($filetype == 'archive') {
    $icons = 'fa fa-archive';
    }elseif ($filetype == 'application/pdf') {
    $icons = 'fas fa-file-pdf';
    }elseif ($filetype == 'application/zip') {
    $icons = 'fas fa-archive';
    }elseif ($filetype == 'word') {
    $icons = 'fas fa-file-word';
    }elseif ($filetype == 'excel') {
    $icons = 'fas fa-file-excel';
    }elseif ($filetype == 'other') {
    $icons = 'fas fa-file';
    }elseif ($filetype == 'video/mp4') {
    $icons = 'fas fa-video';
    }elseif ($filetype == 'video/ogg') {
    $icons = 'fas fa-video';
    }elseif ($filetype == 'video/webm') {
    $icons = 'fas fa-video';
    }elseif ($filetype == 'audio/mpeg') {
    $icons = 'fas fa-music';
    }elseif ($filetype == 'audio/ogg') {
    $icons = 'fas fa-music';
    }elseif ($filetype == 'audio/x-wav') {
    $icons = 'fas fa-music';
    }

    return $icons;

}

//display folders
function dispfolders(PDO $conn, $r) {
    $usalt = userid($conn, 1);
    
    $result = $conn->prepare("SELECT disp_type FROM core_users WHERE usalt='".userid($conn, 1)."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $grid = $r['disp_type'];
    
    if (isset($_POST['grid'])) {
    $grid = 'gridview';
    }
    
    if (isset($_POST['list'])) {
    $grid = 'listview';
    }

    if ($grid == 'listview') {
    $links = 'dispfiles();';
    }else if($grid == 'gridview') {
    $links = 'dispgridfiles();';
    }
    
    echo '<ul class="side">';
    
    $result = $conn->prepare("SELECT file_name FROM core_folders WHERE user_id='".$usalt."' AND dir_id NOT LIKE 'trash%' ORDER BY id DESC");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
        
        while ($r = $result->fetch()) {
        $folderdisp = str_replace("_"," ",strtolower(substr($r['file_name'], 0, -6)));
        echo sprintf('<li class="dir">
        <a value="'.$r['file_name'].'" class="listlinks" onmouseover="'.$links.'"><i id="sidi"class="fa fa-folder" aria-hidden="true"></i>'.$folderdisp.'</a></li>');
        }
        
    echo '</ul>';
}


//display favorite folders
function dispfavfolders(PDO $conn, $r) {
    $usalt = userid($conn, 1);
    
    $result = $conn->prepare("SELECT disp_type FROM core_users WHERE usalt='".userid($conn, 1)."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $grid = $r['disp_type'];
    
    if (isset($_POST['grid'])) {
    $grid = 'gridview';
    }
    
    if (isset($_POST['list'])) {
    $grid = 'listview';
    }

    if ($grid == 'listview') {
    $links = 'dispfiles();';
    }else if($grid == 'gridview') {
    $links = 'dispgridfiles();';
    }

    echo '<ul class="side">';
    
    $result = $conn->prepare("SELECT file_name FROM core_folders WHERE folder_fav='1' AND user_id='".$usalt."' AND dir_id NOT LIKE 'trash%' ORDER BY id DESC");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    
        while ($r = $result->fetch()) {
        $folderdisp = str_replace("_"," ",strtolower(substr($r['file_name'], 0, -6)));
        echo sprintf('<li class="dir">
        <a value="'.$r['file_name'].'" class="listlinks" onmouseover="'.$links.'"><i id="sidi"class="fa fa-folder" aria-hidden="true"></i>'.$folderdisp.'</a></li>');
        }
    
    echo '</ul>';
}

//display recent files
function recentfiles(PDO $conn, $r) {
    $usalt = userid($conn, 1);

    echo '<ul class="side">';
    $result = $conn->prepare("SELECT file_name, file_type FROM core_files WHERE user_id='".$usalt."' ORDER BY reg_date DESC LIMIT 5");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
       
        while ($r = $result->fetch()) {

        $filetype = $r['file_type'];

            if($r['file_type'] == 'image/png'){
            $dirload = "popupc(this);";
            }else if($r['file_type'] == 'image/gif'){
            $dirload = "popupc(this);";
            }else if($r['file_type'] == 'image/ico'){
            $dirload = "popupc(this);";
            }else if($r['file_type'] == 'image/svg+xml'){
            $dirload = "popupc(this);";
            }else if($r['file_type'] == 'image/bmp'){
            $dirload = "popupc(this);";
            }else if($r['file_type'] == 'image/jpg'){
            $dirload = "popupc(this);";
            }else if($r['file_type'] == 'image/jpeg'){
            $dirload = "popupc(this);";
            }else if($r['file_type'] == 'video/mp4'){
            $dirload = "popupv(this);";
            }else if($r['file_type'] == 'video/ogg'){
            $dirload = "popupv(this);";
            }else if($r['file_type'] == 'video/webm'){
            $dirload = "popupv(this);";
            }else if($r['file_type'] == 'audio/mpeg'){
            $dirload = "popupa(this);";
            }else if($r['file_type'] == 'audio/ogg'){
            $dirload = "popupa(this);";
            }else if($r['file_type'] == 'audio/x-wav'){
            $dirload = "popupa(this);";
            }else {
            $dirload = "";
            }

        $folderdisp = str_replace("_"," ",strtolower(substr($r['file_name'], 0, -6)));
        echo sprintf('<li class="dir">
        <a value="'.$r['file_name'].'" onclick="'.$dirload.'"><i id="sidi" class="'.filetypes($filetype, $icons).'" aria-hidden="true"></i>'.$folderdisp.'</a></li>');
        }
        
    echo '</ul>';
}


//plugins
function plugins(PDO $conn, $r, $plugin, $root) {

    $result = $conn->prepare("SELECT active, icon, url, plugin, mobile FROM core_plugins");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
        
        while ($r = $result->fetch()) {

        $active = $r['active'];
        $icon = $r['icon'];
        $url = $r['url'];
        $link = '<i><img src="'.$root.'plugins/'.$r['plugin'].'/'.$icon.'" width="45px" height="60px" /></i>';
    
                if ($active == '1') {
                    if ($icon != '') {
                    $icon = $r['icon'];
                    }else {
                    $icon = '<i class="fas fa-plug"></i>';
                    }
                    
                    if (ismobile()) {
                        if ($r['mobile'] == 'true') {
                        echo '<li class="'.$plugin.'"><a href="'.$root.strtolower($url).'">'.strtolower($link).'</a></li>';
                        }
                    }else {
                    echo '<li class="'.$plugin.'"><a href="'.$root.strtolower($url).'">'.strtolower($link).'</a></li>';
                    }
                    
                }else if ($active == '0'){
                    echo '';
                }
        }

}

//mobile
function ismobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

//search
function search(PDO $conn, $r) {

    $result = $conn->prepare("SELECT disp_type FROM core_users WHERE usalt='".userid($conn, 1)."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $grid = $r['disp_type'];
    
        if (isset($_POST['grid'])) {
        $grid = 'gridview';
        }
        
        if (isset($_POST['list'])) {
        $grid = 'listview';
        }

        if ($grid == 'listview') {
            if (ismobile()) {
            $view = 'searchg();';
            }else {
            $view = 'searchl();';
            }
        }else if ($grid == 'gridview') {
        $view = 'searchg();';
        }

    return $view;
}

//display files
function displayfiles(PDO $conn, $r, $dispfav) {

    if(isset($_POST['nid'])) {
    $uid = $_POST['nid'];
    
    }else {
    $uid = userid($conn, 1);   
    }

    $result = $conn->prepare("SELECT disp_type FROM core_users WHERE usalt='".$uid."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $grid = $r['disp_type'];


        if (isset($_POST['disp'])) {
        $grid = 'gridview';
        }
        
        if (isset($_POST['disp'])) {
        $grid = 'listview';
        }

    $result = $conn->prepare( "UPDATE core_users SET disp_type='$grid' WHERE usalt='".$uid."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    
        if (ismobile()) {
        
        echo '</div>';
        echo '<div class="files">';
        dispgrid($conn, 1, $dispfav);
        echo '</div>';
        pagination($conn, 1);
        
        }else if($dispfav == '3'){
        
            echo '</div>';
            echo '<div class="files">';
            dispgrid($conn, 1, $dispfav);
            echo '</div>';
            pagination($conn, 1);
        
        }else {
        
            if($grid == 'gridview') {
            
            echo '</div>';
            echo '<div class="files">';
            dispgrid($conn, 1, $dispfav);
            echo '</div>';
            pagination($conn, 1);
            
            }else if ($grid == 'listview') {
            
            listoptions($conn, 1, $dispfav);
            echo '<div class="files">';
            displist($conn, 1, $dispfav);
            echo '</div>';
            pagination($conn, 1);
            
            }
        }
        
        if ($grid == 'listview') {
        $view = 'searchl();';
        }else if ($grid == 'gridview') {
        $view = 'searchg();';
        }
        
}


//directory size
function dirsize($dir) {
    $count_size = 0;
    $count = 0;
    $dir_array = scandir($dir);
    
        foreach($dir_array as $key=>$filename){
            if($filename!=".." && $filename!="."){
            
                if(is_dir($dir."/".$filename)){
                $new_foldersize = dirsize($dir."/".$filename);
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

//get file icons
function geticon(PDO $conn, $r) {
    $result = $conn->prepare("(SELECT file_type FROM core_files) UNION (SELECT file_type FROM core_folders)");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
}

//parent directory
function parentdir(PDO $conn, $r, $par, $id, $disptype) {
    $cwd = newcwd($conn, 1);

    $result = $conn->prepare("SELECT dir_id, file_type FROM core_folders WHERE file_name='".$id."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();

    
    if (ismobile()) {
    $dirload = 'onmouseover="loadmobilegrid();"';
    
    }else if ($disptype == 'list') {
    $dirload = 'onmouseover="loaddir();"';
    $dirreturn = 'onmouseover="returnlist();"';
    
    }else if ($disptype == 'grid') {
    $dirload = 'onmouseover="loadgrid();"';
    $dirreturn = 'onmouseover="returngrid();"';
    }

    if(is_dir(substr($cwd.'/'.$r['file_name'], 0, -6))) {
    
        if($id != 'drives') {
        echo sprintf('<div class="gridwrap"><div class="columnd column" id="grida" value="'.$r['dir_id'].'" '.$dirload.'><i class="fa fa-level-up" aria-hidden="true"></i> <span class="name">Parent directory</span></div></div>');
        }
        
    }else if($id != 'drives') {
    
        if($par == '0') {
        echo sprintf('<div class="gridwrap"><div class="columnd column" id="grida" value="'.$r['dir_id'].'" '.$dirload.'><i class="fa fa-level-up" aria-hidden="true"></i> <span class="name">Parent directory</span></div></div>');
        }
        
    }

    if($r['dir_id'] == '') {
       
       if($_GET['id'] != 'drives') {
        echo sprintf('<div class="gridwrap"><div class="columnd column" id="grida" '.$dirreturn.'><i class="fas fa-angle-left" aria-hidden="true"></i> <span class="name">Return</span></div></div>');
        }
        
    }

}


//photo gallery
function gallery(PDO $conn, $r) {
    $cwd = newcwd($conn, 1);
    $disp = dispnum($conn, 1);

    $id = $_GET['id'];
    $search = $_POST['query'];

    $uid = userid($conn, 1);

    echo '<div id="column" class="row gallery">';

    $dispfav = '2';
    if($dispfav == '2') {
    $result = $conn->prepare("SELECT file_name, file_type, cwd FROM core_files WHERE file_type LIKE '%image%' AND user_id='".$uid."' ORDER BY reg_date DESC ");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    }

    while ($r = $result->fetch()) {

        $directory = $r['cwd'];
        $filetype = $r['file_type'];

        $getid = substr($_GET['id'], 0, -6).'/';

        if($r['file_type'] == 'image/png'){
        $size = $filesize;
        $dirload = "popupc(this);";
       
        }else if($r['file_type'] == 'image/gif'){
        $size = $filesize;
        $dirload = "popupc(this);";
       
        }else if($r['file_type'] == 'image/ico'){
        $size = $filesize;
        $dirload = "popupc(this);";
        
        }else if($r['file_type'] == 'image/svg+xml'){
        $size = $filesize;
        $dirload = "popupc(this);";
        
        }else if($r['file_type'] == 'image/bmp'){
        $size = $filesize;
        $dirload = "popupc(this);";
       
        }else if($r['file_type'] == 'image/jpg'){
        $size = $filesize;
        $dirload = "popupc(this);";
       
        }else if($r['file_type'] == 'image/jpeg') {
        $size = $filesize;
        $dirload = "popupc(this);";
        }

        $source = substr($_GET['id'], 0, -6);
        $current = encrypt($conn, 1, $source);

        $source = substr($directory.'/'.$r['file_name'], 0, -6);
        $img = encrypt($conn, 1, $source);

        $thumbs = thumbs($conn, 1, $thumbtype);

        if ($thumbs == 'Icons') {
        $thumbnails = '<i class="'.filetypes($filetype, $icons).'" aria-hidden="true"></i>';
        }else if($thumbs == 'Thumbnails') {

            if($r['file_type'] == 'image/png') {
            $thumbnails = '<img src="'.$img.'"  style="display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/jpg') {
            $thumbnails = '<img src="'.$img.'"  style="display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/jpeg') {
            $thumbnails = '<img src="'.$img.'"  style="display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/gif') {
            $thumbnails = '<img src="'.$img.'"  style="display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/svg+xml') {
            $thumbnails = '<img src="'.$img.'"  style="display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/ico') {
            $thumbnails = '<img src="'.$img.'"  style="display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/bmp') {
            $thumbnails = '<img src="'.$img.'"  style="display:block; margin:auto;" />';
            }else{
            $thumbnails = '<i class="'.filetypes($filetype, $icons).'" aria-hidden="true"></i>';
            }
            
        }

        $folderdisp = str_replace("_"," ",strtolower(substr($r['file_name'], 0, -6)));
        
        if (strlen($folderdisp) > 49){
        $maxLength = 45;
        $folderdisp = substr($folderdisp, 0, $maxLength).'...';
        }

        echo sprintf('<div class="column" value="'.$r['file_name'].'" id="grida"" onclick="'.$dirload.'">'.$thumbnails.'</div>');
    }
    
    echo '  </div>';
}


//display grid
function dispgrid(PDO $conn, $r, $dispfav) {
    $cwd = newcwd($conn, 1);
    $disp = dispnum($conn, 1);

    $search = $_POST['query'];

    if($search != '') {
    $par = '1';
    }else {
    $par = '0';
    }
    
    if (isset($_GET['sorted'])) {
    //$_SESSION['sorted'] = $_GET['sorted'];
    $sorted = $_GET['sorted'];
    }else {
    $sorted = $_SESSION['sorted'];
    }
    
    if(isset($_GET['nid'])) {
    $id = $_GET['id'];
    $uid = $_GET['nid'];
    
    }else {
    $id = $_GET['id'];
    $uid = userid($conn, 1);   
    }

    if(isset($_POST['nid'])) {
        if($dispfav == '0') {
        $uid = $_POST['nid'];
        }
    }
    
    if ($dispfav == '3') {
    $id = 'drives';
    }
    
    $disptype = 'grid';
    echo '<div id="column" class="row gridview">';
    echo parentdir($conn, 1, $par, $id, $disptype);

    $sorted = 'file_name';
    $sortedf = 'file_name';

    if(isset($_POST['sortby'])) {
    $sorted = $_POST['sortby'];
    $_SESSION['sorted'] = $_POST['sortby'];         
    }


    $start = '0';

    if(isset($_POST['next'])) {
    $start = $_POST['next'];
    }

    if(isset($_POST['prev'])) {
    $start = $_POST['prev'] ;
    }


    if($dispfav == '2') { //for all
    $result = $conn->prepare("SELECT file_name, file_type, cwd FROM core_folders WHERE file_name LIKE '%".$search."%' AND user_id='".$uid."' AND dir_id='".$id."' UNION ALL SELECT file_name, file_type, cwd FROM core_files WHERE file_name LIKE '%".$search."%' AND user_id='".$uid."' AND dir_id='".$id."' ORDER BY ".$sorted." LIMIT ".$start." , ".$disp."");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    
    }else if($dispfav == '1') { //for favorites
    $result = $conn->prepare("SELECT file_name, file_type, cwd FROM core_folders WHERE file_name LIKE '%".$search."%' AND user_id='".$uid."' AND dir_id='".$id."' AND folder_fav='1' UNION ALL SELECT file_name, file_type, cwd FROM core_files WHERE file_name LIKE '%".$search."%' AND user_id='".$uid."' AND dir_id='".$id."' AND folder_fav='1' ORDER BY ".$sorted." LIMIT ".$start." , ".$disp."");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    
    }else if($dispfav == '0') { //for search
    $result = $conn->prepare("(SELECT file_name, file_type, cwd FROM core_folders WHERE file_name LIKE '%".$search."%' AND user_id='".$uid."' LIMIT ".$start." , ".$disp.") UNION (SELECT file_name, file_type, cwd FROM core_files WHERE file_name LIKE '%".$search."%' AND user_id='".$uid."' LIMIT ".$start." , ".$disp.")");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    
    }else if($dispfav == '3') { //for trash
    $result = $conn->prepare("(SELECT file_name, file_type, cwd FROM core_folders WHERE file_name LIKE '%".$search."%' AND user_id='".$uid."' AND dir_id='trash' LIMIT ".$start." , ".$disp.") UNION (SELECT file_name, file_type, cwd FROM core_files WHERE file_name LIKE '%".$search."%' AND user_id='".$uid."' AND dir_id='trash' LIMIT ".$start." , ".$disp.")");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    }

    if (ismobile()) {
    $click = 'onclick=';
    }else {
    $click = 'ondblclick=';
    }

    while ($r = $result->fetch()) {

        $directory = $r['cwd'];
        $filetype = $r['file_type'];

        $getid = substr($_GET['id'], 0, -6).'/';
        
        if(is_dir(substr($directory.'/'.$r['file_name'], 0, -6))) {
        $size = $foldersize;
        
            if (ismobile()) {
            $dirload = 'onmouseover="loadmobilegrid();"';
            $click = 'onclick=';
            }else {
            $dirload = 'onmouseover="loadgrid();"';
            $click = 'ondblclick=';
            }
        
        $column = 'columng';
        $newcwd = substr(dirloc($conn, 1).userid($conn, 1 ).'/'.$r['file_name'].'/'.$_GET['id'], 0, -6);
        
        }else if($r['file_type'] == 'image/png'){
        $size = $filesize;
        $dirload = $click.'"popupc(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'image/gif'){
        $size = $filesize;
        $dirload = $click.'"popupc(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'image/ico'){
        $size = $filesize;
        $dirload = $click.'"popupc(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'image/svg+xml'){
        $size = $filesize;
        $dirload = $click.'"popupc(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'image/bmp'){
        $size = $filesize;
        $dirload = $click.'"popupc(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'image/jpg'){
        $size = $filesize;
        $dirload = $click.'"popupc(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'image/jpeg'){
        $size = $filesize;
        $dirload = $click.'"popupc(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'video/mp4'){
        $size = $filesize;
        $dirload = $click.'"popupv(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'video/ogg'){
        $size = $filesize;
        $dirload = $click.'"popupv(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'video/webm'){
        $size = $filesize;
        $dirload = $click.'"popupv(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'audio/mpeg'){
        $size = $filesize;
        $dirload = $click.'"popupa(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'audio/ogg'){
        $size = $filesize;
        $dirload = $click.'"popupa(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'audio/x-wav'){
        $size = $filesize;
        $dirload = $click.'"popupa(this);"';
        $column = 'column';
        
        }else {
        $size = $filesize;
        $dirload = "";
        $column = 'column';
        }

        $source = substr($_GET['id'], 0, -6);
        $current = encrypt($conn, 1, $source);

        $source = substr($directory.'/'.$r['file_name'], 0, -6);
        $img = encrypt($conn, 1, $source);

        $thumbs = thumbs($conn, 1, $thumbtype);

        if ($thumbs == 'Icons') {
        $thumbnails = '<i class="'.filetypes($filetype, $icons).'" aria-hidden="true"></i>';
        }else if($thumbs == 'Thumbnails') {

            if($r['file_type'] == 'image/png') {
            $thumbnails = '<img src="'.$img.'"  style="max-height:100px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/jpg') {
            $thumbnails = '<img src="'.$img.'"  style="max-height:100px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/jpeg') {
            $thumbnails = '<img src="'.$img.'"  style="max-height:100px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/gif') {
            $thumbnails = '<img src="'.$img.'"  style="max-height:100px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/svg+xml') {
            $thumbnails = '<img src="'.$img.'"  style="max-height:100px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/ico') {
            $thumbnails = '<img src="'.$img.'"  style="max-height:100px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/bmp') {
            $thumbnails = '<img src="'.$img.'"  style="max-height:100px; display:block; margin:auto;" />';
            }else{
            $thumbnails = '<i class="'.filetypes($filetype, $icons).'" aria-hidden="true"></i>';
            }
        
        }

        $folderdisp = str_replace("_"," ",strtolower(substr($r['file_name'], 0, -6)));
        
        if (strlen($folderdisp) > 49){
        $maxLength = 45;
        $folderdisp = substr($folderdisp, 0, $maxLength).'...';
        }
        
        echo sprintf('<div class="gridwrap"><div class="'.$column.'" value="'.$r['file_name'].'" id="grida" '.$dirload.'">'.$thumbnails.$folderdisp.'</div></div>');
        }
        
    echo '  </div>';
}


//latest files
function displatest(PDO $conn, $r) {
    $cwd = newcwd($conn, 1);
    $disp = dispnum($conn, 1);

    $id = $_GET['id'];
    $search = $_POST['query'];

    $uid = userid($conn, 1);

    echo '<div id="column" class="row gridview latest">';
    echo '<span>Recent Files</span>';

    $dispfav = '2';
    if($dispfav == '2') {
    $result = $conn->prepare("SELECT file_name, file_type, cwd FROM core_files WHERE user_id='".$uid."' ORDER BY reg_date DESC LIMIT 5 ");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    }

    while ($r = $result->fetch()) {

        $directory = $r['cwd'];
        $filetype = $r['file_type'];

        $getid = substr($_GET['id'], 0, -6).'/';

        if($r['file_type'] == 'image/png'){
        $size = $filesize;
        $dirload = "popupc(this);";
       
        }else if($r['file_type'] == 'image/gif'){
        $size = $filesize;
        $dirload = "popupc(this);";
       
        }else if($r['file_type'] == 'image/ico'){
        $size = $filesize;
        $dirload = "popupc(this);";
        
        }else if($r['file_type'] == 'image/svg+xml'){
        $size = $filesize;
        $dirload = "popupc(this);";
        
        }else if($r['file_type'] == 'image/bmp'){
        $size = $filesize;
        $dirload = "popupc(this);";
       
        }else if($r['file_type'] == 'image/jpg'){
        $size = $filesize;
        $dirload = "popupc(this);";
       
        }else if($r['file_type'] == 'image/jpeg'){
        $size = $filesize;
        $dirload = "popupc(this);";
       
        }else if($r['file_type'] == 'video/mp4'){
        $size = $filesize;
        $dirload = "popupv(this);";
      
        }else if($r['file_type'] == 'video/ogg'){
        $size = $filesize;
        $dirload = "popupv(this);";
        
        }else if($r['file_type'] == 'video/webm'){
        $size = $filesize;
        $dirload = "popupv(this);";
       
        }else if($r['file_type'] == 'audio/mpeg'){
        $size = $filesize;
        $dirload = "popupa(this);";
       
        }else if($r['file_type'] == 'audio/ogg'){
        $size = $filesize;
        $dirload = "popupa(this);";
        
        }else if($r['file_type'] == 'audio/x-wav'){
        $size = $filesize;
        $dirload = "popupa(this);";
       
        }else {
        $size = $filesize;
        $dirload = "";
        }

        $source = substr($_GET['id'], 0, -6);
        $current = encrypt($conn, 1, $source);

        $source = substr($directory.'/'.$r['file_name'], 0, -6);
        $img = encrypt($conn, 1, $source);

        $thumbs = thumbs($conn, 1, $thumbtype);

        if ($thumbs == 'Icons') {
        $thumbnails = '<i class="'.filetypes($filetype, $icons).'" aria-hidden="true"></i>';
        }else if($thumbs == 'Thumbnails') {

            if($r['file_type'] == 'image/png') {
            $thumbnails = '<img src="'.$img.'"  style="max-height:50px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/jpg') {
            $thumbnails = '<img src="'.$img.'"  style="max-height:50px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/jpeg') {
            $thumbnails = '<img src="'.$img.'"  style="max-height:50px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/gif') {
            $thumbnails = '<img src="'.$img.'"  style="max-height:50px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/svg+xml') {
            $thumbnails = '<img src="'.$img.'"  style="max-height:50px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/ico') {
            $thumbnails = '<img src="'.$img.'"  style="max-height:50px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/bmp') {
            $thumbnails = '<img src="'.$img.'"  style="max-height:50px; display:block; margin:auto;" />';
            }else{
            $thumbnails = '<i class="'.filetypes($filetype, $icons).'" aria-hidden="true"></i>';
            }
            
        }

        $folderdisp = str_replace("_"," ",strtolower(substr($r['file_name'], 0, -6)));
        
        if (strlen($folderdisp) > 49){
        $maxLength = 45;
        $folderdisp = substr($folderdisp, 0, $maxLength).'...';
        }

        echo sprintf('<div class="gridwrap"><div class="column" value="'.$r['file_name'].'" id="grida"" ondblclick="'.$dirload.'">'.$thumbnails.$folderdisp.'</div></div>');
    }
    
    echo '  </div>';
}

//listview options
function listoptions(PDO $conn, $r, $dispfav) {
    echo ('</div>');
    
    if ($dispfav == '2') {
    displatest($conn, 1);
    echo ('<div class="listoptions">');
    }else {
    echo ('<div class="listoptions latestoptions">');
    }
    
    echo('<button type="submit" form="delete" onclick="submit();" name="deletebtn" class="listviewbtn" ><i class="fas fa-trash-alt"></i>Delete</button>');
    echo('<button type="submit" form="delete" onclick="submit();" name="move" class="listviewbtn" ><i class="fas fa-external-link-alt"></i>Move</button>');
    //echo('<button class="listviewbtn" ><i class="fa fa-share-alt"></i>Share</button></div>');
    echo('</div>');
}


//thumbtype
function thumbs(PDO $conn, $r, $thumbtype) {
    $result = $conn->prepare("SELECT setting FROM core_options WHERE options = 'icontype'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();

    $thumbtype = $r['setting'];

    return $thumbtype;
}

//number of files per page
function dispnum(PDO $conn, $r) {
    $result = $conn->prepare("SELECT setting FROM core_options WHERE options = 'dispnum'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();

    $types = $r['setting'];
    
    return $types;
}

//display list
function displist(PDO $conn, $r, $dispfav) {
    
    $disp = dispnum($conn, 1);

    if($dispfav == '0') {
    $cwd = $r['dir_id'];
    }else {
    $cwd = newcwd($conn, 1);
    }
    
    $search = $_POST['query'];
    
    if (isset($_GET['sorted'])) {
    $_SESSION['sorted'] = $_GET['sorted'];
    $sorted = $_GET['sorted'];
    }else {
    $sorted = $_SESSION['sorted'];
    }
    
    if($search != '') {
    $par = '1';
    }else {
    $par = '0';
    }
    
    if(isset($_GET['nid'])) {
    $id = $_GET['id'];
    $uid = $_GET['nid'];
    
    }else {
    $id = $_GET['id'];
    $uid = userid($conn, 1);   
    }

    if(isset($_POST['nid'])) {
        
        if($dispfav == '0') {
        $uid = $_POST['nid'];
        }
    }
    
    if(isset($_POST['sortby'])) {
    $sorted = $_POST['sortby'];
    $_SESSION['sorted'] = $_POST['sortby']; 
    }

    $start = '0';

    if(isset($_POST['next'])) {
    $start = $_POST['next'];
    }

    if(isset($_POST['prev'])) {
    $start = $_POST['prev'] ;
    }
    
    if (isset($_GET['sortlink'])) {
   
        if ($_GET['sortlink'] == 'file_name') {
        $ordern = '<i class="fas fa-caret-down"></i>';
        $sorted = $_GET['sortlink'].' ASC';
        $_SESSION['sorted'] = $_POST['sorted'];
        }
        
        if ($_GET['sortlink'] == 'reg_date') {
        $orderd = '<i class="fas fa-caret-down"></i>';
        $sorted = $_GET['sortlink'].' ASC';
        $_SESSION['sorted'] = $_POST['sorted']; 
        }
        
        if ($_GET['sortlink'] == 'file_type') {
        $ordert = '<i class="fas fa-caret-down"></i>';
        $sorted = $_GET['sortlink'].' ASC';
        $_SESSION['sorted'] = $_POST['sorted']; 
        }
        
        if ($_GET['sortlink'] == 'file_size') {
        $orders = '<i class="fas fa-caret-down"></i>';
        $sorted = $_GET['sortlink'].' ASC';
        $_SESSION['sorted'] = $_POST['sorted']; 
        }
        
    }
    
    echo('<div id="'.$id.'" class="row listview" value="'.$sorted.'"><div class="column-top" id="grida">');
    echo('<label class="checkthis"><input type="checkbox" value ="'.$r['file_name'].'" name="selected[]" onclick="selectall();" class="selectall"/><span class="checkmark"></span></label>');
    echo('<span class="otherwideleft"></span><span class="sort name" value="file_name" onmouseover="sortlink();">Name'.$ordern.'</span>');
    echo('<span class="sort date" value="reg_date" onmouseover="sortlink();">Date'.$orderd.'</span>');
    echo('<span value="file_type" class="sort otherwide" onmouseover="sortlink();">File type'.$ordert.'</span>');
    echo('<span class="other"></span><span value="file_size" class="sort otherwide" onmouseover="sortlink();">Size'.$orders.'</span>');
    echo('</div>');
    
    echo('<form id="delete"  method="post" class="selector" onsubmit="return false">');

    $disptype = 'list';
    echo parentdir($conn, 1, $par,$id, $disptype);     

    if($dispfav == '2') { //for all
    $result = $conn->prepare("SELECT file_name, reg_date, file_type, folder_fav, file_size, dir_id, user_id, cwd FROM core_folders WHERE user_id='".$uid."' AND dir_id='".$id."' UNION ALL SELECT file_name, reg_date,  file_type, folder_fav, file_size, dir_id, user_id, cwd FROM core_files WHERE user_id='".$uid."' AND dir_id='".$id."' ORDER BY ".$sorted." LIMIT ".$start." , ".$disp."");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);  
    
    }else if($dispfav == '1') { //for favorites
    $result = $conn->prepare("SELECT file_name, reg_date, file_type, folder_fav, file_size, dir_id, user_id, cwd FROM core_folders WHERE user_id='".$uid."' AND dir_id='".$id."' AND folder_fav='1' UNION ALL SELECT file_name, reg_date,  file_type, folder_fav, file_size, dir_id, user_id, cwd FROM core_files WHERE user_id='".$uid."' AND dir_id='".$id."' AND folder_fav='1' ORDER BY ".$sorted." LIMIT ".$start." , ".$disp."");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    
    }else if($dispfav == '0') { //for search
    $result = $conn->prepare("(SELECT file_name, reg_date, file_type, folder_fav, file_size, dir_id, user_id, cwd FROM core_folders WHERE file_name LIKE '%".$search."%' AND user_id='".$uid."' LIMIT ".$disp.") UNION (SELECT file_name, reg_date, file_type, folder_fav, file_size, dir_id, user_id, cwd FROM core_files WHERE file_name LIKE '%".$search."%' AND user_id='".$uid."' LIMIT ".$disp.")");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    }


    while ($r = $result->fetch()) {        
        
        $directory = $r['cwd'];
        $filetype = $r['file_type'];
        
        if ($r['folder_fav'] == 1) {
        $faved = 'favbtnactive';
        }else {
        $faved = 'favbtn';
        }


        if($id == 'drives') {
        $loc ='';
        }else {
        $loc =substr($r['dir_id'], 0, -6).'/';
        }

        $source = substr($directory.'/'.$r['file_name'], 0, -6);
        $url = encrypt($conn, 1, $source);

        if($r['file_type'] != 'Directory') {
        $download = '<a href="'.$url.'" class="detbtn" download><i class="fa fa-download"></i><span>Download</span></a>';
        }else if($r['file_type'] == 'Directory') {
        $download ='<button type="submit" name="dl" form="dl"  value="'.substr($r['file_name'], 0, -6).'" class="detbtn"><i class="fa fa-download"></i><span>Download</span></button>';
        }

        if($r['dir_id']  != 'drives') {
        $source = substr($r['dir_id'], 0, -6).'/';
        $current = encrypt($conn, 1, $source);
        
        }else {
        $source = substr($r['dir_id'], 0, -6);
        $current = encrypt($conn, 1, $source);
        }

        $source = $r['file_name'];
        $foldname = encrypt($conn, 1, $source);

        $source = str_replace("_"," ",strtolower(substr($r['file_name'], 0, -6)));
        $folderdisp = encrypt($conn, 1, $source);

        $source = str_replace("_"," ",strtolower(substr($r['file_name'], 0, -10)));
        $filedisp = encrypt($conn, 1, $source);

        $source = format(dirsize(substr($directory.'/'.$r['file_name'], 0, -6)));
        $foldersize = encrypt($conn, 1, $source);

        $source = format($r['file_size']);
        $filesize = encrypt($conn, 1, $source);

        $source = substr($directory.'/'.$r['file_name'], 0, -6);
        $img = encrypt($conn, 1, $source);
        //$img = thumbnails($filename, 0.3);
        
        $source = $_POST['dl'];
        $dlfile = encrypt($conn, 1, $source);

        $source = $directory.'/'.$_POST['dl'].'/';
        $pathsource = encrypt($conn, 1, $source);

        if($r['file_type'] != 'Directory') {
        $imgdisp = 'imgdisp';
        }else if($r['file_type'] == 'Directory') {
        $imgdisp = '';
        }

        if(is_dir(substr(dirloc($conn, 1).userid($conn, 1 ).'/'.$current.'/'.$r['file_name'], 0, -6))) {
        $name = str_replace("_"," ",strtolower(substr($r['file_name'], 0, -6)));
        }else if(is_file(substr(dirloc($conn, 1).userid($conn, 1 ).'/'.$current.'/'.$r['file_name'], 0, -6))) {
        $name = str_replace("_"," ",strtolower(substr($r['file_name'], 0, -10)));
        }

        if($r['dir_id']  != 'drives') {
        $getid = substr($id, 0, -6).'/';
        }else {
        $getid = substr($id, 0, -6);
        }


        if(is_dir(substr($directory.'/'.$r['file_name'], 0, -6))) {
        $size = $foldersize;
        //$dirload = "window.location.href='folders?id=" .$r['file_name']. "';";
        $dirload = 'onmouseover="loaddir();"';
        $column = 'column columnd';
        $newcwd = substr(dirloc($conn, 1).userid($conn, 1 ).'/'.$r['file_name'].'/'.$id, 0, -6);
        
        }else if($r['file_type'] == 'image/png'){
        $size = $filesize;
        $dirload = 'ondblclick="popupc(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'image/gif'){
        $size = $filesize;
        $dirload = 'ondblclick="popupc(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'image/ico'){
        $size = $filesize;
        $dirload = 'ondblclick="popupc(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'image/svg+xml'){
        $size = $filesize;
        $dirload = 'ondblclick="popupc(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'image/bmp'){
        $size = $filesize;
        $dirload = 'ondblclick="popupc(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'image/jpg'){
        $size = $filesize;
        $dirload = 'ondblclick="popupc(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'image/jpeg'){
        $size = $filesize;
        $dirload = 'ondblclick="popupc(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'video/mp4'){
        $size = $filesize;
        $dirload = 'ondblclick="popupv(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'video/ogg'){
        $size = $filesize;
        $dirload = 'ondblclick="popupv(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'video/webm'){
        $size = $filesize;
        $dirload = 'ondblclick="popupv(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'audio/mpeg'){
        $size = $filesize;
        $dirload = 'ondblclick="popupa(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'audio/ogg'){
        $size = $filesize;
        $dirload = 'ondblclick="popupa(this);"';
        $column = 'column';
        
        }else if($r['file_type'] == 'audio/x-wav'){
        $size = $filesize;
        $dirload = 'ondblclick="popupa(this);"';
        $column = 'column';
        
        }else {
        $size = $filesize;
        $dirload = '"';
        $column = 'column';
        }

        $thumbs = thumbs($conn, 1, $thumbtype);        
        
        if ($thumbs == 'Icons') {
        $thumbnails = '<i class="'.filetypes($filetype, $icons).'" aria-hidden="true"></i>';
        }else if($thumbs == 'Thumbnails') {

            if($r['file_type'] == 'image/png') {
            $thumbnails = '<div class="imgwrap"><img src="'.$img.'" style="max-height:48px; width: auto; height: 100%;" /></div>';
            }else if($r['file_type'] == 'image/gif') {
            $thumbnails = '<div class="imgwrap"><img src="'.$img.'" style="max-height:48px; width: auto; height: auto;" /></div>';
            }else if($r['file_type'] == 'image/svg+xml') {
            $thumbnails = '<div class="imgwrap"><img src="'.$img.'" style="max-height:48px; width: auto; height: 100%;" /></div>';
            }else if($r['file_type'] == 'image/bmp') {
            $thumbnails = '<div class="imgwrap"><img src="'.$img.'" style="max-height:48px; width: auto; height: 100%;" /></div>';
            }else if($r['file_type'] == 'image/ico') {
            $thumbnails = '<div class="imgwrap"><img src="'.$img.'" style="max-height:48px; width: auto; height: 100%;" /></div>';
            }else if($r['file_type'] == 'image/jpg') {
            $thumbnails = '<div class="imgwrap"><img src="'.$img.'" style="max-height:48px; width: auto; height: 100%;" /></div>';
            }else if($r['file_type'] == 'image/jpeg') {
            $thumbnails = '<div class="imgwrap"><img src="'.$img.'" style="max-height:48px; width: auto; height: 100%;" /></div>';
            }else{
            $thumbnails = '<i class="'.filetypes($filetype, $icons).'" aria-hidden="true"></i>';
            }
            
        }
        
        
        echo ('<div class="'.$column.'" value="'.$r['file_name'].'" id="grida" '.$dirload.'>');
        echo ('<label class="checkthis"><input type="checkbox" value ="'.$r['file_name'].'" name="selected[]" onclick="sel();" class="deletebox"/><span class="checkmark"></span></label>');
        //echo ('<button onclick="return false" ondblclick="'.$dirload.'" class="foldlinks">');
        echo ('<button type="submit" value="'.$r['file_name'].'" class="addfav '.$faved.'" name="fav" onmouseover="addfav();" id="'.$r['dir_id'].'"><i class="fa fa-star"></i></button>');
        echo ($thumbnails.'<input type="text" class="filename" placeholder="'.$folderdisp.'" id="'.$name.'-iname" size="" onload="resizeInput();" readonly><button id="'.$name.'-rebtn" class="rbtn" type="submit" name="renamebtn" onclick="update();" value ="'.$r['file_name'].'" ><span>Rename</span></button>');
        echo ('<span class="date">'.substr($r['reg_date'], 0, -8).'</span>');
        echo ('<span class="otherwide">'.$r['file_type'].'</span>');
        
        echo ('<div class="detailsdown">
        <button type="button" class="listbtn"><i class="fa fa-ellipsis-h"></i></button>
        <div class="details-content"><button type="submit" value="'.$r['file_name'].'" form="details" class="share detbtn" name="shared" onclick="popup(this);"><i class="fa fa-share-alt"></i><span>Share</span></button><button type="submit" name="detbtn" class="detbtn" form="details" value="'.$r['file_name'].'" onclick="infodet(this);"><i class="fa fa-info"></i><span>Details</span></button><button id="'.$name.'-rname" class="detbtn" type="button" onclick="renamefile(this.id)"><i class="fas fa-font"></i><span>Rename</span></button><button value ="'.$r['file_name'].'" name="deleted" class="deletebtn detbtn" type="submit" id="'.$r['dir_id'].'" onmouseover="deletefiles();"><i class="fa fa-trash"></i><span>Delete</span></button>'.$download.'</div>
        </div>');
        
        echo ('<span class="otherwide">'.$size.'</span></button></div>');

    }

    echo '</form></div>';
    
    if (isset($_POST['dl'])) {
    zip($dlfile, $pathsource);
    echo '<script type="text/javascript">';
    echo 'location.href = "../'.$dlfile.'.zip"; ';
    echo '</script>';
    }
    
//var_dump($ascdesc);
}

function thumbnails($filename, $percent) {
//$filename = 'profile/bg.jpg';
//$percent = 0.3;

header('Content-Type: image/jpeg');

list($width, $height) = getimagesize($filename);
$new_width = $width * $percent;
$new_height = $height * $percent;

$image_p = imagecreatetruecolor($new_width, $new_height);
$image = imagecreatefromjpeg($filename);
imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

$url = $filename.'-t';
imagejpeg($image_p, $url, 90);

}

//multi file delete
function multidelete(PDO $conn, $r) {

}

//pagination
function pagination(PDO $conn, $r) {

    $result = $conn->prepare("SELECT COUNT(*) AS count FROM core_folders WHERE user_id='".userid($conn, 1)."' AND dir_id='".$_GET['id']."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $count = $r['count'];

    $result = $conn->prepare("SELECT COUNT(*) AS countf FROM core_files WHERE user_id='".userid($conn, 1)."' AND dir_id='".$_GET['id']."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $countf = $count + $r['countf'];

    $result = $conn->prepare("SELECT setting FROM core_options WHERE options = 'dispnum'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $dispnum = $r['setting'];


    $newval = $dispnum + $_POST['next'];
    $prevval = $_POST['next'] - $dispnum;

    if (isset($_POST['prev'])) {
    $newval = $dispnum + $_POST['prev'];
    $prevval = $_POST['prev'] - $dispnum;
    }

    if (isset($_POST['next'])) {
    $prevval = $_POST['next'] - $dispnum;
    }

    $nextbtn = '<button type="submit" name="next" id="next" class="btn" value="'.$newval.'" >Next<i class="fas fa-angle-right"></i></button>';
    $prevbtn = '';

    if ($newval >= $countf) {
    $nextbtn = '';
    }

    if ($newval - $dispnum >= $dispnum) {
    $prevbtn = '<button type="submit" id="prev" name="prev" class="btn" value="'.$prevval.'"><i class="fas fa-angle-left"></i>Prev</button>';
    }

    if ($countf > $dispnum) {
    echo '<form method="post" id="page">'.$nextbtn.$prevbtn.'</form>';
    }

}


//share
function share(PDO $conn, $r) {
    $share = $_POST['share'];

    $result = $conn->prepare("(SELECT user_id, file_name, dir_id, cwd FROM core_folders where file_name='".$share."')  UNION (SELECT user_id, file_name, dir_id, cwd FROM core_files where file_name='".$share."')");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();

    if($r['dir_id']  != 'drives') {
    $source = substr($r['dir_id'], 0, -6).'/';
    $current = encrypt($conn, 1, $source);
    
    }else {
    $source = substr($r['dir_id'], 0, -6);
    $current = encrypt($conn, 1, $source);
    }

    $link = substr($r['cwd'].'/'.$share, 2, -6);
    $uri = $_SERVER['HTTP_HOST'].substr($_SERVER['REQUEST_URI'], 0, -9);
    
    echo '<div class="modaltop">
    <label>share</label><span class="close-button" onclick="closemodal();"><i class="fas fa-times-circle"></i></span></div>
    <div class="acont" id="acont"><input type="text" name="folder" value="http://'.$uri.$link.'" id="copied" readonly> 
    <button onclick="copy()" type="button" class="create"><i class="fas fa-copy"></i>copy</button></div>';
}

//display image modal
function imgdisp(PDO $conn, $r) {
    $imgmodal = $_POST['imgm'];

    $result = $conn->prepare("SELECT user_id, dir_id, cwd  FROM core_files where file_name='".$imgmodal."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $directory = $r['cwd'];

    if($r['dir_id']  != 'drives') {
    $source = substr($r['dir_id'], 0, -6).'/';
    $current = encrypt($conn, 1, $source);
    
    }else {
    $source = substr($r['dir_id'], 0, -6);
    $current = encrypt($conn, 1, $source);
    }

    $source = substr($directory.'/'.$imgmodal, 0, -6);
    $image = encrypt($conn, 1, $source);

    
    if (ismobile()) {
    echo '<div class="acont" id="acont"><img src="'.$image.'" class="imgmodal" /></div>';
    
    }else {
    echo '<div class="modaltop">
    <label>'.substr($imgmodal, 0, -6).'</label><span class="close-button" onclick="closemodal();"><i class="fas fa-times-circle"></i></span></div>
    <div class="acont" id="acont"><img src="'.$image.'" class="imgmodal" /></div>
    <a href="'.$image.'" class="modalbtn" download><i class="fa fa-download"></i><span>Download</span></a>';
    }
}

//display video modal
function videodisp(PDO $conn, $r) {
    $videomodal = $_POST['videom'];

    $result = $conn->prepare("SELECT user_id, dir_id, cwd FROM core_files where file_name='".$videomodal."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $directory = $r['cwd'];

    if($r['dir_id']  != 'drives') {
    $source = substr($r['dir_id'], 0, -6).'/';
    $current = encrypt($conn, 1, $source);
    
    }else {
    $source = substr($r['dir_id'], 0, -6);
    $current = encrypt($conn, 1, $source);
    }

    $source = substr($directory.'/'.$videomodal, 0, -6);
    $video = encrypt($conn, 1, $source);

    if (ismobile()) {
    $w = '100%';
    $h = 'auto';
    }else {
    $w = '800';
    $h = '600';
    }
    
    echo '<div class="modaltop">
    <label>'.substr($videomodal, 0, -6).'</label><span class="close-button" onclick="closemodal();"><i class="fas fa-times-circle"></i></span></div>
    <div class="acont" id="acont"><video id="media" width="'.$w.'" height="'.$h.'" controls><source src="'.$video.'"></video></div>';
}

//display audio modal
function audiodisp(PDO $conn, $r) {
    $audiomodal = $_POST['audiom'];

    $result = $conn->prepare("SELECT user_id, dir_id, cwd FROM core_files where file_name='".$audiomodal."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $directory = $r['cwd'];

    if($r['dir_id']  != 'drives') {
    $source = substr($r['dir_id'], 0, -6).'/';
    $current = encrypt($conn, 1, $source);
    
    }else {
    $source = substr($r['dir_id'], 0, -6);
    $current = encrypt($conn, 1, $source);
    }

    $source = substr($directory.'/'.$audiomodal, 0, -6);
    $audio = encrypt($conn, 1, $source);

    if (ismobile()) {
    $w = '300';
    $h = '60';
    }else {
    $w = '680';
    $h = '120';
    }
    
    echo '<div class="modaltop">
    <label>'.substr($audiomodal, 0, -6).'</label><span class="close-button" onclick="wavesurfer.destroy(); closemodal();"><i class="fas fa-times-circle"></i></span></div>

    <div class="audiowrap">
    <div class="audio"><i class="fas fa-music"></i></div>
    <div id="audio"></div>
    </div>
    <a href="'.$audio.'" class="modalbtn mediabtn" download><i class="fa fa-download"></i><span>Download</span></a>
    <a class="modalbtn mediabtn pause" onclick="wavesurfer.pause();" style="display:none;"><i class="fas fa-pause"></i>Pause</a>
    <a class="modalbtn mediabtn play" onclick="wavesurfer.play();"><i class="fas fa-play"></i>Play</a>


    <script>
    var wavesurfer = WaveSurfer.create({
        container: "#audio",
        waveColor: "#dddddd",
        progressColor: "#b20938",
        barWidth: "1",
        maxCanvasWidth: "'.$w.'",
        height: "'.$h.'"
    });

    wavesurfer.load("'.$audio.'");
    
    wavesurfer.on("loading", function () {
    $( "#aniout" ).show();
    });

    wavesurfer.on("ready", function () {
    $( "#aniout" ).hide();
    });
    
    $(".play").click(function() {
    $(".pause").show();
    $(".play").hide();
    });
    
    $(".pause").click(function() {
    $(".play").show();
    $(".pause").hide();
    });
    </script>';
    
    //<div class="acont" id="acont"><audio id="media" width="800" controls><source src="'.$audio.'"></audio></div>';
}

//rename folders
if (isset($_POST['renamebtn'])) {
    $newname = $_POST['rename'].'-'.salted();
    $newdir = $_POST['rename'];
    $result = $conn->prepare("UPDATE core_folders SET file_name='".$newname."' WHERE file_name='".$_POST['renamebtn']."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);

    $oldfold = substr($_POST['renamebtn'], 0, -6);
    $newfold = substr($_GET['id'], 0, -6).'/'.str_replace(" ","_",strtolower($oldfold));
    rename(dirloc($conn, 1).userid($conn, 1 ).'/'.$newfold , dirloc($conn, 1).userid($conn, 1 ).'/'.substr($_GET['id'], 0, -6).'/'.$newdir);

    $result = $conn->prepare("UPDATE core_folders SET dir_id='".$newname."' WHERE dir_id='".$_POST['renamebtn']."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);

    $result = $conn->prepare("UPDATE core_files SET dir_id='".$newname."' WHERE dir_id='".$_POST['renamebtn']."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
}

//rename files
    if (isset($_POST['renamebtn'])) {
    $newname = $_POST['rename'].'-'.salted();
    $newdir = $_POST['rename'];
    $result = $conn->prepare("UPDATE core_files SET file_name='".$newname."' WHERE file_name='".$_POST['renamebtn']."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);

    $oldfold = substr($_POST['renamebtn'], 0, -6);
    $newfold = substr($_GET['id'], 0, -6).'/'.str_replace(" ","_",strtolower($oldfold));
    rename(dirloc($conn, 1).userid($conn, 1 ).'/'.$newfold , dirloc($conn, 1).userid($conn, 1 ).'/'.substr($_GET['id'], 0, -6).'/'.$newdir);
}


//delete
function deletefiles(PDO $conn, $r) {
//     if (isset($_POST['deleted'])) {
//     $deleted = $_POST['deleted'];
// 
//     $result = $conn->prepare("SELECT file_name FROM core_folders where dir_id='".$deleted."'");
//     $result->execute();
//     $result->setFetchMode(PDO::FETCH_ASSOC);
//     $r = $result->fetch();
// 
//     $deldir = $dirloc.'/'.substr($r['dir_id'], 0, -6)."/".substr($deleted, 0, -6);
//     rmdir($deldir);
// 
//     $result = $conn->prepare("SELECT file_name, cwd FROM core_files where file_name='".$deleted."'");
//     $result->execute();
//     $result->setFetchMode(PDO::FETCH_ASSOC);
//     $r = $result->fetch();
//     $cwd =$r['cwd'];
// 
//     $delfile = $cwd."/".substr($deleted, 0, -6);
// 
//         if (file_exists($delfile)) {
//         unlink($delfile);
//         }
//     }

if (isset($_POST['deleted'])) {
    $deleted = $_POST['deleted'];
    $uri = $_SERVER['HTTP_HOST'];
    
    $result = $conn->prepare("SELECT file_name, cwd, user_id FROM core_folders WHERE file_name='".$deleted."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    
    $orig = $uri.substr($r['cwd'].'/'. $r['file_name'], 2, -6);
    $new = $uri.substr(dirloc($conn, 1).$r['user_id'].'trash'.'/'. $r['file_name'], 2, -6);
    
    rename($orig, $new);
    
    $result = $conn->prepare("UPDATE core_folders SET dir_id='trash' WHERE dir_id='".$deleted."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    
    $result = $conn->prepare("UPDATE core_folders SET dir_id='trash' WHERE file_name='".$deleted."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    
        
    $result = $conn->prepare("SELECT file_name, cwd, user_id FROM core_files WHERE file_name='".$deleted."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    
    $orig = $uri.substr($r['cwd'].'/'.$r['file_name'], 2, -6);
    $new = $uri.substr(dirloc($conn, 1).$r['user_id'].'trash'.'/'.$r['file_name'], 2, -6);
    
    rename($orig, $new);
    
    $result = $conn->prepare("UPDATE core_files SET dir_id='trash' WHERE dir_id='".$deleted."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    
    $result = $conn->prepare("UPDATE core_files SET dir_id='trash' WHERE file_name='".$deleted."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
}
    
//     $result = $conn->prepare("DELETE FROM core_folders WHERE file_name='".$deleted."'");
//     $result->execute();
//     $result->setFetchMode(PDO::FETCH_ASSOC);
// 
//     $result = $conn->prepare("DELETE FROM core_files WHERE file_name='".$deleted."'");
//     $result->execute();
//     $result->setFetchMode(PDO::FETCH_ASSOC);
}
    
//delete plugin
function removeplug($delplug) {
    $files = glob($delplug . '/*');
    
    foreach ($files as $file) {
    is_dir($file) ? removeplug($file) : unlink($file);
    }
    
    rmdir($delplug);
    
}

if (isset($_POST['delete'])) {
    $deleted = $_POST['delete'];

    $delplug = $root.'plugins/'.strtolower($deleted);
    //echo $delplug;
    removeplug($delplug);

    $result = $conn->prepare("DELETE FROM core_plugins WHERE plugin='".$deleted."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
}
    

//add to favorites
function addfav(PDO $conn, $r) {
    $result = $conn->prepare("SELECT folder_fav FROM core_folders WHERE file_name='".$_POST['fav']."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $addfav = $r['folder_fav'];

    if(isset($_POST['fav'])) {

        if ($addfav == '0') {
        $result = $conn->prepare("UPDATE core_folders SET folder_fav='1'  WHERE file_name='".$_POST['fav']."'");
        $result->execute();
        $result->setFetchMode(PDO::FETCH_ASSOC);
        
        }else if($addfav == '1') {
        $result = $conn->prepare("UPDATE core_folders SET folder_fav='0'  WHERE file_name='".$_POST['fav']."'");
        $result->execute();
        $result->setFetchMode(PDO::FETCH_ASSOC);
        }
    }

    $result = $conn->prepare("SELECT folder_fav FROM core_files WHERE file_name='".$_POST['fav']."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $addfav = $r['folder_fav'];

    if(isset($_POST['fav'])) {

        if ($addfav == '0') {
        $result = $conn->prepare("UPDATE core_files SET folder_fav='1'  WHERE file_name='".$_POST['fav']."'");
        $result->execute();
        $result->setFetchMode(PDO::FETCH_ASSOC);
        
        }else if($addfav == '1') {
        $result = $conn->prepare("UPDATE core_files SET folder_fav='0'  WHERE file_name='".$_POST['fav']."'");
        $result->execute();
        $result->setFetchMode(PDO::FETCH_ASSOC);
        }
    }
}
    
    
//display details
function displaydet(PDO $conn, $r) {

    $info = $_POST['detfile'];


    $result = $conn->prepare("(SELECT file_name, reg_date, file_type, folder_fav, file_size, user_id, dir_id, cwd FROM core_folders WHERE file_name = '".$info."') UNION (SELECT file_name, reg_date, file_type, folder_fav, file_size, user_id, dir_id, cwd FROM core_files WHERE file_name = '".$info."')");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();

    $directory = $r['cwd'];

    if($r['file_type'] == 'Directory') {
    $filename = $r['file_name'];
    $filesize = format(dirsize(substr($directory.'/'.$r['file_name'], 0, -6)));
    }else {
    $filename = $r['file_name'];
    $filesize = format($r['file_size']);
    }

    if( $r['dir_id'] == 'drives') {
    $location = 'root';
    }else {
    $location = substr($r['dir_id'], 0, -6);
    }
    $source = substr($r['dir_id'], 0, -6);
    $current = encrypt($conn, 1, $source);

    $source = substr($directory.'/'.$r['file_name'], 0, -6);
    $img = encrypt($conn, 1, $source);

    $filetype = $r['file_type'];



    if ($r['file_type'] == 'Directory') {
    $thumbnails = '<i class="'.filetypes($filetype, $icons).'" aria-hidden="true"></i>';
    }if($r['file_type'] == 'image/png') {
    $thumbnails = '<img src="'.$img.'" />';
    }else if($r['file_type'] == 'image/gif') {
    $thumbnails = '<img src="'.$img.'" />';
    }else if($r['file_type'] == 'image/svg+xml') {
    $thumbnails = '<img src="'.$img.'" />';
    }else if($r['file_type'] == 'image/bmp') {
    $thumbnails = '<img src="'.$img.'" />';
    }else if($r['file_type'] == 'image/ico') {
    $thumbnails = '<img src="'.$img.'" />';
    }else if($r['file_type'] == 'image/jpg') {
    $thumbnails = '<img src="'.$img.'" />';
    }else if($r['file_type'] == 'image/jpeg') {
    $thumbnails = '<img src="'.$img.'" />';
    }else{
    $thumbnails = '<i class="'.filetypes($filetype, $icons).'" aria-hidden="true"></i>';
    }



    echo '<div id="title"><h2><i class="'.filetypes($filetype, $icons).'" aria-hidden="true"></i>'.substr($filename, 0, -6).'</h2></div>';

    echo '<div class="preview">'.$thumbnails.'</div>';

    echo '<div class="filecont"><span>Name</span>'.substr($filename, 0, -6).'<br />
    <span>Type</span>'.$r['file_type'].'<br />
    <span>Size</span>'.$filesize.'<br />
    <span>Location</span>'.$location.'<br />
    <span>Date created</span>'.substr($r['reg_date'], 0, -8).'<br />';

    $result = $conn->prepare("SELECT core_username FROM core_users WHERE usalt='".$r['user_id']."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();

    echo '<span>Owner</span>'.$r['core_username'].'<br />
    </div>';
}

//display users
function dispusers(PDO $conn, $r) {
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

//display plugins
function dispplugins(PDO $conn, $r) {
    echo '<ul class="side">';
    $result = $conn->prepare("SELECT core_username FROM core_users ORDER BY id ASC");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
   
   while ($r = $result->fetch()) {
    echo sprintf('<li class="dir">
    <a href=""><i class="fa fa-plug"></i>'.$r['core_username'].'</a></li>');   
    }
    
    echo '</ul>';
}

//extra salts
function salted($length = 5) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    
    return $randomString;  
}

//logout
function loggedin($root) {
    if ($_SESSION['timeout'] + 1 * 60 < time()) {
    // session timed out
    session_unset();
    session_destroy();
    $login = $root.'login';
    header("Location: $login"); //die();
    }
}

//restricted access
function restrict(PDO $conn, $r, $admin, $username) {
    $username = $_SESSION['user'];
    $result = $conn->prepare("SELECT user_type, usalt FROM core_users WHERE core_username='".$username."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $admin = $r['user_type'];
    $usalt = $r['usalt'];
    
    if($admin != 'Administrator') {
        if($_GET['id'] != $usalt) {
        header("Location: ../drive/restrict");
        }
    }
}

//restricted links
function restrictlink(PDO $conn, $r, $admin, $username) {
    $username = $_SESSION['user'];
    
    $result = $conn->prepare("SELECT user_type, usalt FROM core_users WHERE core_username='".$username."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    
    $admin = $r['user_type'];
    $usersalt = $r['usalt'];
    
    return $admin;
}

//restricted form
function restrictform(PDO $conn, $r, $admin, $username) {
    $username = $_SESSION['user'];
    
    $result = $conn->prepare("SELECT user_type, usalt FROM core_users WHERE core_username='".$username."'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $admin = $r['user_type'];
    $usalt = $r['usalt'];
    
    if($admin != 'Administrator') {
    echo 'disabled';
    }
} 

//openssl encryption 
//$source = encryption source $echoed = decrypted content
function encrypt(PDO $conn, $r, $source) {
    $result = $conn->prepare("SELECT setting FROM core_options WHERE options = 'enableEncryption'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();

    if($r['setting'] == 'Enable') {

        $result = $conn->prepare("SELECT setting FROM core_options WHERE options = 'enctype'");
        $result->execute();
        $result->setFetchMode(PDO::FETCH_ASSOC);
        $r = $result->fetch();

        if( $r['setting'] == 'AES-256-CBC'){
        $enctype = 'AES-256-CBC';
        }elseif($r['setting'] == 'AES-128-CBC'){
        $enctype = 'AES-128-CBC';
        }

        $ivlen = openssl_cipher_iv_length($cipher= $enctype);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($source, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
        $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );

        $c = base64_decode($ciphertext);
        $ivlen = openssl_cipher_iv_length($cipher= $enctype);
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $output = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
        
        if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
        {
        return $output;
        }
        
    }else {
    return $source;
    }
}

//supported file types 
function supported(){
    $types = json_encode($support);
}


//download directory
function zip($dlfile, $pathsource){
    $pathdir = $pathsource;
    $zipcreated = "../".$dlfile.".zip";
    $dl = new ZipArchive;
    
    if($dl -> open($zipcreated, ZipArchive::CREATE ) === TRUE) {
    $dir = opendir($pathdir);
        
        while($file = readdir($dir)) {

            if(is_file($pathdir.$file)) {
            $dl -> addFile($pathdir.$file, $file);
            }

        }
        
    $dl ->close();
    }
}

?>
