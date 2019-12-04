<?php
define('func', TRUE);

$root = '../';
require '../req/headers.php';

$searchable = 'true';
$folds = 'active';
require '../req/index.php';

IsSession($root);
loggedin($root);
?>

<div id="left"><!--left-->
  <div class="folders">
    <?php
      if (!ismobile()) {
        recentfiles($conn, 1);
        dispfavfolders($conn, 1);
        dispfolders($conn, 1);
        ui::accordion($conn, '', 'Shared with you', 'fas fa-share-square');
        ui::accordion($conn, '', 'You shared', 'fas fa-share-square');
      }

      echo '</div>
            </div>';
    ?>

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
                    <input type="file" name="myFiles[]" id="myFiles" multiple="" onchange="uploadFiles();"/>
                </form>

                <button onclick="createBar();" class="trigger folderBut"><i class="fa fa-folder"></i>Folder</button>
            </div>
        </div>

    <?php print_r($errors); ?>

    </div>

    <form id="dl" method="post"></form>
  </div><!-- dirbtn -->


    <?php
    $dispfav ='2';
    displayfiles($conn, 1, $dispfav);
    ?>

    <div id="infocont">
        <div id="infobar">
            <button class="hide hdown" onclick="infoBarShow();"><i class="fas fa-chevron-circle-up"></i></button>
            <button class="hide hup" onclick="infoBarHide();" style="display:none;"><i class="fas fa-chevron-circle-down" ></i></button>
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

<div class="folderPopup" id="folderPopup" <?php echo $click; ?>="closeModal();">

    <div class="popCont">
    <form class="icont" id="createFolder" enctype="multipart/form-data">
    </form>
    </div>

</div>

<?php
$conn = null;
?>

</div><!--main-->
</body>
</html>
