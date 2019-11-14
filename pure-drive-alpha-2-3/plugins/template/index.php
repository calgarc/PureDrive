<!--plugin header-->
<?php
$root = '../../';
require $root.'req/headers.php';

if(isset($_SESSION['user'])) {

}else{
    header("Location: ../../login"); //die();
}

$plugin = 'active';
require $root.'req/index.php';
loggedin($root);
?>


<!--plugin start-->
<div id="left"><!--left-->

</div><!--left-->


<div id="right" class="right"><!--right-->

</div><!--right-->


<?php
$conn = null;
?> 
</div><!--main-->


</body>
</html>
