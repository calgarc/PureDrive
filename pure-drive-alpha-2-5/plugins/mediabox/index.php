<!--plugin header-->
<script src="https://unpkg.com/wavesurfer.js"></script>
<?php
$root = '../../';
require $root.'req/headers.php';
    if(isset($_SESSION['user'])) {
    }else{
    $login = $root.'login';
    header("Location: $login"); //die();
    }

$plugin = 'isactive';// active css
require $root.'req/index.php';
loggedin($root);
?>

<link rel="stylesheet" href="css/styles.css" type="text/css" />

<!--plugin start-->
<script>
var nxt = 1;
</script>

<div id="right" class="right"><!--right-->

<div class="audionav">
<button type="button" class="stop" onclick="next(); document.getElementById(--nxt).click();" ><i class="fas fa-step-backward"></i></button>

<button type="button" id="play" class="play" onclick="wavesurfer.play(); play();" style="display:block;"><i class="fas fa-play"></i></button><button type="button" class="play" id="pause" onclick="wavesurfer.pause(); play();" style="display:none;"><i class="fas fa-pause"></i></button>

<button type="button" class="stop" onclick="next(); document.getElementById(++nxt).click();" ><i class="fas fa-step-forward"></i></button>
<div id="audio" class="audio"></div>

<button type="button" id="up" class="stop" onclick="mute(); wavesurfer.toggleMute();" style="display:block;"><i class="fas fa-volume-up"></i></button><button type="button" id="mute" class="stop" onclick="mute(); wavesurfer.toggleMute();" style="display:none;"><i class="fas fa-volume-mute"></i></button>

<?php
echo('<form><select class="btn locbtn" name="location" id="display" onchange="form.submit();">');
$result = $conn->prepare("SELECT file_name, dir_id, user_id, cwd FROM core_folders WHERE user_id= :uid");
$result->execute([':uid' => UserId($conn, 1)]);
$result->setFetchMode(PDO::FETCH_ASSOC);

$loc = 'all';

if(isset($_GET['location'])) {
    if($_GET['location'] != 'drives') {
    $loc = substr($_GET['location'], 0, -6);
    }

    if($_GET['location'] == 'drives') {
    $loc = 'root';
    }

    if ($_GET['location'] == 'all') {
    $loc = 'all';
    }
}

echo $loc;


echo('<option value="'.$loc.'">'.$loc.'</option><option value="all">All</option><option value="drives">Root</option>');

    while ($r = $result->fetch()) {
    echo("<option value='".$r['file_name']."'>".substr($r['file_name'], 0, -6)."</option>");
    }

echo('</select></form>');

?>

<img src="logo2.png" />

</div>

<script>
function play() {
    var x = document.getElementById("play");
    var y = document.getElementById("pause");

    if (x.style.display === "block") {
    x.style.display = "none";
    y.style.display = "block";

    } else {
    x.style.display = "block";
    y.style.display = "none";
    }
}

function mute() {
    var x = document.getElementById("up");
    var y = document.getElementById("mute");

    if (x.style.display === "block") {
    x.style.display = "none";
    y.style.display = "block";

    } else {
    x.style.display = "block";
    y.style.display = "none";
    }
}

</script>

<div id="column" class="mediabox">
<form id="delete"  method="post">


<?php
$location = 'all';

if(isset($_GET['location'])) {
$location = $_GET['location'];
}

if($location == 'all') {
$dirid = "WHERE file_type LIKE '%audio%' AND";
}else {
$dirid = "WHERE file_type LIKE '%audio%' AND dir_id='".$location."' AND";
}




$audio = 'audio/mpeg';
$result = $conn->prepare("SELECT file_name, file_type, dir_id, user_id, cwd, folder_fav FROM core_files ".$dirid." user_id='".UserId($conn, 1)."'");
$result->execute();
$result->setFetchMode(PDO::FETCH_ASSOC);

echo '<script>
var wavesurfer = WaveSurfer.create({
        container: "#audio",
        waveColor: "#dddddd",
        progressColor: "#ffd116",
        barWidth: "2",
        maxCanvasWidth: "680",
        height: "58",
        skipLength: "2",
        cursorColor:"#dddddd"
    });


    wavesurfer.on("finish", function () {
        document.getElementById(nxt).style.background = "none";
        document.getElementById(++nxt).click();
        var x = document.getElementById("play");
        var y = document.getElementById("pause");
        document.getElementById(nxt).style.background = "#dddddd";
    });

    wavesurfer.on("ready", function () {
        wavesurfer.play();
        document.getElementById(nxt).style.background = "#dddddd";
        var x = document.getElementById("play");
        var y = document.getElementById("pause");
        if (x.style.display === "block") {
        x.style.display = "none";
        y.style.display = "block";
    }

    });

    function next() {
        document.getElementById(nxt).style.background = "none";
    }

</script>';

$a = 1;
while ($r = $result->fetch()) {

    if ($r['folder_fav'] == 1) {
        $faved = 'favbtnactive';
        }else {
        $faved = 'favbtn';
    }

    $directory = $r['cwd'];
    $source = substr($directory.'/'.$r['file_name'], 0, -6);
    $url = encrypt($conn, 1, $source);

    $directory = $r['cwd'];
    $audiofile = '../'.$directory.'/'.substr($r['file_name'], 0, -6);
    $load = ('document.getElementById(nxt).style.background = "none"; wavesurfer.load("'.$audiofile.'"); nxt = $(this).attr("id");');

    echo ("<div class='column' onclick='".$load."' id='".$a++."'>");
    echo('<span class="audiofile">'.substr($r['file_name'], 0, -6).'</span>');
    echo ('<button type="submit" value="'.$r['file_name'].'" class="'.$faved.'" name="fav" ><i class="fa fa-star"></i></button>');
    echo ('<a href="../'.$url.'" class="favbtn" download><i class="fa fa-download"></i></a>');
    echo ('</div>');

}
    echo '</form></div>';

    addfav($conn, 1);
?>



</div><!--right-->

<script>
    wavesurfer.on("loading", function () {
    $( "#aniout" ).show();
    });

    wavesurfer.on("ready", function () {
    $( "#aniout" ).hide();
    });
</script>

<?php
$conn = null;
?>
</div><!--main-->


</body>
</html>
