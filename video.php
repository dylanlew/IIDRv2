<?php
require_once('init.php');
$page = new VideoPage($_GET['id']);
$page->Page();
?>