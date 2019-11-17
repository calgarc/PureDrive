<?php
require ('headers.php');
require('config.php');

function uid(PDO $conn, $r) {
    $result = $conn->prepare("SELECT usalt FROM core_users WHERE core_username = :ses");
    $result->execute([':ses' => $_SESSION['user']]);
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $r = $result->fetch();
    $usersalt = $r['usalt'];

    return $usersalt;
}

$userid = uid($conn, 1);

?>
