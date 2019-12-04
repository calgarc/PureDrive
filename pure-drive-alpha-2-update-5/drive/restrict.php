<?php
define('func', TRUE);

$root = '../';
require '../req/headers.php';

session_unset();
session_destroy();
session_regenerate_id(true);
//header("Location: ../login"); //die();

loggedin($root);

$conn = null;
?>
</div><!--main-->
</body>
</html>
