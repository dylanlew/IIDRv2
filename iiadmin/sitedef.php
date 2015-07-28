<?php
define('DEFAULT_LANGUAGE', 'en');
include_once("../init.php");

if (!is_array($_SESSION[SITE_NAME]))
{	$_SESSION[SITE_NAME] = array();
}

?>