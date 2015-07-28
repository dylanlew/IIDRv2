<?php 
require_once('init.php');

$page = new PagePage($_GET["page"]);
$page->Page();
?>