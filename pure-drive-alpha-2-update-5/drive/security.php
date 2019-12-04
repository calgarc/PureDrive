<?php
define('func', TRUE);

$root = '../';
require '../req/headers.php';

$searchable = 'false';
$settings = 'active';
require '../req/index.php';

IsSession($root);
loggedin($root);
restrict($conn, $admin, $username, 1);

//left
echo '<div id="left">';
    ui::h2($conn, 'Settings');

    echo '<ul class="side">';
      ui::sideLink($conn, 'settings', 'General', 'fas fa-sliders-h', '');
      ui::sideLink($conn, 'security', 'Security', 'fas fa-lock', '');
      ui::sideLink($conn, 'server', 'Server', 'fas fa-server', '');
    echo '</ul>';

echo '</div>';


echo '<div id="right" class="right"><!--right-->
      <div class="userinfo">
      <form class="form" id="formed" method="post">';

        ui::h3($conn, 'Encryption');

        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'enableEncryption']);

        ui::label($conn, 'ENABLE ENCRYPTION:');
        echo '<select name="encrypt">';

            ui::inputOption($conn, $r['setting']);
            ui::inputOption($conn, 'Enabled');
            ui::inputOption($conn, 'Disabled');

        echo '</select>';

        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'enctype']);

        ui::label($conn, 'ENCRYPTION TYPE:');
        echo '<select name="enctype">';

            ui::inputOption($conn, $r['setting']);
            ui::inputOption($conn, 'AES-128-CBC');
            ui::inputOption($conn, 'AES-256-CBC');

        echo '</select>';

        ui::h3($conn, '2 Factor Authentication');
        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'enable2fa']);

        ui::label($conn, 'ENABLE 2FA:');
        echo '<select name="2fa">';

            ui::inputOption($conn, $r['setting']);
            ui::inputOption($conn, 'Enabled');
            ui::inputOption($conn, 'Disabled');

        echo '</select>';

        if(!ismobile()) {
          ui::h3($conn, 'Htaccess');
          ui::button($conn, '', 'button access', 'Edit htaccess', 'button', 'onclick="modal();"');
        }

        ui::h3($conn, 'Auth Token');
        $r = select($conn, "SELECT setting FROM core_options WHERE options = :option", [':option' => 'token']);

        ui::label($conn, 'REGENERATION TIME:');
        echo '<span>Time in seconds</span>';
        ui::inputText($conn, $r['setting'], 'token', 'text', '15');

        ui::submitBtn($conn, 'Update', 'button', 'update');

        $enctype = secure($conn, $_POST['enctype']);
        $encryption = secure($conn, $_POST['encrypt']);
        $tfa = secure($conn, $_POST['2fa']);
        $token = secure($conn, $_POST['token']);

        if (isset($_POST['update'])){
            $r = update($conn, "UPDATE core_options SET setting = :setting WHERE options = :option", [':option' => 'enctype', ':setting' => $enctype]);
            $r = update($conn, "UPDATE core_options SET setting = :setting WHERE options = :option", [':option' => 'enableEncryption', ':setting' => $encryption]);
            $r = update($conn, "UPDATE core_options SET setting = :setting WHERE options = :option", [':option' => 'enable2fa', ':setting' => $tfa]);
            $r = update($conn, "UPDATE core_options SET setting = :setting WHERE options = :option", [':option' => 'token', ':setting' => $token]);

            header("Location: security");
        }
        ?>


    </form>
  </div>
</div><!--right-->

<div class="folderPopup" id="folderPopup" <?php echo $click; ?>="closeModal();">

    <div class="popCont">
    <form class="icont" id="createFolder" enctype="multipart/form-data">
      <?php htaccess($conn, 1); ?>
    </form>
    </div>

</div>

<?php
$conn = null;
?>

</div><!--main-->
</body>
</html>
