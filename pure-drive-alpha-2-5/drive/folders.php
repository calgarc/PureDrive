<?php
define('func', TRUE);

$root = '../';
require '../req/headers.php';

$searchable = 'true';
$folds = 'active';
require '../req/index.php';

IsSession();
loggedin($root);
?>

<div id="left"><!--left-->
  <div class="folders">

      <button class="accordion"><i class="fas fa-history" aria-hidden="true"></i>Recent Files</button>
      <div class="panel">
          <?php
          if (!ismobile()) {
              $favfolders = recentfiles($conn, 1);
              echo $favfolder;
          }
          ?>
      </div>

      <button class="accordion"><i class="fa fa-star" aria-hidden="true"></i>Your Favorites</button>
      <div class="panel favs">
          <?php
          if (!ismobile()) {
              $favfolders = dispfavfolders($conn, 1);
              echo $favfolder;
          }
          ?>
      </div>

      <button class="accordion"><i class="fa fa-folder" aria-hidden="true"></i>Folders</button>
      <div class="panel">
          <?php
          if (!ismobile()) {
              $folders = dispfolders($conn, 1);
              echo $folders;
          }
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
        this.classList.toggle("active");

        var panel = this.nextElementSibling;
        if (panel.style.display === "block") {
        panel.style.display = "none";
        } else {
        panel.style.display = "block";
        }
    });
    }
    </script>

</div><!--left-->

<div id="right" class="right"><!--right-->

  <div id="dirbtn">

    <div class="hier">
    <?php echo dirlink($conn, 1); ?>
    </div>

    <div id="btnContainer">

        <form id="viewform" enctype="multipart/form-data" method="post" >
        <button class="btn btnlist" name="list" value="listview"><i class="fa fa-bars"></i> List</button>
        <button class="btn btngrid" name="grid" value="gridview"><i class="fa fa-th-large"></i> Grid</button>
        </form>

        <script>
        function createBar() {
            $('#create').animate({width:'toggle'},410);
        }
        </script>

        <form action="" id="create" method="post" enctype="multipart/form-data">
        <input type="text" name="folder" id="folder" placeholder="New folder">
        <button type="submit" value="createfolder" class="create" name="create"><i class="fa fa-folder" aria-hidden="true"></i>Create</button>
        </form>

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

                <button onclick="createBar();" class="trigger folderBut"><i class="fa fa-folder"></i>Folder</button>
            </div>
        </div>

    <?php print_r($errors); ?>

    </div>

    <form id="dl" method="post"></form>


    <?php
    $dispfav ='2';
    displayfiles($conn, 1, $dispfav);
    //var_dump($_SESSION['user']);
    ?>

    <div id="infocont">
        <div id="infobar">
            <button class="hide hdown" onclick="infoBar();"><i class="fas fa-chevron-circle-up"></i></button>
            <button class="hide hup" onclick="infoBar2();" style="display:none;"><i class="fas fa-chevron-circle-down" ></i></button>
            <form name="details"></form>

            <div class='infodet'>
            <?php
            displaydet($conn, 1);
            ?>
            </div>
        </div>
    </div>

</div><!--right-->

<?php
if (ismobile()) {
$click = 'onclick';
}else {
$click = 'ondblclick';
}
?>

<div class="folderPopup" id="folderPopup" <?php echo $click; ?>="closemodal();">

    <div class="popCont">
    <form class="icont" id="createFolder" enctype="multipart/form-data">
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
