<?php

function thumbnails($filename, $percent) {
//$filename = substr($r['file_name'], 0, -6);
$filename = 'drive/profile/bg.jpg';
$percent = 0.3;

header('Content-Type: image/jpeg');

list($width, $height) = getimagesize($filename);
$new_width = $width * $percent;
$new_height = $height * $percent;

$image_p = imagecreatetruecolor($new_width, $new_height);
$image = imagecreatefromjpeg($filename);
imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

$url = 't-'.$filename;
imagejpeg($image_p, $url, 90);
}

?>
