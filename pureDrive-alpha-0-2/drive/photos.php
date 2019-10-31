<?php
$root = '../';
require '../req/headers.php';

if(!isset($_SESSION['user'])) {
header("Location: ../login"); //die();
}

if ($_SESSION['user'] == '0'){
header("Location: ../login"); //die();
}

$searchable = 'true';
$photos = 'active';
require '../req/index.php';
loggedin($root);
?>



<div id="photos" class="right"><!--right-->
    
    <div id="dirbtn">
    <?php echo dirlink($conn, 1); ?>
    </div>
    
<?php echo gallery($conn, 1); ?>

</div><!--right-->

<div class="folderPopup" id="folderPopup" onclick="closemodal();">
    
    <div class="popCont">
    <form class="icont" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" id="createFolder" method="post" enctype="multipart/form-data">
    </form>
    </div>
    
</div>

<?php 
$conn = null;
?> 
</div><!--main-->
</body>
</html>
