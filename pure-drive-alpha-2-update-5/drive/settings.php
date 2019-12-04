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

//space left
$left = disk_free_space("../");
$total = disk_total_space("../");
$du = $total - $left;

//percentage of disk used - this will be used to also set the width % of the progress bar
$dp = sprintf('%.2f',($du / $total) * 100);

$left = format($left);
$du = format($du);
$total = format($total);
?>



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
            <input type="file" name="logo" id="file" class="logoup"><label for="file" style="background:url('<?php echo($r['setting']); ?>') !important; background-size:cover !important; margin-top:0px;"><i class="fa fa-plus"></i></label>

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
                    $( "#aniout" ).show();
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
                    $( "#aniout" ).show();
                };
            </script>

            </form>
            </div>
        </div>
        <?php
        echo '<form class="form" id="delbg" method="post">';

            ui::button($conn, '', 'button', 'Remove', 'submit', '');

        echo '</form>';


        if(isset($_POST['Remove'])) {
            $result = $conn->prepare("UPDATE core_options SET setting='' WHERE options ='background'");
            $result->execute();
            $result->setFetchMode(PDO::FETCH_ASSOC);
            header("Location: settings");
        }

        echo '<form class="form" id="formed" method="post">';

        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'theme']);

        ui::label($conn, 'THEME:');
        echo '<select name="theme">';

            ui::inputOption($conn, $r['setting']);
            ui::dynamicOption($conn, '../req/css/themes/');

        echo '</select>';


        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'lang']);

        ui::label($conn, 'LANGUAGE:');
        echo '<select name="lang">';

            ui::inputOption($conn, $r['setting']);
            ui::inputOption($conn, 'English');

        echo '</select>';


        ui::h3($conn, 'Directory Settings');


        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'directory']);

        ui::label($conn, 'FILE DIRECTORY:');

            ui::inputText($conn, $r['setting'], 'directory', 'text', '');


        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'uploadSize']);

        ui::label($conn, 'MAX UPLOAD SIZE:');
        echo '<span>size in megabytes</span>';

            ui::inputText($conn, $r['setting'], 'size', 'text', '');


        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'dispnum']);

        ui::label($conn, 'DISPLAY AMMOUNT:');
        echo '<select name="display">';

            ui::inputOption($conn, $r['setting']);
            ui::inputOption($conn, '10');
            ui::inputOption($conn, '20');
            ui::inputOption($conn, '50');
            ui::inputOption($conn, '100');

        echo '</select>';

        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'supported']);

        ui::label($conn, 'SUPPORTED FILE TYPES:');
        echo '<span>separated by commas</span>';

            ui::arrayText($conn, $r['setting'], 'types', 'text', '');


        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'search']);

        ui::label($conn, 'ALLOW SEARCH ENGINES:');
        echo '<select name="robots">';

            ui::inputOption($conn, $r['setting']);
            ui::inputOption($conn, 'yes');
            ui::inputOption($conn, 'no');

        echo '</select>';

        ui::h3($conn, 'Thumbnail Settings');

        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'icontype']);

        ui::label($conn, 'DISPLAY TYPE:');
        echo '<select name="icontype">';

            ui::inputOption($conn, $r['setting']);
            ui::inputOption($conn, 'Thumbnails');
            ui::inputOption($conn, 'Icons');

        echo '</select>';


        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'gd']);

        ui::label($conn, 'CREATE THUMBNAILS:');
        echo '<span>GD module required</span><select name="gd">';

            ui::inputOption($conn, $r['setting']);
            ui::inputOption($conn, 'Enable');
            ui::inputOption($conn, 'Disable');

        echo '</select>';

        ui::submitBtn($conn, 'Update', 'button', 'update');

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

            $supported = encrypt($conn, 1, inputArray($_POST['types']));
            $r = update($conn, "UPDATE core_options SET setting = :setting WHERE options = :option", [':option' => 'supported', ':setting' => $supported]);

            $theme = encrypt($conn, 1, inputArray($_POST['theme']));
            $r = update($conn, "UPDATE core_options SET setting = :setting WHERE options = :option", [':option' => 'theme', ':setting' => $theme]);

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
