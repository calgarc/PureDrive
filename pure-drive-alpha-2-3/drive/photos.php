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
            <div id="btnContainer">
        
            <?php 
            galleryfolders($conn, 1);
            ?>

            <form id="sortform" enctype="multipart/form-data" method="get">
            <select class="btn" name="sortlink" id="sortlink" onchange="sortphotos();" ><option value="file_name">Sort By</option><option value="file_name">Name</option><option value="file_size">Size</option><option value="reg_date">Date created</option><option value="file_type">File type</option></select>
            </form>

            <script>
                function createBar() {
                    $('#create').animate({width:'toggle'},410);
                } 
            </script>

            <div class="dropdown">
                <a class="dropbtn"><i class="fa fa-plus"></i>Upload</a>
                <div class="dropdown-content">
                    <form action="" id="formed" class="up-outer" method="post" enctype="multipart/form-data">
                    <button type="submit" name="upload" multiple="multiple" class="folderBut" ><i class="fa fa-plus"></i>File</button>
                    <input type="file" name="myFiles[]" id="myFiles" multiple=""/>
                    <script>
                        document.getElementById("myFiles").onchange = function() {
                            document.getElementById("aniout").style.display = "block";
                            document.getElementById("formed").submit();
                        };
                    </script>
                    </form>
                </div>
            </div>
        
        <?php print_r($errors); ?>

        </div>
    </div>
    
    <div class="files">
    <?php echo gallery($conn, 1); ?>
    </div>

</div><!--right-->

<div class="folderPopup" id="folderPopup" ondblclick="closemodal();">
    
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
