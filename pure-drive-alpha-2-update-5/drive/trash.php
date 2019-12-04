<?php
define('func', TRUE);

$root = '../';
require '../req/headers.php';

$searchable = 'true';
$trash = 'active';
require '../req/index.php';

IsSession($root);
loggedin($root);

echo '<div id="left">
      <div class="folders">';

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

<div id="right" class="right trash"><!--right-->

<div id="dirbtn">
<?php echo dirlink($conn, 1); ?>

    <div id="btnContainer">

        <form id="sortform" enctype="multipart/form-data" method="post">
            <select class="btn" name="sortby" id="sortby" onchange="form.submit();" ><option value="file_name">Sort By</option><option value="file_name">Name</option><option value="file_size">Size</option><option value="reg_date">Date created</option><option value="file_type">File type</option></select>
        </form>

    <?php print_r($errors); ?>

    </div>

    <form id="dl" method="post"></form>
</div><!-- dirbtn -->

    <?php
    $dispfav ='3';
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
