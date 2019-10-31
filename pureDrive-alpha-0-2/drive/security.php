<?php
$root = '../';
require '../req/headers.php';

if(!isset($_SESSION['user'])) {
header("Location: ../login"); //die();
}

if ($_SESSION['user'] == '0'){
header("Location: ../login"); //die();
}

$searchable = 'false';
$settings = 'active';
require '../req/index.php';
loggedin($root);
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

    <div class="userinfo">
    <h3>Encryption</h3>

    <form class="form" id="formed" method="post">
    <?php
    $result = $conn->prepare("SELECT setting FROM core_options WHERE options = 'enableEncryption'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    ?>

    <label>ENABLE ENCRYPTION</label><select name="encrypt"> <option value="<?php echo($r['setting']); ?>"><?php echo($r['setting']); ?></option><option value="Enable">Enable</option><option value="Disable">Disable</option></select>

    <?php
    $result = $conn->prepare("SELECT setting FROM core_options WHERE options = 'enctype'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    ?>

    <label>ENCRYPTION TYPE</label><select name="enctype"> <option value="<?php echo($r['setting']); ?>"><?php echo($r['setting']); ?></option><option value="AES-256-CBC">AES-256-CBC</option><option value="AES-128-CBC">AES-128-CBC</option></select>

    <input type="submit" value="Update" name="update" class="button">

    <?php
    $source = $_POST['enctype'];
    $enctype = encrypt($conn, 1, $source);
    
    if (isset($_POST['update'])){
    $result = $conn->prepare("UPDATE core_options SET setting = '".$enctype."' WHERE options = 'enctype'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);

    $source = $_POST['encrypt'];
    $encryption = encrypt($conn, 1, $source);
    $result = $conn->prepare("UPDATE core_options SET setting = '".$encryption."' WHERE options = 'enableEncryption'");
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    header("Location: security");
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
