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
</div><!--right-->


<?php 
$conn = null;
?> 

</div><!--main-->
</body>
</html>
