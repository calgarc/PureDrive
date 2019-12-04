<?php
/*

Core functions for Pure Drive.
--------------------------------------

1. Uploading and files
    1.1 upload files
    1.2 make directory
    1.3 active()
    1.4 filetypes()
    1.5 renamefiles()
    1.6 deletefiles()
    1.7 deleteMulti()
    1.8 trashdelete()
    1.9 emptytrash()
    1.10 removeplug()

2. Directory functions
    2.1 displayfiles()
    2.2 dirlink()
    2.3 dispfolders()
    2.4 dispfavfolders()
    2.5 plugins()
    2.6 search()
    2.7 geticon()
    2.8 parentdir()
    2.9 deleteoptions()
    2.10 listoptions()
    2.11 thumbs()
    2.12 dispnum()
    2.13 gallery()
    2.14 galleryfolders()
    2.15 dispgrid()
    2.16 displatest()
    2.17 displist()
    2.18 pagination()
    2.19 share()
    2.20 htaccess();
    2.21 imgdisp()
    2.22 videodisp()
    2.23 audiodisp()
    2.24 pdfdisp()
    2.25 addfav()
    2.26 displaydet()

--------------------------------------

*/



//Required files
if(!defined('func')) {
  header("Location: ../../login"); //die();
}

define('inc', TRUE);

require('security.inc.php');
require('misc.inc.php');
require('ui.inc.php');


error_reporting (E_ALL ^ E_NOTICE);
error_reporting(E_ERROR | E_PARSE);
$oldmask = umask(0);

/*

1. Uploading and files

*/

//upload files
    $usalt = UserId($conn, 1);

    $r = select($conn, "SELECT setting FROM core_options WHERE options= :dir", [':dir' => 'directory']);

    $dirlocf = $r['setting']."/".$usalt;
    $current = subsalt(id());

    $r = select($conn, "SELECT setting FROM core_options WHERE options= :size", [':size' => 'uploadSize']);

    $maxsize = $r['setting'].'000000';

    if(isset($_FILES['myFiles'])){
        if('true' == auth($conn, 1)) {
            $errors= array();
                foreach($_FILES['myFiles']['tmp_name'] as $key => $tmp_name ){
                    $file_name = $_FILES['myFiles']['name'][$key];
                    $file_size = $_FILES['myFiles']['size'][$key];
                    $file_tmp = $_FILES['myFiles']['tmp_name'][$key];
                    $file_type = $_FILES['myFiles']['type'][$key];
                    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

                    if($file_size > $maxsize){
                        $errors= '<div class="errors">'.format($maxsize).' Max file size </div>';
                    }

                    // $supported = array();
                    // $supported = array_merge($supported, array_map('trim', explode(",", SupportedFiles($conn, 1))));
                    $supported = str_replace(', ', ',',SupportedFiles($conn, 1));
                    $supported = explode(',',$supported);

                    if('' != SupportedFiles($conn, 1)) {
                        if(!in_array($file_ext, $supported)){
                          $errors= '<div class="errors">File type not supported.</div>';
                        }
                    }

                    require('filetypes.php');

                    $size = DirSize(DirLocation($conn, 1).$usalt);
                    $uplimit = limit($conn, 1);
                    $left = $uplimit - $size;

                    //if($size > $uplimit) {
                        if($file_size > $left) {
                            $errors= '<div class="errors">Not enough space left</div>';
                        }
                    //}

                    if (file_exists(NewCwd($conn, 1)."/".$file_name)) {
                      $errors= '<div class="errors">File already exists</div>';
                    }

                    if(empty($errors)==true){
                        $file_name = str_replace(" ","_",strtolower($file_name));
                        $file_name = sanitize(escape($file_name));

                        move_uploaded_file($file_tmp, NewCwd($conn, 1)."/".$file_name);

                        $file_name = encrypt($conn, 1, $file_name.'-'.salted());
                        //move_uploaded_file($file_tmp, NewCwd($conn, 1)."/".dataHash($file_name));

                        $result = $conn->prepare("INSERT INTO core_files (file_name, folder_fav, user_id, dir_id, file_type, file_size, cwd, trash) VALUES (:filename,'0','".$usalt."', :id,'".$file_type."','".$file_size."', '".NewCwd($conn, 1)."', '0')");
                        $result->execute([':id' => id(), ':filename' => $file_name]);
                        $result->setFetchMode(PDO::FETCH_ASSOC);

                        header('Location: ?id='.id().'');
                    }else{
                        print_r($errors);
                    }
                }

                if(empty($errors)==true) {
                  $foldersize = encrypt($conn, 1, DirSize(NewCwd($conn, 1)));

                  $r = select($conn, "UPDATE core_folders SET file_size = :filesize WHERE file_name = :id ", [':id' => id(), ':filesize' => $foldersize]);
                }
            }
    }

//make directory
//function makedir(PDO $conn, $r) {
    $usalt = UserId($conn, 1);

    $r = select($conn, "SELECT setting FROM core_options WHERE options= :dir", [':dir' => 'directory']);

    $dirloc = $r['setting']."/".dataHash($usalt);

    if('true' == auth($conn, 1)) {
        if('drives' == id()) {
            $foldername = encrypt($conn, 1, str_replace(" ","_",strtolower($_POST['folder'])));
            $foldername = encrypt($conn, 1, sanitize($foldername));

        }else {
            $foldername = encrypt($conn, 1, subsalt(id()).'/'.str_replace(" ","_",strtolower($_POST['folder'])));
            $foldername = encrypt($conn, 1, sanitize($foldername));
        }

        $r = select($conn, "SELECT dir_id, file_name FROM core_folders WHERE file_name = :id", [':id' => id()]);

        if ('drives' != $r['dir_id']) {
            $active = $dirloc.'/'.subsalt($r['dir_id'])."/".$foldername;
        }else {
            $active = $dirloc."/".$foldername;
        }

        if (isset($_POST['create'])){
            if(!is_dir($foldername)) {
                mkdir($active, 0755, true);
            }
        }
    }

    $folderid = encrypt($conn, 1, str_replace(" ","_",strtolower($_POST['folder'])));
    $folderid = encrypt($conn, 1, sanitize($folderid));

    $current = encrypt($conn, 1, subsalt(id()));

    if (isset($_POST['create'])){
        $result = $conn->prepare( "INSERT INTO core_folders (file_name, folder_fav, user_id, dir_id, file_type, cwd, trash) VALUES ('".$folderid.'-'.salted()."','0','".$usalt."','".id()."', 'Directory', '".NewCwd($conn, 1)."', '0')");
        $result->execute();
        $result->setFetchMode(PDO::FETCH_ASSOC);
    }



//active folder
function active(PDO $conn, $r) {
    $r = select($conn, "SELECT dir_id, file_name FROM core_folders WHERE file_name = :id", [':id' => id()]);

    if ('drives' != $r['dir_id']) {
        $active = DirLocation($conn, 1).UserId($conn, 1 );
    }else {
        $active = subsalt(DirLocation($conn, 1).UserId($conn, 1 ).'/'.$getid.$r['file_name']);
    }

    return $active;

}

//file types
function filetypes($filetype, $icons) {

    if('image/jpg' == $filetype) {
        $icons = 'fa fa-image';
    }else if('image/jpeg' == $filetype) {
        $icons = 'fa fa-image';
    }else if('image/png' == $filetype) {
        $icons = 'fa fa-image';
    }else if('image/gif' == $filetype) {
        $icons = 'fa fa-image';
    }else if('image/ico' == $filetype) {
        $icons = 'fa fa-image';
    }else if('image/bmp' == $filetype) {
        $icons = 'fa fa-image';
    }else if('image/svg+xml' == $filetype) {
        $icons = 'fa fa-image';
    }else if('Directory' == $filetype) {
        $icons = 'fa fa-folder';
    }else if ('text' == $filetype) {
        $icons = 'fa fa-file';
    }else if ('text/odt' == $filetype) {
        $icons = 'fa fa-file';
    }else if ('archive' == $filetype) {
        $icons = 'fa fa-archive';
    }else if ('application/pdf' == $filetype) {
        $icons = 'fas fa-file-pdf';
    }else if ('application/zip' == $filetype) {
        $icons = 'fas fa-archive';
    }else if ('word' == $filetype) {
        $icons = 'fas fa-file-word';
    }else if ('excel' == $filetype) {
        $icons = 'fas fa-file-excel';
    }else if ('video/mp4' == $filetype) {
        $icons = 'fas fa-video';
    }else if ('video/ogg' == $filetype) {
        $icons = 'fas fa-video';
    }else if ('video/webm' == $filetype) {
        $icons = 'fas fa-video';
    }else if ('audio/mpeg' == $filetype) {
        $icons = 'fas fa-music';
    }else if ('audio/ogg' == $filetype) {
        $icons = 'fas fa-music';
    }else if ('audio/x-wav' == $filetype) {
        $icons = 'fas fa-music';
    }else {
        $icons = 'fas fa-file';
    }

    return $icons;
}

//rename files and folders
function renamefiles(PDO $conn, $r) {
$name = encrypt($conn, 1, escape($_GET['name']));


echo ('<div class="modaltop" id="'.salted().'">
<label>Rename</label><span class="close-button" onclick="closeModal();"><i class="fas fa-times-circle"></i></span>
</div>

<div class="acont" id="acont"><input type="text" name="rename" placeholder="'.subsalt($name).'"id="copied" >
<button type="submit" name="renamebtn" class="create renamebtn" value="'.$name.'" onclick="return false;" onmouseover="rnamefiles();"><i class="fas fa-font"></i>rename</button>
</div>');

    if('true' == auth($conn, 1)) {
        if (isset($_POST['name'])) {
            $uid = sanitize($_POST['nid']);
        }else {
            $uid = UserId($conn, 1 );
        }
    }

    if (isset($_POST['name'])) {
        $oldname = encrypt($conn, 1, $_POST['name']);
        $newname = encrypt($conn, 1, $_POST['rename']);
        $newdir = encrypt($conn, 1, subsalt($_POST['rename']));

        $result = $conn->prepare("UPDATE core_folders SET file_name = :newname WHERE file_name = :oldname");
        $result->execute([':newname' => $newname, ':oldname' => $oldname]);
        $result->setFetchMode(PDO::FETCH_ASSOC);

        $result = $conn->prepare("UPDATE core_files SET file_name = :newname WHERE file_name = :oldname");
        $result->execute([':newname' => $newname, ':oldname' => $oldname]);
        $result->setFetchMode(PDO::FETCH_ASSOC);

        $oldfold = subsalt($oldname);
        $newfold = subsalt(id()).'/'.str_replace(" ","_",strtolower($oldfold));

        rename(DirLocation($conn, 1).$uid.'/'.$newfold , DirLocation($conn, 1).$uid.'/'.subsalt(id()).'/'.$newdir);

        $oldcwd = DirLocation($conn, 1).$uid.'/'.$newfold;
        $newcwd = DirLocation($conn, 1).$uid.'/'.subsalt(id()).'/'.$newdir;

        $result = $conn->prepare("UPDATE core_folders SET dir_id = :newname cwd = :newcwd WHERE dir_id = :oldname");
        $result->execute([':newname' => $newname, ':oldname' => $oldname, ':newcwd' => $newcwd]);
        $result->setFetchMode(PDO::FETCH_ASSOC);

        $result = $conn->prepare("UPDATE core_files SET dir_id = :newname, cwd = :newcwd WHERE dir_id = :oldname");
        $result->execute([':newname' => $newname, ':oldname' => $oldname, ':newcwd' => $newcwd]);
        $result->setFetchMode(PDO::FETCH_ASSOC);
    }
}

//delete files
function deletefiles(PDO $conn, $r) {

    if('true' == auth($conn, 1)) {

        UserMatch();

        if(isset($_GET['nid'])) {
            $uid = secure($conn, $_GET['nid']);

        }else {
            $uid = encrypt($conn, 1, UserId($conn, 1));
        }
    }

    if (isset($_POST['deleted'])) {
        $deleted = $_POST['deleted'];
        $uri = $_SERVER['HTTP_HOST'];

        $r = select($conn, "SELECT file_name, cwd, user_id FROM core_folders WHERE file_name= :deleted", [':deleted' => $deleted]);

        $orig = subsalt($r['cwd'].'/'.$r['file_name']);
        $new = DirLocation($conn, 1).$uid.'/trash/'.subsalt($r['file_name']);

        rename($orig, $new);

        $result = $conn->prepare("UPDATE core_folders SET trash='1' WHERE dir_id = :deleted");
        $result->execute([':deleted' => $deleted]);
        $result->setFetchMode(PDO::FETCH_ASSOC);

        $result = $conn->prepare("UPDATE core_folders SET trash='1' WHERE file_name = :deleted");
        $result->execute([':deleted' => $deleted]);
        $result->setFetchMode(PDO::FETCH_ASSOC);


        $r = select($conn, "SELECT file_name, cwd, user_id FROM core_files WHERE file_name= :deleted", [':deleted' => $deleted]);

        $orig = subsalt($r['cwd'].'/'.$r['file_name']);
        $new = DirLocation($conn, 1).$uid.'/trash/'.subsalt($r['file_name']);

        rename($orig, $new);

        $result = $conn->prepare("UPDATE core_files SET trash='1' WHERE dir_id = :deleted");
        $result->execute([':deleted' => $deleted]);
        $result->setFetchMode(PDO::FETCH_ASSOC);

        $result = $conn->prepare("UPDATE core_files SET trash='1' WHERE file_name = :deleted");
        $result->execute([':deleted' => $deleted]);
        $result->setFetchMode(PDO::FETCH_ASSOC);
    }
}

//perm delete
function trashDelete(PDO $conn, $r) {

  if('true' == auth($conn, 1)) {

      UserMatch($conn, 1);

      if(isset($_POST['nid'])) {
          $uid = sanitize($_POST['nid']);

      }else {
          $uid = UserId($conn, 1);
      }
  }

  $fname = implode(',', $_POST['selected']);

  foreach($_POST['selected'] as $filename) {

    $result = $conn->prepare("SELECT file_name, dir_id, user_id, cwd FROM core_folders WHERE file_name = :filename
    AND user_id = :usalt AND trash = '1' UNION ALL SELECT file_name, dir_id, user_id, cwd FROM core_files
    WHERE file_name = :filename AND user_id = :usalt AND trash = '1'");

    $result->execute([':usalt' => $uid, ':filename' => $filename]);
    $result->setFetchMode(PDO::FETCH_ASSOC);

    while($r = $result->fetch()) {
        $directory = escape(encrypt($conn, 1, $r['cwd']));
        $filename = escape(encrypt($conn, 1, $r['file_name']));
        $delfile = $directory.'/'.subsalt($filename);
        $delthumb = $directory.'/'.subsalt('thumb-'.$filename);

        unlink($delfile);
        unlink($delthumb);
        rmdir($delfile);

        update($conn, "DELETE FROM core_files WHERE user_id = :uid AND file_name = :filename", [':uid' => $uid, ':filename' => $filename]);
        update($conn, "DELETE FROM core_folders WHERE user_id = :uid AND file_name = :filename", [':uid' => $uid, ':filename' => $filename]);

    }
  }
}


//empty trash
function emptytrash(PDO $conn, $r) {


  if('true' == auth($conn, 1)) {

      UserMatch($conn, 1);

      if(isset($_POST['nid'])) {
          $uid = sanitize($_POST['nid']);

      }else {
          $uid = UserId($conn, 1);
      }
  }

  $result = $conn->prepare("SELECT file_name, dir_id, user_id, cwd FROM core_folders WHERE user_id = :usalt AND trash = '1' UNION ALL SELECT file_name, dir_id, user_id, cwd FROM core_files WHERE user_id = :usalt AND trash = '1'");
  $result->execute([':usalt' => $uid]);
  $result->setFetchMode(PDO::FETCH_ASSOC);

  while ($r = $result->fetch()) {

      $directory = escape(encrypt($conn, 1, $r['cwd']));
      $filename = escape(encrypt($conn, 1, $r['file_name']));
      $trash = escape(encrypt($conn, 1, $r['dir_id']));
      $delfile = $directory.'/'.subsalt($filename);
      $delthumb = $directory.'/'.subsalt('thumb-'.$filename);

      unlink($delfile);
      unlink($delthumb);
      rmdir($delfile);
  }

  update($conn, "DELETE FROM core_files WHERE trash = :trash AND user_id = :uid", [':trash' => '1', ':uid' => $uid]);
  update($conn, "DELETE FROM core_folders WHERE trash = :trash AND user_id = :uid", [':trash' => '1', ':uid' => $uid]);
}


//delete and restore files
function restoreFiles(PDO $conn, $r) {

  if('true' == auth($conn, 1)) {

      UserMatch($conn, 1);

      if(isset($_POST['nid'])) {
          $uid = sanitize($_POST['nid']);

      }else {
          $uid = UserId($conn, 1);
      }
  }

  $trash = $_POST['trash'];

  foreach($_POST['selected'] as $filename) {
    update($conn, "UPDATE core_files SET trash = :trash WHERE user_id = :uid AND file_name = :filename", [':trash' => $trash, ':uid' => $uid, ':filename' => $filename]);
    update($conn, "UPDATE core_folders SET trash = :trash WHERE user_id = :uid AND file_name = :filename", [':trash' => $trash, ':uid' => $uid, ':filename' => $filename]);
  }
}

//move files
function moveFiles(PDO $conn, $r) {

  if('true' == auth($conn, 1)) {

      UserMatch($conn, 1);

      if(isset($_POST['nid'])) {
          $uid = sanitize($_POST['nid']);

      }else {
          $uid = UserId($conn, 1);
      }
  }

  $filename = $_POST['move'];

  foreach($_POST['selected'] as $rename) {

    $result = $conn->prepare("SELECT file_name, user_id, cwd FROM core_folders WHERE user_id = :usalt AND dir_id = :id AND trash = '0' AND file_name = :fname UNION ALL SELECT file_name, dir_id, user_id, cwd FROM core_files WHERE user_id = :usalt AND dir_id = :id AND trash = '0' AND file_name = :fname");
    $result->execute([':usalt' => $uid, ':id' => $id, ':fname' => $filename]);
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();

    $oldname = $r['cwd'].'/'.subsalt($r['file_name']);
    $newname = $_POST['moved'];

    rename($oldname, $newname);

  }

}

//delete plugin
function removeplug($delplug) {
    $files = glob($delplug . '/*');

    foreach ($files as $file) {
        is_dir($file) ? removeplug($file) : unlink($file);
    }

    rmdir($delplug);

}

if (isset($_POST['delplug'])) {
    $deleted = $_POST['delplug'];

    $delplug = $root.'plugins/'.strtolower($deleted);
    //echo $delplug;
    removeplug($delplug);

    update($conn, "DELETE FROM core_plugins WHERE plugin = :deleted", [':deleted' => $deleted]);

    unlink($delfile);
}


/*

2. Directory functions

*/

//display files
function displayfiles(PDO $conn, $r, $dispfav) {

    if('true' == auth($conn, 1)) {
        if(isset($_POST['nid'])) {
            $uid = sanitize($_POST['nid']);
        }else {
            $uid = UserId($conn, 1);
        }
    }

    $r = select($conn, "SELECT disp_type FROM core_users WHERE usalt = :usalt", [':usalt' => $uid]);

    $grid = $r['disp_type'];

        if (isset($_POST['grid'])) {
            $grid = 'gridview';
        }

        if (isset($_POST['list'])) {
            $grid = 'listview';
        }

    $result = $conn->prepare( "UPDATE core_users SET disp_type='$grid' WHERE usalt = :usalt");
    $result->execute([':usalt' => $uid]);
    $result->setFetchMode(PDO::FETCH_ASSOC);

        if (IsMobile()) {

            echo '<div class="files">';
            dispgrid($conn, 1, $dispfav);
            echo '</div>';
            pagination($conn, 1);

        }else if($dispfav == '3'){

            echo('<form id="delete"  method="post" class="selector" onsubmit="return false;">');
            deleteoptions($conn, 1, $dispfav);
            echo '<div class="files">';
            dispgrid($conn, 1, $dispfav);
            echo '</div></form><div class="pag">';
            echo '</div>';

        }else {

            if('gridview' == $grid) {

                listoptions($conn, 1, $dispfav);
                echo '<div class="files">';
                dispgrid($conn, 1, $dispfav);
                echo '</div><div class="pag">';
                pagination($conn, 1);
                echo '</div>';

            }else if ('listview' == $grid) {

                echo('<form id="delete"  method="post" class="selector" onsubmit="return false;">');
                listoptions($conn, 1, $dispfav);
                echo '<div class="files">';
                displist($conn, 1, $dispfav);
                echo '</div></form><div class="pag">';
                pagination($conn, 1);
                echo '</div>';

            }
        }

        if ('listview' == $grid) {
            $view = 'searchl();';
        }else if ('gridview' == $grid) {
            $view = 'searchg();';
        }
}


//heirarchy links
function dirlink(PDO $conn, $r) {

    if('true' == auth($conn, 1)) {

        UserMatch();

        if(isset($_GET['nid'])) {

            $id = encrypt($conn, 1, id());
            $uid = secure($conn,$_GET['nid']);

        }else {
            $id = encrypt($conn, 1, id());
            $uid = encrypt($conn, 1, UserId($conn, 1));
        }
    }

    $r = select($conn, "SELECT disp_type FROM core_users WHERE usalt = :usalt", [':usalt' => $uid]);

    $grid = $r['disp_type'];

    if (isset($_POST['grid'])) {
        $grid = 'gridview';
    }

    if (isset($_POST['list'])) {
        $grid = 'listview';
    }

    if ('listview' == $grid) {
        $links = 'dispfiles();';
        $return = 'listhier()';

    }else if('gridview' == $grid) {
        $links = 'dispgridfiles();';
        $return = 'gridhier()';
    }

    $r = select($conn, "SELECT file_type, dir_id, file_name FROM core_folders WHERE file_name = :id", [':id' => $id]);

    if($r['dir_id']  != 'drives') {
        $getid = subsalt(id()).'/';
    }else {
        $getid = subsalt(id());
    }

    $oldcwd = $r['file_type'];
    $cwd = subsalt($r['dir_id']);
    $currentcwd = $r['file_name'];

    if ('' != $cwd) {
        $prev = '<span class="dirlink"><a value ="'.$r['dir_id'].'" onmouseover="'.$links.'" class="listlinks"><i class="fas fa-folder"></i>'.$cwd.'</a></span><span class="dirbtn"><i class="fas fa-chevron-right"></i></span>';
    }

    if('Directory' == $oldcwd) {
        $newcwd = subsalt(DirLocation($conn, 1).$uid.'/'.$cwd.'/'.$currentcwd);
        $linkname ='<span class="dirlink"><a onmouseover="'.$return.'" class="returnbtn"><i class="fas fa-home"></i>Root</a></span><span class="dirbtn"><i class="fas fa-chevron-right"></i></span>'.$prev.'<span class="dirlink"><a value="'.$id.'" onmouseover="'.$links.'" class="listlinks"><i class="fas fa-folder"></i>'.subsalt($currentcwd).'</a></span>';

    }else {
        $newcwd = DirLocation($conn, 1).$uid;
        $linkname = '<span class="dirlink"><a onmouseover="'.$return.'" class="returnbtn"><i class="fas fa-home"></i>Root</a></span>';
    }

    return $linkname;
}

//display folders
function dispfolders(PDO $conn, $r) {

    if('true' == auth($conn, 1)) {
        $usalt = UserId($conn, 1);
    }

    $r = select($conn, "SELECT disp_type FROM core_users WHERE usalt = :usalt", [':usalt' => $uid]);

    $grid = $r['disp_type'];

    if (isset($_POST['grid'])) {
        $grid = 'gridview';
    }

    if (isset($_POST['list'])) {
        $grid = 'listview';
    }

    if ('listview' == $grid) {
        $links = 'dispfiles();';
    }else if('gridview' == $grid) {
        $links = 'dispgridfiles();';
    }

    echo'<button class="accordion" onclick="accordion(this);"><i class="fa fa-folder" aria-hidden="true"></i>Your Folders</button>';
    echo '<div class="panel">';
    echo '<ul class="side">';

    $result = $conn->prepare("SELECT file_name FROM core_folders WHERE user_id = :usalt AND dir_id NOT LIKE 'trash%' ORDER BY id DESC");
    $result->execute([':usalt' => $usalt]);
    $result->setFetchMode(PDO::FETCH_ASSOC);

    //while( $r = select($conn, "SELECT file_name FROM core_folders WHERE user_id = :usalt AND dir_id NOT LIKE 'trash%' ORDER BY id DESC", [':usalt' => $usalt]) ) {

    while ($r = $result->fetch()) {
        $folderdisp = str_replace("_"," ",strtolower(subsalt($r['file_name'])));
        echo sprintf('<li class="dir">
        <a value="'.$r['file_name'].'" class="listlinks" onmouseover="'.$links.'"><i id="sidi"class="fa fa-folder" aria-hidden="true"></i>'.$folderdisp.'</a></li>');
    }

    echo '</ul></div>';
}


//display favorite folders
function dispfavfolders(PDO $conn, $r) {

    if('true' == auth($conn, 1)) {

        UserMatch();

        if(isset($_GET['nid'])) {

            $uid = secure($conn,$_GET['nid']);

        }else {
            $uid = encrypt($conn, 1, UserId($conn, 1));
        }
    }

    $r = select($conn, "SELECT disp_type FROM core_users WHERE usalt = :usalt", [':usalt' => $uid]);

    $grid = $r['disp_type'];

    if (isset($_POST['grid'])) {
        $grid = 'gridview';
    }

    if (isset($_POST['list'])) {
        $grid = 'listview';
    }

    if ('listview' == $grid) {
        $links = 'dispfiles();';
    }else if('gridview' == $grid) {
        $links = 'dispgridfiles();';
    }

    echo'<button class="accordion" onclick="accordion(this);"><i class="fa fa-star" aria-hidden="true"></i>Your Favorites</button>';
    echo '<div class="panel">';
    echo '<ul class="side">';

    $result = $conn->prepare("SELECT file_name FROM core_folders WHERE folder_fav='1' AND user_id = :usalt AND dir_id NOT LIKE 'trash%' ORDER BY id DESC");
    $result->execute([':usalt' => $uid]);
    $result->setFetchMode(PDO::FETCH_ASSOC);

        while ($r = $result->fetch()) {
            $folderdisp = str_replace("_"," ",strtolower(subsalt($r['file_name'])));
            echo sprintf('<li class="dir">
            <a value="'.$r['file_name'].'" class="listlinks" onmouseover="'.$links.'"><i id="sidi"class="fa fa-folder" aria-hidden="true"></i>'.$folderdisp.'</a></li>');
        }

    echo '</ul></div>';
}

//display recent files
function recentfiles(PDO $conn, $r) {
    if('true' == auth($conn, 1)) {
        $usalt = sanitize(UserId($conn, 1));
    }

    echo'<button class="accordion" onclick="accordion(this);"><i class="fas fa-history" aria-hidden="true"></i>Recent Files</button>';
    echo '<div class="panel">';
    echo '<ul class="side">';

    $result = $conn->prepare("SELECT file_name, file_type FROM core_files WHERE user_id = :usalt AND dir_id NOT LIKE 'trash%' ORDER BY reg_date DESC LIMIT 5");
    $result->execute([':usalt' => $usalt]);
    $result->setFetchMode(PDO::FETCH_ASSOC);

        while ($r = $result->fetch()) {

        $filetype = $r['file_type'];

            if('image/png' == $r['file_type']){
                $dirload = "popupc(this);";
            }else if('image/gif' == $r['file_type']){
                $dirload = "popupc(this);";
            }else if('image/ico' == $r['file_type']){
                $dirload = "popupc(this);";
            }else if('image/svg+xml' == $r['file_type']){
                $dirload = "popupc(this);";
            }else if('image/bmp' == $r['file_type']){
                $dirload = "popupc(this);";
            }else if('image/jpg' == $r['file_type']){
                $dirload = "popupc(this);";
            }else if('image/jpeg' == $r['file_type']){
                $dirload = "popupc(this);";
            }else if('video/mp4' == $r['file_type']){
                $dirload = "popupv(this);";
            }else if('video/ogg' == $r['file_type']){
                $dirload = "popupv(this);";
            }else if('video/webm' == $r['file_type']){
                $dirload = "popupv(this);";
            }else if('audio/mpeg' == $r['file_type']){
                $dirload = "popupa(this);";
            }else if('audio/ogg' == $r['file_type']){
                $dirload = "popupa(this);";
            }else if('audio/x-wav' == $r['file_type']){
                $dirload = "popupa(this);";
            }else if($r['file_type'] == 'application/pdf'){
                    $dirload = "popupdf(this);";
            }else {
                $dirload = "";
            }

        $folderdisp = str_replace("_"," ",strtolower(subsalt($r['file_name'])));
        echo sprintf('<li class="dir">
        <a value="'.$r['file_name'].'" onclick="'.$dirload.'"><i id="sidi" class="'.filetypes($filetype, $icons).'" aria-hidden="true"></i>'.$folderdisp.'</a></li>');
        }

    echo '</ul></div>';
}


//plugins
function plugins(PDO $conn, $r, $plugin, $root) {

    $r = select($conn, "SELECT setting FROM core_options WHERE options = :url", [':url' => 'installPath']);
    $path =$r['setting'];

    $result = $conn->prepare("SELECT active, icon, url, plugin, mobile FROM core_plugins");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);

        while ($r = $result->fetch()) {

            $active = escape(encrypt($conn,1, $r['active']));
            $icon = escape(encrypt($conn,1, $r['icon']));
            $url = escape(encrypt($conn,1, $r['url']));
            $pluginname = escape(encrypt($conn,1, $r['plugin']));

            $link = '<i><img src="'.$root.'plugins/'.$pluginname.'/'.$icon.'" width="45px" height="60px" /></i>';

            if ('1' == $active) {
                if ('' != $icon) {
                    $icon = $r['icon'];
                }else {
                    $icon = '<i class="fas fa-plug"></i>';
                }

                if (IsMobile()) {
                    if ('true' == $r['mobile']) {
                        echo '<li class="'.$plugin.'"><a href="'.$root.strtolower($url).'" class="nav">'.strtolower($link).'</a></li>';
                    }
                }else {
                    echo '<li class="'.$plugin.'"><a href="'.$root.strtolower($url).'" class="nav">'.strtolower($link).'</a></li>';
                }

            }else if ('0' == $active){
                echo '';
            }
        }

}

//search
function search(PDO $conn, $r) {

    if('true' == auth($conn, 1)) {
        $uid = UserId($conn, 1);
    }

    $r = select($conn, "SELECT disp_type FROM core_users WHERE usalt = :usalt", [':usalt' => $uid]);

    $grid = $r['disp_type'];

        if (isset($_POST['grid'])) {
            $grid = 'gridview';
        }

        if (isset($_POST['list'])) {
            $grid = 'listview';
        }

        if ('listview' == $grid) {
            if (IsMobile()) {
                $view = 'searchg();';
            }else {
                $view = 'searchl();';
            }
        }else if ('gridview' == $grid) {
            $view = 'searchg();';
        }

    return $view;
}

//get file icons
function geticon(PDO $conn, $r) {

    $r = select($conn, "(SELECT :ftype FROM core_files) UNION (SELECT :ftype FROM core_folders)", [':ftype' => 'file_type']);
}

//parent directory
function parentdir(PDO $conn, $r, $par, $id, $disptype) {
    $cwd = NewCwd($conn, 1);

    $r = select($conn, "SELECT dir_id, file_type FROM core_folders WHERE file_name =  :id", [':id' => $id]);

    if (IsMobile()) {
        $dirload = 'onmouseover="loadmobilegrid();"';
        $dirreturn = 'onmouseover="returnmobilegrid();"';

    }else if ('list' == $disptype) {
        $dirload = 'onmouseover="loaddir();"';
        $dirreturn = 'onmouseover="returnlist();"';

    }else if ('grid' == $disptype) {
        $dirload = 'onmouseover="loadgrid();"';
        $dirreturn = 'onmouseover="returngrid();"';
    }

    if(is_dir(subsalt($cwd.'/'.$r['file_name']))) {

        if('drives' != $id) {
            echo sprintf('<div class="gridwrap"><div class="columnd column" id="grida" value="'.$r['dir_id'].'" '.$dirload.'><i class="fa fa-level-up" aria-hidden="true"></i> <span class="name">Parent directory</span></div></div>');
        }

    }else if('drives' != $id) {

        if('0' == $par) {
            echo sprintf('<div class="gridwrap"><div class="columnd column" id="grida" value="'.$r['dir_id'].'" '.$dirload.'><i class="fa fa-level-up" aria-hidden="true"></i> <span class="name">Parent directory</span></div></div>');
        }

    }

    if('' == $r['dir_id']) {

       if('drives' != id()) {
         if ('2' != $par){
           echo sprintf('<div class="gridwrap"><div class="columnd column" id="grida" '.$dirreturn.'><i class="fas fa-angle-left" aria-hidden="true"></i> <span class="name">Return</span></div></div>');
        }
      }
    }
}

//delete options
function deleteoptions(PDO $conn, $r, $dispfav) {

    echo ('<div class="listoptions latestoptions">');
    echo('<button type="submit" form="delete" onclick="trashDelete();" name="deletebtn" class="listviewbtn" ><i class="fas fa-trash-alt"></i>Delete</button>');
    echo('<button type="submit" form="delete" onclick="restoreFiles();" name="restore" id="restore" value="0" class="listviewbtn" ><i class="fas fa-trash-restore-alt"></i>Restore</button>');
    echo('<button type="submit" form="delete" onclick="emptytrash();" name="empty" class="listviewbtn" ><i class="fas fa-minus-circle"></i>Empty trash</button>');
    echo('</div>');
}

//listview options
function listoptions(PDO $conn, $r, $dispfav) {

  if('true' == auth($conn, 1)) {

      UserMatch();

      if(isset($_GET['nid'])) {

          $id = encrypt($conn, 1, id());
          $uid = sanitize(escape($_GET['nid']));

      }else {
          $id = encrypt($conn, 1, id());
          $uid = UserId($conn, 1);
      }
  }

  if ('2' == $dispfav) {
      echo(' <div class="latestwrap">');
      displatest($conn, 1);

      echo ('</div><div class="listoptions">');
  }else {
      echo ('<div class="listoptions latestoptions">');
  }

  $r = select($conn, "SELECT disp_type FROM core_users WHERE usalt = :usalt", [':usalt' => $uid]);

  if (isset($_POST['grid'])) {
    $grid = 'gridview';

  }else if (isset($_POST['list'])) {
    $grid = 'listview';

  }else {
    $grid = $r['disp_type'];
  }

  if('listview' == $grid) {
    $delete = 'onclick="deleteList();"';

  }else if('gridview' == $grid) {
    $delete = 'onclick="deleteGrid();"';
  }

    echo('<button type="submit" '.$delete.' name="deletebtn" id="del" value="1" class="listviewbtn" ><i class="fas fa-trash-alt"></i>Delete</button>');
    echo('<button type="submit" form="delete" onclick="submit();" name="move" class="listviewbtn" ><i class="fas fa-external-link-alt"></i>Move</button>');
    //echo('<button class="listviewbtn" ><i class="fa fa-share-alt"></i>Share</button></div>');
    echo('</div>');

}


//thumbtype
function thumbs(PDO $conn, $r, $thumbtype) {
    $r = select($conn, "SELECT setting FROM core_options WHERE options = :itype", [':itype' => 'icontype']);

    $thumbtype = $r['setting'];

    return $thumbtype;
}

//number of files per page
function dispnum(PDO $conn, $r) {
    $r = select($conn, "SELECT setting FROM core_options WHERE options = :dispnum", [':dispnum' => 'dispnum']);

    $types = $r['setting'];

    return $types;
}

//photo gallery
function gallery(PDO $conn, $r) {
    $cwd = NewCwd($conn, 1);
    $disp = dispnum($conn, 1);
    $loc = '';
    //$sorted = escape('ORDER BY reg_date ASC');

    if('true' == auth($conn, 1)) {

        UserMatch();

        if(isset($_GET['nid'])) {
            $id = encrypt($conn, 1, id());
            $uid = secure($conn,$_GET['nid']);

        }else {
            $id = encrypt($conn, 1, id());
            $uid = UserId($conn, 1);
        }

        if(id() == 'all') {
            $loc = '';
        }else {
            $loc = "AND dir_id = '".$id."'";
        }

    }

    if (isset($_GET['sorted'])) {
        $sorted = escape($_GET['sorted']);
    }else {
        $sorted = 'reg_date';
    }

    $search = encrypt($conn, 1, sanitize($_POST['query']));

    echo '<div id="'.$id.'" value="'.$sorted.'" class="row gallery">';

    if (isset($_GET['sortlink'])) {

        if ($_GET['sortlink'] == 'file_name') {
            $ordern = '<i class="fas fa-caret-down"></i>';
            $sorted = sanitize(escape($_GET['sortlink']).' ASC');
        }

        else if ($_GET['sortlink'] == 'reg_date') {
            $orderd = '<i class="fas fa-caret-down"></i>';
            $sorted = sanitize(escape($_GET['sortlink']).' ASC');
        }

        else if ($_GET['sortlink'] == 'file_type') {
            $ordert = '<i class="fas fa-caret-down"></i>';
            $sorted = sanitize(escape($_GET['sortlink']).' ASC');
        }

        else if ($_GET['sortlink'] == 'file_size') {
            $orders = '<i class="fas fa-caret-down"></i>';
            $sorted = sanitize(escape($_GET['sortlink']).' ASC');
        }

    }

    $dispfav = '2';
    if('2' == $dispfav) {
        $result = $conn->prepare("SELECT file_name, file_type, cwd FROM core_files WHERE file_type LIKE '%image%' AND user_id = :usalt ".$loc." AND trash = '0' ORDER BY ".$sorted." ");
        $result->execute([':usalt' => $uid]);
        $result->setFetchMode(PDO::FETCH_ASSOC);
    }

    while ($r = $result->fetch()) {

        $directory = $r['cwd'];
        $filetype = $r['file_type'];

        $getid = subsalt(id()).'/';

        if('image/png' == $r['file_type']){
            $size = $filesize;
            $dirload = "popupc(this);";

        }else if('image/gif' == $r['file_type']){
            $size = $filesize;
            $dirload = "popupc(this);";

        }else if('image/ico' == $r['file_type']){
            $size = $filesize;
            $dirload = "popupc(this);";

        }else if('image/svg+xml' == $r['file_type']){
            $size = $filesize;
            $dirload = "popupc(this);";

        }else if('image/bmp' == $r['file_type']){
            $size = $filesize;
            $dirload = "popupc(this);";

        }else if('image/jpg' == $r['file_type']){
            $size = $filesize;
            $dirload = "popupc(this);";

        }else if('image/jpeg' == $r['file_type']) {
            $size = $filesize;
            $dirload = "popupc(this);";
        }

        $current = encrypt($conn, 1, subsalt(id()));
        $img = encrypt($conn, 1, subsalt($directory.'/'.$r['file_name']));

        $thumbs = thumbs($conn, 1, $thumbtype);

        list($width, $height) = getimagesize($img);

        if(EnableThumbs($conn, 1) == 'Enable') {
            $src = $img;
            $dest = subsalt($directory.'/thumb-'.$r['file_name']);
            $desired_width = '300';
        }else {
            $dest = $img;
        }

        if (!file_exists($dest)){
            thumbnail($src, $dest, $desired_width);
        }

        if ($width > $height) {
        $imgsize = 'class="ih"';
        }else if ($width < $height) {
        $imgsize = 'class="iw"';
        }else if ($width == $height) {
        $imgsize = 'class="iw"';
        }

        if ('Icons' == $thumbs) {
            $thumbnails = '<i class="'.filetypes($filetype, $icons).'" aria-hidden="true"></i>';
        }else if('Thumbnails' == $thumbs) {

            if($r['file_type'] == 'image/png') {
                $thumbnails = '<img src="'.$dest.'"  '.$imgsize.' />';
            }else if($r['file_type'] == 'image/jpg') {
                $thumbnails = '<img src="'.$dest.'"  '.$imgsize.' />';
            }else if($r['file_type'] == 'image/jpeg') {
                $thumbnails = '<img src="'.$dest.'"  '.$imgsize.' />';
            }else if($r['file_type'] == 'image/gif') {
                $thumbnails = '<img src="'.$dest.'"  '.$imgsize.' />';
            }else if($r['file_type'] == 'image/svg+xml') {
                $thumbnails = '<img src="'.$img.'"  class="ih" />';
            }else if($r['file_type'] == 'image/ico') {
                $thumbnails = '<img src="'.$dest.'"  '.$imgsize.' />';
            }else if($r['file_type'] == 'image/bmp') {
                $thumbnails = '<img src="'.$dest.'"  '.$imgsize.' />';
            }else{
                $thumbnails = '<i class="'.filetypes($filetype, $icons).'" aria-hidden="true"></i>';
            }

        }

        $folderdisp = str_replace("_"," ",strtolower(subsalt($r['file_name'])));

        if (strlen($folderdisp) > 49){
            $maxLength = 45;
            $folderdisp = substr($folderdisp, 0, $maxLength).'...';
        }

        echo ('<div class="galwrap" value="'.$r['file_name'].'" onclick="'.$dirload.'"><div class="column" id="grida"">'.$thumbnails.'</div>');
        echo('<div class="imginfo"><p>'.subsalt($r['file_name']).'</p></div></div>');
    }

    echo '  </div>';

    $r = selectSearch($conn, "SELECT COUNT(*) AS countf FROM core_files WHERE file_type LIKE 'image%' AND user_id = :usalt $loc AND trash = :trash", [':usalt' => $uid, ':trash' => '0']);
    $countf = $count + $r['countf'];

    if ($countf == '0') {
        if ($dispfav != '0') {
            echo '<div class="empty"><i class="fas fa-image"></i>';
            echo '<h2>The Photo gallery is empty!</h2>';
            echo '<p>There no photos uploaded onto your drive.</p></div>';
        }
    }
}


//gallery folders
function galleryfolders(PDO $conn, $r) {

    if('true' == auth($conn, 1)) {

        UserMatch();

        if(isset($_GET['nid'])) {
            $id = encrypt($conn, 1, id());
            $uid = secure($conn,$_GET['nid']);

        }else {
            $uid = encrypt($conn, 1, UserId($conn, 1));
        }
    }

    echo('<form id="dirform" enctype="multipart/form-data" method="post"><select class="btn" name="location" id="loc" onchange="loadlocation();">');

    $result = $conn->prepare("SELECT file_name, dir_id, user_id, cwd FROM core_folders WHERE user_id = :uid");
    $result->execute([':uid' => $uid]);
    $result->setFetchMode(PDO::FETCH_ASSOC);

    $loc = 'all';

    if(isset($_GET['location'])) {
        if($_GET['location'] != 'drives') {
            $loc = subsalt($_GET['location']);
        }

        else if($_GET['location'] == 'drives') {
            $loc = 'root';
        }

        else if ($_GET['location'] == 'all') {
            $loc = 'all';
        }
    }

    echo $loc;


    echo('<option value="'.$loc.'">'.$loc.'</option><option value="all">All</option><option value="drives">Root</option>');

        while ($r = $result->fetch()) {
            echo("<option value='".$r['file_name']."'>".subsalt($r['file_name'])."</option>");
        }

    echo('</select></form>');

}

//display grid
function dispgrid(PDO $conn, $r, $dispfav) {
    $cwd = NewCwd($conn, 1);
    $disp = dispnum($conn, 1);
    $search = encrypt($conn, 1, sanitize($_POST['query']));

    if('' != $search) {
        $par = '1';
    }else if('3' == $dispfav){
      $par = '2';
    }else {
        $par = '0';
    }

    $sorted = 'file_name';

    if (isset($_GET['sorted'])) {
        $sorted = sanitize(escape($_GET['sorted']));
    }

    if('true' == auth($conn, 1)) {

        UserMatch();

        if(isset($_GET['nid'])) {

            $id = encrypt($conn, 1, id());
            $uid = sanitize(escape($_GET['nid']));

        }else {
            $id = encrypt($conn, 1, id());
            $uid = UserId($conn, 1);
        }

        if(isset($_POST['nid'])) {
            if('0' == $dispfav) {
                $uid = sanitize(escape($_POST['nid']));
            }
        }
    }

    if ('3' == $dispfav) {
        $id = 'drives';
    }

    $disptype = 'grid';
    echo '<div class="row gridview" id="'.$id.'" value="'.$sorted.'">';
    echo parentdir($conn, 1, $par, $id, $disptype);

    if(isset($_POST['sortby'])) {
        $sorted = $_POST['sortby'];
    }


    $start = '0';

    if(isset($_POST['next'])) {
        $start = $_POST['next'];
    }

    if(isset($_POST['prev'])) {
        $start = $_POST['prev'] ;
    }


    if('2' == $dispfav) { //for all
        $result = $conn->prepare("SELECT file_name, file_type, cwd FROM core_folders WHERE file_name LIKE '%".$search."%' AND user_id = :usalt AND dir_id = :id AND trash = '0' UNION ALL SELECT file_name, file_type, cwd FROM core_files WHERE file_name LIKE '%".$search."%' AND user_id = :usalt AND dir_id = :id AND trash= '0' ORDER BY ".$sorted." LIMIT ".$start." , ".$disp."");
        $result->execute([':usalt' => $uid, ':id' => $id]);
        $result->setFetchMode(PDO::FETCH_ASSOC);

    }else if('1' == $dispfav) { //for favorites
        $result = $conn->prepare("SELECT file_name, file_type, cwd FROM core_folders WHERE file_name LIKE '%".$search."%' AND user_id = :usalt AND dir_id = :id AND folder_fav = '1' UNION ALL SELECT file_name, file_type, cwd FROM core_files WHERE file_name LIKE '%".$search."%' AND user_id = :usalt AND dir_id = :id AND folder_fav= '1' ORDER BY ".$sorted." LIMIT ".$start." , ".$disp."");
        $result->execute([':usalt' => $uid, ':id' => $id]);
        $result->setFetchMode(PDO::FETCH_ASSOC);

    }else if('0' == $dispfav) { //for search
        $result = $conn->prepare("SELECT file_name, file_type, cwd FROM core_folders WHERE file_name LIKE '%".$search."%' AND user_id = :usalt UNION ALL SELECT file_name, file_type, cwd FROM core_files WHERE file_name LIKE '%".$search."%' AND user_id = :usalt");
        $result->execute([':usalt' => $uid]);
        $result->setFetchMode(PDO::FETCH_ASSOC);

    }else if('3' == $dispfav) { //for trash
        $result = $conn->prepare("SELECT file_name, file_type, cwd FROM core_folders WHERE file_name LIKE '%".$search."%' AND user_id = :usalt AND trash = '1' UNION ALL SELECT file_name, file_type, cwd FROM core_files WHERE file_name LIKE '%".$search."%' AND user_id = :usalt AND trash = '1'");
        $result->execute([':usalt' => $uid]);
        $result->setFetchMode(PDO::FETCH_ASSOC);
    }

    if (IsMobile()) {
        $click = 'onclick=';
    }else {
        $click = 'ondblclick=';
    }

    while ($r = $result->fetch()) {

        $directory = $r['cwd'];
        $filetype = $r['file_type'];

        $getid = subsalt(id()).'/';

        if(is_dir(subsalt($directory.'/'.$r['file_name']))) {
            $size = $foldersize;

            if (IsMobile()) {
                $dirload = 'onmouseover="loadmobilegrid();"';
                $click = 'onclick=';
            }else {
                $dirload = 'onmouseover="loadgrid();"';
                $click = 'ondblclick=';
            }

        $column = 'columng';
        $newcwd = subsalt(DirLocation($conn, 1).UserId($conn, 1 ).'/'.$r['file_name'].'/'.id());

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

          }else if($r['file_type'] == 'application/pdf'){
                $size = $filesize;
                $dirload = $click.'"popupdf(this);"';
                $column = 'column';

        }else {
            $size = $filesize;
            $dirload = "";
            $column = 'column';
        }

        if (!IsMobile()) {
        $select = '<label class="checkthis"><input type="checkbox" value ="'.$r['file_name'].'" name="selected[]" onclick="sel();" class="deletebox"/><span class="checkmark"></span></label>';
        }

        $current = encrypt($conn, 1, subsalt(id()));
        $img = encrypt($conn, 1, subsalt($directory.'/'.$r['file_name']));

        if(EnableThumbs($conn, 1) == 'Enable') {
            $src = $img;
            $dest = subsalt($directory.'/thumb-'.$r['file_name']);
            $desired_width = '300';
        }else {
            $dest = $img;
        }

        if (!file_exists($dest)){
            thumbnail($src, $dest, $desired_width);
        }

        $thumbs = thumbs($conn, 1, $thumbtype);

        if ('Icons' == $thumbs) {
            $thumbnails = '<i class="'.filetypes($filetype, $icons).'" aria-hidden="true"></i>';
        }else if('Thumbnails' == $thumbs) {

            if($r['file_type'] == 'image/png') {
                $thumbnails = '<img src="'.$dest.'"  style="max-height:100px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/jpg') {
                $thumbnails = '<img src="'.$dest.'"  style="max-height:100px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/jpeg') {
                $thumbnails = '<img src="'.$dest.'"  style="max-height:100px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/gif') {
                $thumbnails = '<img src="'.$dest.'"  style="max-height:100px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/svg+xml') {
                $thumbnails = '<img src="'.$img.'"  style="max-height:100px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/ico') {
                $thumbnails = '<img src="'.$dest.'"  style="max-height:100px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/bmp') {
                $thumbnails = '<img src="'.$dest.'"  style="max-height:100px; display:block; margin:auto;" />';
            }else{
                $thumbnails = '<i class="'.filetypes($filetype, $icons).'" aria-hidden="true"></i>';
            }

        }

        $folderdisp = str_replace("_"," ",strtolower(subsalt($r['file_name'])));

        if (strlen($folderdisp) > 49){
            $maxLength = 45;
            $folderdisp = substr($folderdisp, 0, $maxLength).'...';
        }

        echo ('<div class="gridwrap"><div class="'.$column.'" value="'.$r['file_name'].'" id="grida" '.$dirload.'">');
        echo $select;
        echo ($thumbnails.$folderdisp.'</div></div>');
        }

    echo '  </div>';

    if('3' == $dispfav) {
      $id = 'trash';
    }

    if (fileCount($conn, 1, '0', $uid, $id) == '0') {
        if ($dispfav != '0') {
          if ($dispfav != '3') {
            echo '<div class="empty"><i class="fas fa-folder-open"></i>';
            echo '<h2>This folder is empty</h2>';
            echo '<p>You can upload files and folders here.</p></div>';
          }
        }
    }

    if (trashCount($conn, 1, $uid) == '0') {
      if ($dispfav == '3') {
        echo '<div class="empty"><i class="fa fa-trash"></i>';
        echo '<h2>The trash is empty</h2>';
      }
    }
}


//latest files
function displatest(PDO $conn, $r) {
    $cwd = NewCwd($conn, 1);
    $disp = dispnum($conn, 1);

    $search = $_POST['query'];

    if('true' == auth($conn, 1)) {

        UserMatch();

        if(isset($_GET['nid'])) {

            $id = encrypt($conn, 1, id());
            $uid = secure($conn, $_GET['nid']);

        }else {
            $id = encrypt($conn, 1, id());
            $uid = encrypt($conn, 1, UserId($conn, 1));
        }
    }

    echo '<div id="'.$id.'" class="row gridview latest">';
    echo '<span>Recent Files</span>';

    $dispfav = '2';
    if('2' == $dispfav) {
        $result = $conn->prepare("SELECT file_name, file_type, cwd FROM core_files WHERE trash = '0' AND user_id = :usalt ORDER BY reg_date DESC LIMIT 5 ");
        $result->execute([':usalt' => $uid]);
        $result->setFetchMode(PDO::FETCH_ASSOC);
    }

    while ($r = $result->fetch()) {

        $directory = $r['cwd'];
        $filetype = $r['file_type'];

        $getid = subsalt(id()).'/';

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

        }else if($r['file_type'] == 'application/pdf'){
            $size = $filesize;
            $dirload = "popupdf(this);";

        }else {
            $size = $filesize;
            $dirload = "";
        }

        $current = encrypt($conn, 1, subsalt(id()));
        $img = encrypt($conn, 1, subsalt($directory.'/'.$r['file_name']));

        if(EnableThumbs($conn, 1) == 'Enable') {
            $src = $img;
            $dest = subsalt($directory.'/thumb-'.$r['file_name']);
            $desired_width = '300';
        }else {
            $dest = $img;
        }

        if (!file_exists($dest)){
            thumbnail($src, $dest, $desired_width);
        }

        $thumbs = thumbs($conn, 1, $thumbtype);

        if ('Icons' == $thumbs) {
            $thumbnails = '<i class="'.filetypes($filetype, $icons).'" aria-hidden="true"></i>';
        }else if('Thumbnails' == $thumbs) {

            if($r['file_type'] == 'image/png') {
                $thumbnails = '<img src="'.$dest.'"  style="max-height:50px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/jpg') {
                $thumbnails = '<img src="'.$dest.'"  style="max-height:50px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/jpeg') {
                $thumbnails = '<img src="'.$dest.'"  style="max-height:50px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/gif') {
                $thumbnails = '<img src="'.$dest.'"  style="max-height:50px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/svg+xml') {
                $thumbnails = '<img src="'.$img.'"  style="max-height:50px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/ico') {
                $thumbnails = '<img src="'.$dest.'"  style="max-height:50px; display:block; margin:auto;" />';
            }else if($r['file_type'] == 'image/bmp') {
                $thumbnails = '<img src="'.$dest.'"  style="max-height:50px; display:block; margin:auto;" />';
            }else{
                $thumbnails = '<i class="'.filetypes($filetype, $icons).'" aria-hidden="true"></i>';
            }

        }

        $folderdisp = str_replace("_"," ",strtolower(subsalt($r['file_name'])));

        if (strlen($folderdisp) > 49){
            $maxLength = 45;
            $folderdisp = subsalt($folderdisp, 0, $maxLength).'...';
        }

        echo sprintf('<div class="gridwrap"><div class="column" value="'.$r['file_name'].'" id="grida"" ondblclick="'.$dirload.'">'.$thumbnails.$folderdisp.'</div></div>');
    }

    echo '  </div>';
}


//display list
function displist(PDO $conn, $r, $dispfav) {

    $disp = dispnum($conn, 1);

    if('0' == $dispfav) {
        $cwd = $r['dir_id'];
    }else {
        $cwd = NewCwd($conn, 1);
    }

    $search = encrypt($conn, 1, sanitize($_POST['query']));

    if (isset($_GET['sorted'])) {
        $sorted = escape($_GET['sorted']);
    }else {
        $sorted = 'reg_date';
    }

    if($search != '') {
        $par = '1';
    }else {
        $par = '0';
    }

    if('true' == auth($conn, 1)) {

        UserMatch();

        if(isset($_GET['nid'])) {

            $id = encrypt($conn, 1, id());
            $uid = secure($conn,$_GET['nid']);

        }else {
            $id = encrypt($conn, 1, id());
            $uid = encrypt($conn, 1, UserId($conn, 1));
        }

        if(isset($_POST['nid'])) {
            if($dispfav == '0') {
                $uid = sanitize(escape($_POST['nid']));
            }
        }
    }

    if(isset($_POST['sortby'])) {
        $sorted = $_POST['sortby'];
    }

    $start = '0';

    if(isset($_POST['next'])) {
        $start = $_POST['next'];
    }

    if(isset($_POST['prev'])) {
        $start = $_POST['prev'] ;
    }

    $asd = 'ASC';

    if (isset($_GET['sortlink'])) {

        if ('ASC' == $_GET['asc']) {
            $asd = 'DESC';
        }else if ('DESC' == $_GET['asc']) {
            $asd = 'ASC';
        }

        if ($_GET['sortlink'] == 'file_name') {

            if ('ASC' == $asd) {
                $ordern = '<i class="fas fa-caret-down"></i>';
            }else if ('DESC' == $asd) {
                $ordern = '<i class="fas fa-caret-up"></i>';
            }

            $activen ='sortactive';

            $sorted = sanitize(escape($_GET['sortlink']).' '.$asd);
        }

        else if ($_GET['sortlink'] == 'reg_date') {

            if ('ASC' == $asd) {
                $orderd = '<i class="fas fa-caret-down"></i>';
            }else if ('DESC' == $asd) {
                $orderd = '<i class="fas fa-caret-up"></i>';
            }

            $actived ='sortactive';

            $sorted = sanitize(escape($_GET['sortlink']).' '.$asd);
        }

        else if ($_GET['sortlink'] == 'file_type') {

            if ('ASC' == $asd) {
                $ordert = '<i class="fas fa-caret-down"></i>';
            }else if ('DESC' == $asd) {
                $ordert = '<i class="fas fa-caret-up"></i>';
            }

            $activet='sortactive';

            $sorted = sanitize(escape($_GET['sortlink']).' '.$asd);
        }

        else if ($_GET['sortlink'] == 'file_size') {

            if ('ASC' == $asd) {
                $orders = '<i class="fas fa-caret-down"></i>';
            }else if ('DESC' == $asd) {
                $orders = '<i class="fas fa-caret-up"></i>';
            }

            $actives ='sortactive';

            $sorted = sanitize(escape($_GET['sortlink']).' '.$asd);
        }

    }

    echo('<div id="'.$id.'" class="row listview" value="'.$sorted.'"><div class="column-top list" id="'.$asd.'">');
    echo('<label class="checkthis"><input type="checkbox" value ="'.$r['file_name'].'" name="selected[]" onclick="selectAll();" class="selectAll"/><span class="checkmark"></span></label>');
    echo('<span class="otherwideleft"></span><span class="sort name '.$activen.'" value="file_name" onmouseover="sortlink();">Name'.$ordern.'</span>');
    echo('<span class="sort date '.$actived.'" value="reg_date" onmouseover="sortlink();">Date'.$orderd.'</span>');
    echo('<span value="file_type" class="sort otherwide '.$activet.'" onmouseover="sortlink();">File type'.$ordert.'</span>');
    echo('<span class="other"></span><span value="file_size" class="sort otherwide '.$actives.'" onmouseover="sortlink();">Size'.$orders.'</span>');
    echo('</div>');

    $disptype = 'list';
    echo parentdir($conn, 1, $par,$id, $disptype);

    if('2' == $dispfav) { //for all
        $result = $conn->prepare("SELECT file_name, reg_date, file_type, folder_fav, file_size, dir_id, user_id, cwd FROM core_folders WHERE user_id = :usalt AND dir_id = :id AND trash = '0' UNION ALL SELECT file_name, reg_date,  file_type, folder_fav, file_size, dir_id, user_id, cwd FROM core_files WHERE user_id = :usalt AND dir_id = :id AND trash = '0' ORDER BY ".$sorted." LIMIT ".$start." , ".$disp."");
        $result->execute([':usalt' => $uid, ':id' => $id]);
        $result->setFetchMode(PDO::FETCH_ASSOC);

    }else if('1' == $dispfav) { //for favorites
        $result = $conn->prepare("SELECT file_name, reg_date, file_type, folder_fav, file_size, dir_id, user_id, cwd FROM core_folders WHERE user_id = :usalt AND dir_id = :id AND folder_fav='1' AND trash = '0' UNION ALL SELECT file_name, reg_date,  file_type, folder_fav, file_size, dir_id, user_id, cwd FROM core_files WHERE user_id = :usalt AND dir_id = :id AND folder_fav='1' AND trash = '0' ORDER BY ".$sorted." LIMIT ".$start." , ".$disp."");
        $result->execute([':usalt' => $uid, ':id' => $id]);
        $result->setFetchMode(PDO::FETCH_ASSOC);

    }else if('0' == $dispfav) { //for search
        $result = $conn->prepare("SELECT file_name, reg_date, file_type, folder_fav, file_size, dir_id, user_id, cwd FROM core_folders WHERE file_name LIKE '%".$search."%' AND user_id = :usalt UNION ALL SELECT file_name, reg_date, file_type, folder_fav, file_size, dir_id, user_id, cwd FROM core_files WHERE file_name LIKE '%".$search."%' AND user_id = :usalt");
        $result->execute([':usalt' => $uid]);
        $result->setFetchMode(PDO::FETCH_ASSOC);
    }

    while ($r = $result->fetch()) {

        $directory = $r['cwd'];
        $filetype = $r['file_type'];

        if (1 == $r['folder_fav']) {
            $faved = 'favbtnactive';
        }else {
            $faved = 'favbtn';
        }


        if('drives' == $id) {
            $loc ='';
        }else {
            $loc =subsalt($r['dir_id']).'/';
        }

        $url = encrypt($conn, 1, subsalt($directory.'/'.$r['file_name']));
        //$url = encrypt($conn, 1, $directory.'/'.dataHash($r['file_name']));
        $aniout = "$('#aniout').show();";

        if($r['file_type'] != 'Directory') {
            $download = '<a href="'.$url.'" class="detbtn" download><i class="fa fa-download"></i><span>Download</span></a>';

        }else if($r['file_type'] == 'Directory') {
            $download ='<button type="submit" name="dl" form="dl"  value="'.subsalt($r['file_name']).'" class="detbtn" onclick="'.$aniout.'"><i class="fa fa-download"></i><span>Download</span></button>';
        }

        if($r['dir_id']  != 'drives') {
            $current = encrypt($conn, 1, subsalt($r['dir_id']).'/');

        }else {
            $current = encrypt($conn, 1, subsalt($r['dir_id']));
        }

        $foldname = encrypt($conn, 1, $r['file_name']);
        $folderdisp = encrypt($conn, 1, str_replace("_"," ",strtolower(subsalt($r['file_name']))));
        $filedisp = encrypt($conn, 1, str_replace("_"," ",strtolower(substr($r['file_name'], 0, -10))));
        $foldersize = encrypt($conn, 1, format(DirSize(subsalt($directory.'/'.$r['file_name']))));
        $filesize = encrypt($conn, 1, format($r['file_size']));
        $img = encrypt($conn, 1, subsalt($directory.'/'.$r['file_name']));

        if(EnableThumbs($conn, 1) == 'Enable') {
            $src = $img;
            $dest = subsalt($directory.'/thumb-'.$r['file_name']);
            $desired_width = '300';
        }else {
            $dest = $img;
        }

        if (!file_exists($dest)){
            thumbnail($src, $dest, $desired_width);
        }

        $dlfile = encrypt($conn, 1, $_POST['dl'].'.zip');
        $pathsource = encrypt($conn, 1, $directory.'/'.$_POST['dl'].'/');

        if($r['file_type'] != 'Directory') {
            $imgdisp = 'imgdisp';
        }else if($r['file_type'] == 'Directory') {
            $imgdisp = '';
        }

        if(is_dir(subsalt(DirLocation($conn, 1).dataHash(UserId($conn, 1 )).'/'.$current.'/'.$r['file_name']))) {
            $name = str_replace("_"," ",strtolower(subsalt($r['file_name'])));
        }else if(is_file(subsalt(DirLocation($conn, 1).dataHash(UserId($conn, 1 )).'/'.$current.'/'.$r['file_name']))) {
            $name = str_replace("_"," ",strtolower(substr($r['file_name'], 0, -10)));
        }

        if($r['dir_id']  != 'drives') {
            $getid = subsalt($id, 0, -6).'/';
        }else {
            $getid = subsalt($id, 0, -6);
        }


        if(is_dir(subsalt($directory.'/'.$r['file_name']))) {
            $size = $foldersize;
            $dirload = 'onmouseover="loaddir();"';
            $column = 'column columnd';
            $newcwd = substr(DirLocation($conn, 1).dataHash(UserId($conn, 1 )).'/'.$r['file_name'].'/'.$id);

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

        }else if($r['file_type'] == 'application/pdf'){
              $size = $filesize;
              $dirload = 'ondblclick="popupdf(this);"';
              $column = 'column';

        }else {
            $size = $filesize;
            $dirload = '"';
            $column = 'column';
        }

        $thumbs = thumbs($conn, 1, $thumbtype);

        if ('Icons' == $thumbs) {
            $thumbnails = '<i class="'.filetypes($filetype, $icons).'" aria-hidden="true"></i>';
        }else if('Thumbnails' == $thumbs) {

            if($r['file_type'] == 'image/png') {
                $thumbnails = '<div class="imgwrap"><img src="'.$dest.'" style="max-height:48px; width: auto; height: 100%;" /></div>';
            }else if($r['file_type'] == 'image/gif') {
                $thumbnails = '<div class="imgwrap"><img src="'.$dest.'" style="max-height:48px; width: auto; height: auto;" /></div>';
            }else if($r['file_type'] == 'image/svg+xml') {
                $thumbnails = '<div class="imgwrap"><img src="'.$img.'" style="max-height:48px; width: auto; height: 100%;" /></div>';
            }else if($r['file_type'] == 'image/bmp') {
                $thumbnails = '<div class="imgwrap"><img src="'.$dest.'" style="max-height:48px; width: auto; height: 100%;" /></div>';
            }else if($r['file_type'] == 'image/ico') {
                $thumbnails = '<div class="imgwrap"><img src="'.$dest.'" style="max-height:48px; width: auto; height: 100%;" /></div>';
            }else if($r['file_type'] == 'image/jpg') {
                $thumbnails = '<div class="imgwrap"><img src="'.$dest.'" style="max-height:48px; width: auto; height: 100%;" /></div>';
            }else if($r['file_type'] == 'image/jpeg') {
                $thumbnails = '<div class="imgwrap"><img src="'.$dest.'" style="max-height:48px; width: auto; height: 100%;" /></div>';
            }else{
                $thumbnails = '<i class="'.filetypes($filetype, $icons).'" aria-hidden="true"></i>';
            }

        }

        echo ('<div class="'.$column.'" value="'.$r['file_name'].'" id="grida" '.$dirload.'>');
        echo ('<label class="checkthis"><input type="checkbox" value ="'.$r['file_name'].'" name="selected[]" onclick="sel();" class="deletebox"/><span class="checkmark"></span></label>');
        echo ('<button type="submit" value="'.$r['file_name'].'" class="addfav '.$faved.'" name="fav" onclick="addfav(this);" id="'.$r['dir_id'].'"><i class="fa fa-star"></i></button>');
        echo ($thumbnails.'<input type="text" class="filename" placeholder="'.$folderdisp.'" size="" name="rename" onload="resizeInput();" readonly>');
        echo ('<span class="date">'.substr($r['reg_date'], 0, -8).'</span>');
        echo ('<span class="otherwide">'.$r['file_type'].'</span>');

        echo ('<div class="detailsdown">
        <button type="button" class="listbtn"><i class="fa fa-ellipsis-h"></i></button>
        <div class="details-content"><button type="submit" value="'.$r['file_name'].'" form="details" class="share detbtn" name="shared" onclick="popup(this);"><i class="fa fa-share-alt"></i><span>Share</span></button><button type="submit" name="detbtn" class="detbtn" form="details" value="'.$r['file_name'].'" onclick="infodet(this);"><i class="fa fa-info"></i><span>Details</span></button><button value="'.$r['file_name'].'" class="detbtn rnamebtn" type="button" onclick="renamepopup(this)"><i class="fas fa-font"></i><span>Rename</span></button><button value ="'.$r['file_name'].'" name="deleted" class="deletebtn detbtn" type="submit" id="'.$r['dir_id'].'" onmouseover="deletefiles(this);"><i class="fa fa-trash"></i><span>Delete</span></button>'.$download.'</div>
        </div>');

        echo ('<span class="otherwide">'.$size.'</span></button></div>');
    }

    echo '</div>';

    if (isset($_POST['dl'])) {
        zip($dlfile, $pathsource);
        echo '<script type="text/javascript">';
        echo 'location.href = "'.$dlfile.'"; ';
        echo '</script>';
    }

    if (fileCount($conn, 1, '0', $uid, $id) == '0') {
        if ($dispfav != '0') {
            echo '<div class="empty"><i class="fas fa-folder-open"></i>';
            echo '<h2>This folder is empty</h2>';
            echo '<p>You can upload files and folders here.</p></div>';
        }
    }

    //var_dump($_SESSION['token']);
}

//pagination
function pagination(PDO $conn, $r) {

    if('true' == auth($conn, 1)) {

        UserMatch();

        if(isset($_GET['nid'])) {

            $id = encrypt($conn, 1, id());
            $uid = secure($conn,$_GET['nid']);

        }else {
            $id = encrypt($conn, 1, id());
            $uid = encrypt($conn, 1, UserId($conn, 1));
        }
    }

    $r = select($conn, "SELECT COUNT(*) AS count FROM core_folders WHERE user_id = :usalt AND dir_id = :id", [':usalt' => $uid, ':id' => $id]);
    $count = $r['count'];

    $r = select($conn, "SELECT COUNT(*) AS countf FROM core_files WHERE user_id = :usalt AND dir_id = :id", [':usalt' => $uid, ':id' => $id]);
    $countf = $count + $r['countf'];

    $r = select($conn, "SELECT setting FROM core_options WHERE options = :dispnum", [':dispnum' => 'dispnum']);
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

    $r = select($conn, "(SELECT user_id, file_name, dir_id, cwd FROM core_folders where file_name = :share)
    UNION (SELECT user_id, file_name, dir_id, cwd FROM core_files where file_name = :share)", [':share' => $share]);

    if($r['dir_id']  != 'drives') {
        $current = encrypt($conn, 1, subsalt($r['dir_id']).'/');

    }else {
        $current = encrypt($conn, 1, subsalt($r['dir_id']));
    }

    $link = substr($r['cwd'].'/'.$share, 2, -6);
    $uri = $_SERVER['HTTP_HOST'].substr($_SERVER['REQUEST_URI'], 0, -9);
    $dirlink = '/drive/folders?id='.$share;

    if(is_dir(subsalt($r['cwd'].'/'.$r['file_name']))) {
        $sharevalue = 'http://'.$uri.$dirlink;
    }else {
        $sharevalue = 'http://'.$uri.$link;
    }

    echo '<div class="modaltop">
    <label>share</label><span class="close-button" onclick="closeModal();"><i class="fas fa-times-circle"></i></span></div>
    <div class="acont" id="acont"><input type="text" name="folder" value="'.$sharevalue.'" id="copied" readonly>
    <button onclick="copy()" type="button" class="create"><i class="fas fa-copy"></i>copy</button></div>';
}


//htacces
function htaccess(PDO $conn, $r) {
    $filename = '../.htaccess';
    $htfile = file_get_contents($filename);

    if(isset($_POST['save'])) {
      $data = encrypt($conn, 1, $_POST['htaccess']);
      $open = fopen($filename, 'w');
      $write = fwrite($open, $data);
      fclose($open);
    }

    echo '<div class="modaltop">
    <label>Edit htaccess</label><span class="close-button" onclick="closeModal();"><i class="fas fa-times-circle"></i></span></div>
    <div class="acont" id="acont"><textarea name="htaccess" style="width:650px; height:500px;">'.$htfile.'</textarea>
    <button type="submit" class="create" name="save"><i class="far fa-save"></i> save</button></div>';
}


//display image modal
function imgdisp(PDO $conn, $r) {
    $imgmodal = $_POST['imgm'];

    $r = select($conn, "SELECT user_id, dir_id, cwd  FROM core_files where file_name = :imgmodal", [':imgmodal' => $imgmodal]);

    $directory = $r['cwd'];

    if($r['dir_id']  != 'drives') {
        $current = encrypt($conn, 1, subsalt($r['dir_id']).'/');

    }else {
        $current = encrypt($conn, 1, subsalt($r['dir_id']));
    }

    $image = encrypt($conn, 1, subsalt($directory.'/'.$imgmodal));


    if (IsMobile()) {
        echo '<div class="acont" id="acont"><img src="'.$image.'" class="imgmodal" /></div>';

    }else {
        echo '<div class="modaltop">
        <label>'.subsalt($imgmodal).'</label><span class="close-button" onclick="closeModal();"><i class="fas fa-times-circle"></i></span></div>
        <div class="acont" id="acont"><img src="'.$image.'" class="imgmodal" /></div>
        <a href="'.$image.'" class="modalbtn" download><i class="fa fa-download"></i><span>Download</span></a>';
    }
}

//display video modal
function videodisp(PDO $conn, $r) {
    $videomodal = $_POST['videom'];

    $r = select($conn, "SELECT user_id, dir_id, cwd FROM core_files where file_name = :videomodal", [':videomodal' => $videomodal]);
    $directory = $r['cwd'];

    if($r['dir_id']  != 'drives') {
        $current = encrypt($conn, 1, subsalt($r['dir_id']).'/');

    }else {
        $current = encrypt($conn, 1, subsalt($r['dir_id']));
    }

    $video = encrypt($conn, 1, subsalt($directory.'/'.$videomodal));

    if (IsMobile()) {
        $w = '100%';
        $h = 'auto';
    }else {
        $w = '800';
        $h = '600';
    }

    echo '<div class="modaltop">
    <label>'.subsalt($videomodal).'</label><span class="close-button" onclick="closeModal();"><i class="fas fa-times-circle"></i></span></div>
    <div class="acont" id="acont"><video id="media" width="'.$w.'" height="'.$h.'" controls><source src="'.$video.'"></video></div>';
}

//display audio modal
function audiodisp(PDO $conn, $r) {
    $audiomodal = $_POST['audiom'];

    $r = select($conn, "SELECT user_id, dir_id, cwd FROM core_files where file_name = :audiomodal", [':audiomodal' => $audiomodal]);

    $directory = $r['cwd'];

    if($r['dir_id']  != 'drives') {
        $current = encrypt($conn, 1, subsalt($r['dir_id']).'/');

    }else {
        $current = encrypt($conn, 1, subsalt($r['dir_id']));
    }

    $audio = encrypt($conn, 1, subsalt($directory.'/'.$audiomodal));

    if (IsMobile()) {
        $w = '300';
        $h = '60';
    }else {
        $w = '680';
        $h = '120';
    }

    echo '<div class="modaltop">
    <label>'.subsalt($audiomodal).'</label><span class="close-button" onclick="wavesurfer.destroy(); closeModal();"><i class="fas fa-times-circle"></i></span></div>

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

//pdf viewer
function pdfdisp(PDO $conn, $r) {

  $pdfmodal = $_POST['pdfm'];

  $r = select($conn, "SELECT user_id, dir_id, cwd  FROM core_files where file_name = :pdfmodal", [':pdfmodal' => $pdfmodal]);

  $directory = $r['cwd'];

  if($r['dir_id']  != 'drives') {
      $current = encrypt($conn, 1, subsalt($r['dir_id']).'/');

  }else {
      $current = encrypt($conn, 1, subsalt($r['dir_id']));
  }

  $pdf = encrypt($conn, 1, subsalt($directory.'/'.$pdfmodal));


  if (IsMobile()) {
      echo '<div class="acont" id="acont"><img src="'.$image.'" class="imgmodal" /></div>';

  }else {
      echo '<div class="modaltop">
      <label>'.subsalt($pdfmodal).'</label><span class="close-button" onclick="closeModal();"><i class="fas fa-times-circle"></i></span></div>
      <div class="acont" id="acont"><object data="'.$pdf.'" width="800" height=650"" type="application/pdf"></object></div>
      <a href="'.$pdf.'" class="modalbtn" download><i class="fa fa-download"></i><span>Download</span></a>';
  }
}

//add to favorites
function addfav(PDO $conn, $r) {
    $r = select($conn, "SELECT folder_fav FROM core_folders WHERE file_name = :fav", [':fav' => $_POST['fav']]);

    $addfav = $r['folder_fav'];

    if(isset($_POST['fav'])) {

        if ('0' == $addfav) {
            $result = $conn->prepare("UPDATE core_folders SET folder_fav='1'  WHERE file_name = :fav");
            $result->execute([':fav' => $_POST['fav']]);
            $result->setFetchMode(PDO::FETCH_ASSOC);

        }else if('1' == $addfav) {
            $result = $conn->prepare("UPDATE core_folders SET folder_fav='0'  WHERE file_name = :fav");
            $result->execute([':fav' => $_POST['fav']]);
            $result->setFetchMode(PDO::FETCH_ASSOC);
        }
    }

    $r = select($conn, "SELECT folder_fav FROM core_files WHERE file_name = :fav", [':fav' => $_POST['fav']]);

    $addfav = $r['folder_fav'];

    if(isset($_POST['fav'])) {

        if ('0' == $addfav) {
          $result = $conn->prepare("UPDATE core_files SET folder_fav='1'  WHERE file_name = :fav");
          $result->execute([':fav' => $_POST['fav']]);
          $result->setFetchMode(PDO::FETCH_ASSOC);

        }else if('1' == $addfav) {
          $result = $conn->prepare("UPDATE core_files SET folder_fav='0'  WHERE file_name = :fav");
          $result->execute([':fav' => $_POST['fav']]);
          $result->setFetchMode(PDO::FETCH_ASSOC);
        }
    }
}


//display details
function displaydet(PDO $conn, $r) {

    $info = $_POST['detfile'];

    $r = select($conn, "(SELECT file_name, reg_date, file_type, folder_fav, file_size, user_id, dir_id, cwd FROM core_folders WHERE file_name = :info)
    UNION (SELECT file_name, reg_date, file_type, folder_fav, file_size, user_id, dir_id, cwd FROM core_files WHERE file_name = :info)", [':info' => $info]);

    $uid = $r['user_id'];

    $directory = $r['cwd'];

    if($r['file_type'] == 'Directory') {
        $filename = $r['file_name'];
        $filesize = format(DirSize(subsalt($directory.'/'.$r['file_name'])));
    }else {
        $filename = $r['file_name'];
        $filesize = format($r['file_size']);
    }

    if( $r['dir_id'] == 'drives') {
        $location = 'root';
    }else {
        $location = subsalt($r['dir_id']);
    }

    $current = encrypt($conn, 1, subsalt($r['dir_id']));
    $img = encrypt($conn, 1, subsalt($directory.'/'.$r['file_name']));

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


    if (isset($_POST['detfile'])) {

        echo '<div id="title"><h2><i class="'.filetypes($filetype, $icons).'" aria-hidden="true"></i>'.subsalt($filename).'</h2></div>';

        echo '<div class="preview">'.$thumbnails.'</div>';

        echo '<div class="filecont"><span>Name</span>'.subsalt($filename).'<br />
        <span>Type</span>'.$r['file_type'].'<br />
        <span>Size</span>'.$filesize.'<br />
        <span>Location</span>'.$location.'<br />
        <span>Date created</span>'.substr($r['reg_date'], 0, -8).'<br />';

        $r = select($conn, "SELECT core_username FROM core_users WHERE usalt= :uid", [':uid' => $uid]);

        echo '<span>Owner</span>'.$r['core_username'].'<br />
        </div>';
    }else {
        echo '<div class="infoimg"><i class="fas fa-info" aria-hidden="true"></i></div>';
    }
}

?>
