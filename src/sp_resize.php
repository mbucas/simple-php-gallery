<?php
require('sp_config.php');
require('sp_helper_functions.php');

$source = stripslashes($_GET['source']);
$path = pathinfo($source);

$hash = md5(substr($source, 2, strlen($source) - 2));

$original = imagecreatefrom_ext($source);

$xratio = $maxwidth/(imagesx($original));
$yratio = $maxheight/(imagesy($original));

if($xratio < 1 || $yratio < 1) {
    if($xratio < $yratio) {
        $thumb = imagecreatetruecolor(
            $maxwidth,
            floor(imagesy($original)*$xratio)
        );
    }
    else {
        $thumb = imagecreatetruecolor(
            floor(imagesx($original)*$yratio),
            $maxheight
        );
    }
    imagecopyresampled(
        $thumb,
        $original,
        0, 0,
        0, 0,
        imagesx($thumb) + 1, imagesy($thumb) + 1,
        imagesx($original), imagesy($original)
    );

    if($cacheresized) {
        imagejpeg($thumb, $cacheresizedfolder . "/" . $hash . ".jpg");
        $resized_cache_ini = @parse_ini_file(
            $cacheresizedfolder . "/resized_cache.ini",
            true
        );
        $resized_cache_ini[$hash]['size'] = filesize($source);
        write_ini_file(
            $cacheresizedfolder . "/resized_cache.ini",
            $resized_cache_ini
        );
    }

    imagejpeg($thumb);
    imagedestroy($thumb);
}
else {
    imagejpeg($original);
}
imagedestroy($original);
?>