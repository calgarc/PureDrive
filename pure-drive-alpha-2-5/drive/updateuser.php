<?php
define('func', TRUE);

$root = '../';
require '../req/headers.php';

$searchable = 'true';
$users = 'active';
require '../req/index.php';

IsSession();
loggedin($root);
restrict($conn, $admin, $username, 1);

if($_GET['id'] != userid($conn, 1)) {
    if('true' != admin($conn, 1)) {
      // session timed out
      session_unset();
      session_destroy();
      $login = $root.'login';
      header("Location: $login"); //die();
    }
}
?>



<div id="left"><!--left-->

    <div class="nuser">
        <a href="newuser" class="dropbtn"><i class="fa fa-user-plus"></i>New</a>
    </div>

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
                    $errors[]="extension not allowed, please choose a JPEG or PNG file.";
                }

                if($file_size > 4194304){
                    $errors[]='File size must be excately 4 MB';
                }

                if(empty($errors)==true){
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
            };
        </script>

        <form class="form" id="formed" method="post">

        <label>USERNAME</label><span>cannot be changed</span><input type="text" style="background-color:#cccccc;" value="<?php echo($r['core_username']); ?>" readonly>

        <label>EMAIL</label><span>required</span><input type="text" name="email" id="email" value="<?php echo($r['core_email']); ?>">

        <label>FIRST NAME</label><input type="text" name="fname" id="fname" value="<?php echo($r['core_firstname']); ?>" placeholer="First name">

        <label>LAST NAME</label><input type="text" name="lname" id="lname" value="<?php echo($r['core_lastname']); ?>" placeholer="Last name">

        <label>USER TYPE</label><select name="usertype" <?php restrictform($conn, $admin, $username, 1); ?> ><option value="<?php echo($r['user_type']); ?>"><?php echo($r['user_type']); ?></option><option value="Standard">Standard</option><option value="Administrator">Administrator</option></select>

        <label>SPACE</label><span>number in GB</span><input type="text" name="space" id="space" value="<?php echo($limit); ?> GB" placeholer="space" <?php restrictform($conn, $admin, $username, 1); ?>>

       <input type="submit" value="Update" name="update" class="button">


        <?php

        $source = $_POST['email'];
        $useremail = encrypt($conn, 1, $source);

        $source = sanitize($_POST['lname']);
        $userlname = encrypt($conn, 1, $source);

        $source = sanitize($_POST['fname']);
        $userfname = encrypt($conn, 1, $source);

        $source = sanitize($_POST['usertype']);
        $usertype = encrypt($conn, 1, $source);

        $uplimit = sanitize($_POST['space']);
        $source = str_replace(str_split('gGbB '),"",$uplimit);
        $uplimit = encrypt($conn, 1, $source);

            if (isset($_POST['update'])){
                update($conn,  "UPDATE core_users SET core_email= :email, core_firstname= :fname, core_lastname= :lname, user_type= :type, uplimit= :uplimit WHERE usalt= :usalt",
                  [':email' => $useremail, ':fname' => $userfname, ':lname' => $userlname, ':type' => $usertype, ':uplimit' => $uplimit, ':usalt' => $usercont]);

                header("Location: updateuser?id=$usercont");
            }
        ?>
        </form>
    </div>



    <div class="userinfo">
        <h3>Password</h3>
        <form class="form" method="post">
        <label>NEW PASSWORD:</label><input type="password" name="pass" id="pass" >
        <label>REPEAT PASSWORD:</label><input type="password" name="rpass" id="rpass" >

        <?php
        //salts
        function generateRandomPass($length = 16) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%?';
            $charactersLength = strlen($characters);
            $randomString = '';

            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }

            return $randomString;
        }

        $pass =  false;
        if(isset($_POST['pass'])){
            $pass = $_POST['pass'];
        }

        $salted = generateRandomPass();
        $user_password = hash('sha512', $pass . $salted ) . $salted;


        if ($_POST['pass']!= $_POST['rpass']) {
            echo('<span class="oops">Your password did not match. Try again.</span>');

        }elseif (isset($_POST['pupdate'])){
            update($conn, "UPDATE core_users SET core_pass= :pass, salt= :salted WHERE usalt= :usalt", [':pass' => $user_password, ':salted' => $salted, ':usalt' => $usercont]);
        }
        ?>

        <input type="submit" value="Update" name="pupdate" class="button">
        </form>

    </div>



</div><!--right-->


<?php
$conn = null;
?>

</div><!--main-->
</body>
</html>
