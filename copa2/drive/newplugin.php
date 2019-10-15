<?php
$root = '../';
require '../req/headers.php';
if(isset($_SESSION['user'])) {
//echo "Your session is running " . $_SESSION['user'];
}

if ($_SESSION['user'] == '0'){
header("Location: ../login"); //die();
}


$plugins = 'active';
require '../req/index.php';

loggedin($root);
restrict($conn, $admin, $username, 1);
?>

<div id="left"><!--left-->

<div class="folders">
<h2>Plugins</h2>

<ul class="side">
<li class="dir"><a href="plugins">Your Plugins</a></li>
<li class="dir"><a href="newplugin">New Plugins</a></li>
</ul>
</div>

</div><!--left-->

<div id="right" class="right plugins"><!--right-->

<div id="btnContainer">
<div class="search">
<input type="text" placeholder="Search..">
<button type="button"><i class="fa fa-search"></i></button>
</div>



</div><!--right-->


<?php 
$conn = null;
?> 
</div><!--main-->
</body>
</html>
