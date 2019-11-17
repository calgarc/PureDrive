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
    <div id="userinfo">

    </div>
</div><!--right-->


<?php
$conn = null;
?>

</div><!--main-->
</body>
</html>
