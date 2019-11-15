<?php
$root = '../';
require '../req/headers.php';

if(!isset($_SESSION['user'])) {
    header("Location: ../login"); //die();
}

require '../req/index.php';
require '../req/functions.php';
loggedin($root);
?>

<div id="left"><!--left-->
</div><!--left-->

<div id="right" class="right"><!--right-->
    
    <div class="restrict">
    <img src="../req/css/restrict.jpg" />
    <h1>YOU DO NOT HAVE PRIVILEGES TO ACCESS THIS PAGE</h1>
    </div>
    
</div><!--right-->


<?php 
$conn = null;
?> 
</div><!--main-->
</body>
</html>
