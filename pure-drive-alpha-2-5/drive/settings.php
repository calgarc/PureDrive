<?php
define('func', TRUE);

$root = '../';
require '../req/headers.php';

$searchable = 'false';
$settings = 'active';
require '../req/index.php';

IsSession();
loggedin($root);
?>
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


    <div class="setting">
        <h3>Disk Usage</h3>
        <div class='usage'>
            <div class='bartext'><?php echo $dp; ?>% Used</div>
        </div>
    </div>


    <div class="userinfo">
        <div class='theme' ><h3>Theme Settings</h3></div>

        <div class="form">

        <?php
        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'logo']);
        ?>

        <label>LOGO</label><span>240x45px recommended</span>
            <div class="uplogo">

            <form method="post" enctype="multipart/form-data" id="logoform">
            <input type="file" name="logo" id="file" class="logoup"><label for="file" style="background:url('<?php echo($r['setting']); ?>') !important; background-size:cover !important;"><i class="fa fa-plus"></i></label>

            <?php
            if(isset($_FILES['logo'])){
                $errors= array();
                $file_name = $_FILES['logo']['name'];
                $file_size =$_FILES['logo']['size'];
                $file_tmp =$_FILES['logo']['tmp_name'];
                $file_type=$_FILES['logo']['type'];
                $file_ext=strtolower(end(explode('.',$_FILES['logo']['name'])));

                $extensions= array("jpeg","jpg","png");

                if(in_array($file_ext,$extensions)=== false){
                    $errors[]="extension not allowed, please choose a JPEG or PNG file.";
                }

                if($file_size > 4194304){
                    $errors[]='File size must be less then 4 MB';
                }

                if(empty($errors)==true){
                    move_uploaded_file($file_tmp,"../drive/profile/".$file_name);

                    $result = $conn->prepare("UPDATE core_options SET setting='../drive/profile/$file_name' WHERE options ='logo'");
                    $result->execute();
                    $result->setFetchMode(PDO::FETCH_ASSOC);
                    header("Location: settings");
                }else{
                    print_r($errors);
                }
            }
            ?>

            <script>
                document.getElementById("file").onchange = function() {
                    document.getElementById("logoform").submit();
                };
            </script>

            </form>
            </div>
        </div>

        <div class="form">

        <?php
        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'background']);
        ?>

            <label>LOGIN BACKGROUND</label><span>1600x1200px recommended</span>
            <div class="uplogo">

            <form method="post" enctype="multipart/form-data" id="bgform">
            <input type="file" name="background" id="bgfile" class="logoup"><label for="bgfile" style="background:url('<?php echo($r['setting']); ?>') !important; background-size:cover !important; margin:0px !important; height:100% !important;"><i class="fa fa-plus"></i></label>

            <?php
            if(isset($_FILES['background'])){
                $errors= array();
                $file_name = $_FILES['background']['name'];
                $file_size =$_FILES['background']['size'];
                $file_tmp =$_FILES['background']['tmp_name'];
                $file_type=$_FILES['background']['type'];
                $file_ext=strtolower(end(explode('.',$_FILES['background']['name'])));

                $extensions= array("jpeg","jpg","png");

                if(in_array($file_ext,$extensions)=== false){
                    $errors[]="extension not allowed, please choose a JPEG or PNG file.";
                }

                if($file_size > 4194304){
                    $errors[]='File size must be excately 4 MB';
                }

                if(empty($errors)==true){
                    move_uploaded_file($file_tmp,"../drive/profile/".$file_name);

                    $result = $conn->prepare("UPDATE core_options SET setting='../drive/profile/$file_name' WHERE options ='background'");
                    $result->execute();
                    $result->setFetchMode(PDO::FETCH_ASSOC);
                    header("Location: settings");
                }else{
                    print_r($errors);
                }
            }
            ?>

            <script>
                document.getElementById("bgfile").onchange = function() {
                    document.getElementById("bgform").submit();
                };
            </script>

            </form>
            </div>
        </div>

        <form class="form" id="delbg" method="post">
        <button type="submit" value='' name="delbg" class="button"> Remove</button>
        </form>

        <?php
        if(isset($_POST['delbg'])) {
            $result = $conn->prepare("UPDATE core_options SET setting='' WHERE options ='background'");
            $result->execute();
            $result->setFetchMode(PDO::FETCH_ASSOC);
            header("Location: settings");
        }
        ?>

        <form class="form" id="formed" method="post">

        <?php
        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'theme']);
        ?>

        <label>THEME</label><select name="theme"> <option value="<?php echo($r['setting']); ?>"><?php echo($r['setting']); ?></option><option value="Pure v1">Pure v1</option></select>

        <?php
        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'lang']);
        ?>

        <label>LANGUAGE</label><select name="lang"> <option value="<?php echo($r['setting']); ?>"><?php echo($r['setting']); ?></option><option value="English">English</option></select>

        <h3>Directory Settings</h3>

        <?php
        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'directory']);
        ?>

        <label>FILE DIRECTORY</label><input type="text" name="directory" id="directory" value="<?php echo($r['setting']);  ?>" >

        <?php
        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'uploadSize']);
        ?>

        <label>MAX UPLOAD SIZE</label><span>size in megabytes</span><input type="text" name="size" id="size" value="<?php echo($r['setting']);  ?>">

        <?php
        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'dispnum']);

        $dispnum = $r['setting'];
        ?>

        <label>DISPLAY AMMOUNT</label><select class="btn" name="display" id="display"><option value="<?php echo($r['setting']); ?>"><?php echo($r['setting']); ?> per page</option><option value="10">10 per page</option>><option value="20">20 per page</option>><option value="50">50 per page</option><option value="100">100 per page</option></select>

        <?php
        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'supported']);

        $types = $r['setting'];
        ?>

        <label>SUPPORTED FILE TYPES</label><span>separated by commas</span><input type="text" name="types" id="types" value="<?php echo($r['setting']);  ?>" >

        <?php
        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'search']);
        ?>

        <label>ALLOW SEARCH ENGINES</label><select name="robots"> <option value="<?php echo($r['setting']); ?>"><?php echo($r['setting']); ?><option value="yes">yes</option><option value="no">no</option></select>

        <h3>Thumbnail Settings</h3>

        <?php
        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'icontype']);
        ?>

        <label>DISPLAY TYPE</label><select name="icontype"> <option value="<?php echo($r['setting']); ?>"><?php echo($r['setting']); ?></option><option value="Thumbnails">Thumbnails</option><option value="Icons">Icons</option></select>

        <?php
        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'gd']);
        ?>

        <label>CREATE THUMBNAILS</label><span>php GD module required</span><select name="gd"> <option value="<?php echo($r['setting']); ?>"><?php echo($r['setting']); ?></option><option value="Enable">Enable</option><option value="Disable">Disable</option></select>

        <input type="submit" value="Update" name="update" class="button">

        <?php
        if (isset($_POST['update'])) {

            $directory = encrypt($conn, 1, $_POST['directory']);
            $r = update($conn, "UPDATE core_options SET setting = :setting WHERE options = :option", [':option' => 'directory', ':setting' => $directory]);

            $theme = encrypt($conn, 1, $_POST['theme']);
            $r = update($conn, "UPDATE core_options SET setting = :setting WHERE options = :option", [':option' => 'theme', ':setting' => $theme]);

            $upsize = encrypt($conn, 1, preg_replace('/\D/', '', $_POST['size']));
            $r = update($conn, "UPDATE core_options SET setting = :setting WHERE options = :option", [':option' => 'uploadSize', ':setting' => $upsize]);

            $icontype = encrypt($conn, 1, $_POST['icontype']);
            $r = update($conn, "UPDATE core_options SET setting = :setting WHERE options = :option", [':option' => 'icontype', ':setting' => $icontype]);

            $gd = encrypt($conn, 1, $_POST['gd']);
            $r = update($conn, "UPDATE core_options SET setting = :setting WHERE options = :option", [':option' => 'gd', ':setting' => $gd]);

            $display = encrypt($conn, 1, $_POST['display']);
            $r = update($conn, "UPDATE core_options SET setting = :setting WHERE options = :option", [':option' => 'dispnum', ':setting' => $display]);

            $types = encrypt($conn, 1, preg_replace("/[^a-zA-Z0-9\/_|+ .,-]/", '', $_POST['types']));
            $r = update($conn, "UPDATE core_options SET setting = :setting WHERE options = :option", [':option' => 'supported', ':setting' => $types]);

            if($_POST['robots'] == 'yes') {
                $source = 'User-agent: *
                Disallow: ';
                $allow = encrypt($conn, 1, $source);

            }else if($_POST['robots'] == 'no') {
                $source = 'User-agent: *
                Disallow: /';
                $allow = encrypt($conn, 1, $source);
            }

            $robots = fopen("../robots.txt", "w") or die("Unable to open file!");
            $robup = $allow;
            fwrite($robots, $robup);
            fclose($robots);

            $search = encrypt($conn, 1, $_POST['robots']);
            $r = update($conn, "UPDATE core_options SET setting = :setting WHERE options = :option", [':option' => 'search', ':setting' => $search]);

            header("Location: settings");
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
