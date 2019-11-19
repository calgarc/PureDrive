<?php
define('func', TRUE);

$root = '../';
require '../req/headers.php';

$searchable = 'false';
$settings = 'active';
require '../req/index.php';

IsSession();
loggedin($root);
restrict($conn, $admin, $username, 1);

//left
echo '<div id="left">';
    ui::h2($conn, 'Settings');

    echo '<ul class="side">';
      ui::sideLink($conn, 'settings', 'General', 'fas fa-sliders-h');
      ui::sideLink($conn, 'security', 'Security', 'fas fa-lock');
      ui::sideLink($conn, 'server', 'Server', 'fas fa-server');
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
            ui::inputOption($conn, 'Enable');
            ui::inputOption($conn, 'Disable');

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
            ui::inputOption($conn, 'Enable');
            ui::inputOption($conn, 'Disable');

        echo '</select>';

        ui::submitBtn($conn, 'Update', 'button', 'update');

        $enctype = encrypt($conn, 1, $_POST['enctype']);
        $encryption = encrypt($conn, 1, $_POST['encrypt']);
        $tfa = encrypt($conn, 1, $_POST['2fa']);

        if (isset($_POST['update'])){
            $r = update($conn, "UPDATE core_options SET setting = :setting WHERE options = :option", [':option' => 'enctype', ':setting' => $enctype]);
            $r = update($conn, "UPDATE core_options SET setting = :setting WHERE options = :option", [':option' => 'enableEncryption', ':setting' => $encryption]);
            $r = update($conn, "UPDATE core_options SET setting = :setting WHERE options = :option", [':option' => 'enable2fa', ':setting' => $tfa]);
            header("Location: security");
        }
        ?>


    </form>
  </div>
</div><!--right-->

<?php
$conn = null;
?>

</div><!--main-->
</body>
</html>
