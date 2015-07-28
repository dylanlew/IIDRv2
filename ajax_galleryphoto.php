<?php 
require_once('init.php');

$photo = new GalleryPhoto($_GET['id']);
echo '<img src="', $photo->HasImage($_GET['size']), '" title="', $title = $photo->InputSafeString($photo->details['title']), '" alt="', $title, '" />';
?>