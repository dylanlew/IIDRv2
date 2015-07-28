<?php 
require_once('init.php');

$page = new CoursePage($_GET['id']);
$page->Page();
?>