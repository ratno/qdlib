<?php
include_once("bootstrap.php");

$cssfile = __CSS_PATH__ . "/" . $_GET['f'] . ".css";
if (file_exists($cssfile)) {
    $css = file_get_contents($cssfile);
    $css = str_replace(array("__CSS_URL__", "__WEB_URL__", "__IMAGES_URL__"), array(__CSS_URL__, __WEB_URL__, __IMAGES_URL__), $css);
} else {
    $css = "";
}

header("Content-type: text/css; charset: UTF-8");
//header("Content-type: text/javascript; charset: UTF-8");
header("Cache-Control: must-revalidate");
$offset = 60 * 60 * 24 * 3;
$ExpStr = "Expires: " .
    gmdate("D, d M Y H:i:s",
        time() + $offset) . " GMT";
header($ExpStr);
echo $css;