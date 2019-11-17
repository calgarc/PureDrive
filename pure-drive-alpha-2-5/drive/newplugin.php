<?php
define('func', TRUE);

$root = '../';
require '../req/headers.php';

$searchable = 'false';
$plugins = 'active';
require '../req/index.php';

IsSession();
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
</div><!--right-->


<?php
$conn = null;
?>

</div><!--main-->
</body>
</html>
