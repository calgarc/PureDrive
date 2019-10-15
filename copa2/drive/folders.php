<?php
$root = '../';
require '../req/headers.php';
if(isset($_SESSION['user'])) {
//echo "Your session is running " . $_SESSION['user'];
}else{
header("Location: ../login"); //die();
}
//var_dump(getcwd());
$folds = 'active';
require '../req/index.php';
loggedin($root);


?>

<div id="left"><!--left-->

<div class="folders">

<button class="accordion"><i class="fas fa-history" aria-hidden="true"></i>Recent Files</button>
<div class="panel">
<?php
$favfolders = recentfiles($conn, 1);
echo $favfolder;
?>
</div>

<button class="accordion"><i class="fa fa-star" aria-hidden="true"></i>Your Favorites</button>
<div class="panel">
<?php
$favfolders = dispfavfolders($conn, 1);
echo $favfolder;
?>
</div>

<button class="accordion"><i class="fa fa-folder" aria-hidden="true"></i>Folders</button>
<div class="panel">
<?php
$folders = dispfolders($conn, 1);
echo $folders;
?>
</div>

<button class="accordion"><i class="fas fa-share-square" aria-hidden="true"></i>Shared with you</button>
<div class="panel">
</div>

<button class="accordion"><i class="fas fa-share-square" aria-hidden="true"></i>You shared</button>
<div class="panel">
</div> 
</div>

<script>
var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    /* Toggle between adding and removing the "active" class,
    to highlight the button that controls the panel */
    this.classList.toggle("active");

    /* Toggle between hiding and showing the active panel */
    var panel = this.nextElementSibling;
    if (panel.style.display === "block") {
      panel.style.display = "none";
    } else {
      panel.style.display = "block";
    }
  });
} 

//test
var _dirid = <?php echo json_encode($_GET['id']); ?>;
</script>

</div><!--left-->

<div id="right" class="right"><!--right-->
<div id="dirbtn">
<?php echo dirlink($conn, 1); ?>
<div id="btnContainer">
<form id="viewform" method="post">
  <button class="btn" name="list"><i class="fa fa-bars"></i> List</button> 
  <button class="btn" name="grid"><i class="fa fa-th-large"></i> Grid</button>
</form>

<form id="sortform" enctype="multipart/form-data" method="post">
<select class="btn" name="sortby" id="sortby" onchange="form.submit();" ><option value="file_name">Sort By</option><option value="file_name">Name</option><option value="file_size">Size</option><option value="reg_date">Date created</option><option value="file_type">File type</option></select>
</form>

<script>
function createBar() {
  var x = document.getElementById("create");
  if (x.style.display === "block") {
    x.style.display = "none";
  } else {
    x.style.display = "block";
  }
} 
</script>

<form action="" id="create" method="post" enctype="multipart/form-data">
<input type="text" name="folder" id="folder" placeholder="New folder">
<button type="submit" value="createfolder" class="create" name="create"><i class="fa fa-folder" aria-hidden="true"></i>Create</button>
</form>

<div class="dropdown">
<a class="dropbtn"><i class="fa fa-plus"></i>New</a>
<div class="dropdown-content">
<form id="formed" class="up-outer" method="post" enctype="multipart/form-data">
<button type="submit" name="upload" multiple="multiple" class="folderBut" ><i class="fa fa-plus"></i>File</button>
<input type="file" name="myFiles" id="myFiles" multiple=""/>
<script>
document.getElementById("myFiles").onchange = function() {
document.getElementById("formed").submit();
};
</script>
</form>
<button onclick="createBar();" class="trigger folderBut"><i class="fa fa-folder"></i>Folder</button>
</div>
</div>
<?php print_r($errors); ?>

</div>

<form id="dl" method="post"></form>
<?php
$dispfav ='2';
displayfiles($conn, 1, $dispfav);

error_reporting (E_ALL ^ E_NOTICE);
    error_reporting(E_ERROR | E_PARSE);
?>

<div id="infocont">

<div id="infobar">
<button class="hide" onclick="infoBar2();"><i class="fa fa-ellipsis-h"></i></button>
<form name="details"></form>

<div class='infodet'>
<?php
displaydet($conn, 1); 
?>
</div>

</div>
</div>

</div><!--right-->

<div class="folderPopup" id="folderPopup" ondblclick="closemodal();">
<div class="popCont">
        
<form class="icont" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" id="createFolder" method="post" enctype="multipart/form-data">

</form>
</div>
</div>

<script>

function copy() {
var copyText = document.getElementById("copied");
copyText.select();
copyText.setSelectionRange(0, 99999);
document.execCommand("copy");
}

</script>


<?php
$conn = null;
?> 
</div><!--main-->


</body>
</html>
