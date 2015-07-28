<?php 
require_once('init.php');

$gallery = new Gallery($_GET['gid']);
//echo $gallery->FrontEndList();
echo $gallery->FrontEndListLightbox();
?>