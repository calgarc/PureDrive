<?php
define('func', TRUE);

$root = '../';
require '../req/headers.php';

$searchable = 'true';
$users = 'active';
require '../req/index.php';

IsSession($root);
loggedin($root);
restrict($conn, $admin, $username, 1);

if($_GET['id'] != userid($conn, 1)) {
    if('true' != admin($conn, 1)) {
      session_unset();
      session_destroy();
      $login = $root.'login';
      header("Location: $login"); //die();
    }
}

echo '<div id="left"><!--left-->
    <div class="nuser">';

      ui::sideBtn($conn, 'newuser', 'New', 'fa fa-user-plus', 'dropbtn');

echo '</div>';
?>

    <div class="folders">
        <h2>Users</h2>
            <?php
            $users = DispUsers($conn, 1);
            echo $users;
            ?>
    </div>

</div><!--left-->


<div id="right" class="right"><!--right-->
    <div class="userinfo">
        <h3>User Info</h3>

        <?php
        $usercont = encrypt($conn, 1, id());
        $r = select($conn, "SELECT core_avatar, core_username, core_email, core_firstname, core_lastname, user_type, uplimit FROM core_users WHERE usalt= :usalt", [':usalt' => $usercont]);

        $limit = $r['uplimit'];
        ?>


        <div class="avatar">
            <form method="post" enctype="multipart/form-data" id="avform">
            <input type="file" name="image" id="file" class="avup"><label for="file" style="background:url('<?php echo($r['core_avatar']); ?>') !important; background-size:cover !important;"><i class="fa fa-plus"></i></label>

            <?php
            if(isset($_FILES['image'])){
                $errors= array();
                $file_name = $_FILES['image']['name'];
                $file_size =$_FILES['image']['size'];
                $file_tmp =$_FILES['image']['tmp_name'];
                $file_type=$_FILES['image']['type'];
                $file_ext=strtolower(end(explode('.',$_FILES['image']['name'])));

                $extensions= array("jpeg","jpg","png");

                if(in_array($file_ext,$extensions)=== false){
                    $errors[] = 'extension not allowed, please choose a JPEG or PNG file.';
                }

                if($file_size > 4194304) {
                    $errors[] = 'File size must be excately 4 MB';
                }

                if(empty($errors) == true) {
                    move_uploaded_file($file_tmp,"../drive/profile/".$file_name);

                    $result = $conn->prepare("UPDATE core_users SET core_avatar='../drive/profile/$file_name' WHERE usalt='".$usercont."'");
                    $result->execute();
                    $result->setFetchMode(PDO::FETCH_ASSOC);
                    header("Location: updateuser?id=$usercont");
                }
            }
            ?>

            </form>
        </div>

        <script>
            document.getElementById("file").onchange = function() {
                document.getElementById("avform").submit();
                $( "#aniout" ).show();
            };
        </script>

        <?php
        echo '<form class="form" id="formed" method="post">';


        ui::label($conn, 'USERNAME:');
        ui::inputText($conn, $r['core_username'], 'readonly', 'text', '');

        ui::label($conn, 'EMAIL:');
        ui::inputText($conn, $r['core_email'], 'email', 'text', 'Email');

        ui::label($conn, 'FIRST NAME:');
        ui::inputText($conn, $r['core_firstname'], 'fname', 'text', 'First Name');

        ui::label($conn, 'LAST NAME:');
        ui::inputText($conn, $r['core_lastname'], 'lname', 'text', 'Last Name');

        ui::label($conn, 'USER TYPE:');
        echo '<select name="usertype" '.restrictform($conn, $admin, $username, 1).'>';

            ui::inputOption($conn, $r['user_type']);
            ui::inputOption($conn, 'Administrator');
            ui::inputOption($conn, 'Standard');

        echo '</select>';


        ui::label($conn, 'SPACE:');
        echo '<span>number in GB</span>';
        ui::inputRestrict($conn, $limit.' GB', 'space', 'text');

        ui::submitBtn($conn, 'Update', 'button', 'update');


        $useremail = encrypt($conn, 1, $_POST['email']);

        $userlname = encrypt($conn, 1, sanitize($_POST['lname']));

        $userfname = encrypt($conn, 1, sanitize($_POST['fname']));

        if ('true' == admin($conn, 1)) {

          if (isset($_POST['usertype'])){
            $usertype = encrypt($conn, 1, sanitize($_POST['usertype']));
          }

          if (isset($_POST['space'])){
            $uplimit = sanitize($_POST['space']);
            $uplimit = encrypt($conn, 1, str_replace(str_split('gGbB '),"",$uplimit));
          }

        }else {
            $usertype = encrypt($conn, 1, $r['user_type']);
            $uplimit = encrypt($conn, 1, $limit);
        }

            if (isset($_POST['update'])){
                update($conn,  "UPDATE core_users SET core_email= :email, core_firstname= :fname, core_lastname= :lname, user_type= :type, uplimit= :uplimit WHERE usalt= :usalt",
                  [':email' => $useremail, ':fname' => $userfname, ':lname' => $userlname, ':type' => $usertype, ':uplimit' => $uplimit, ':usalt' => $usercont]);

                header("Location: updateuser?id=$usercont");
            }

    echo '</form>
          </div>';


    echo '<div class="userinfo">';

        UI::h3($conn, 'Password');

        echo '<form class="form" method="post">';

          ui::label($conn, 'NEW PASSWORD:');
          ui::inputText($conn, '', 'pass', 'password', 'New Password');

          ui::label($conn, 'REPEAT PASSWORD:');
          ui::inputText($conn, '', 'rpass', 'password', 'Repeat Password');

        $pass =  false;
        if(isset($_POST['pass'])){
            $pass = escape($_POST['pass']);
        }

        $pass = password_hash( $pass, PASSWORD_DEFAULT);
        $user_password = $pass.dataHash($pass);


        if ($_POST['pass']!= $_POST['rpass']) {
            echo('<span class="oops">Your password did not match. Try again.</span>');

        }elseif (isset($_POST['pupdate'])){
            update($conn, "UPDATE core_users SET core_pass= :pass WHERE usalt= :usalt", [':pass' => $user_password, ':usalt' => $usercont]);
        }

        ui::submitBtn($conn, 'Update', 'button', 'pupdate');

        echo '</form>';
        ?>

    </div>
</div><!--right-->


<?php
$conn = null;
?>

</div><!--main-->
</body>
</html>
