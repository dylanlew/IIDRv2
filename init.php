<?php 
<<<<<<< HEAD
error_reporting(E_ALL ^ E_NOTICE);
=======
error_reporting(0);
>>>>>>> origin/master
require_once('config/config.php'); 
session_start();

// autoload classes
function __autoload($className) 
{	if(file_exists($file = CITDOC_ROOT . "/classes/" . strtolower($className) . '.class.php'))
	{	include_once($file);
	}
	elseif(file_exists($file = CITDOC_ROOT . "/classes/" . strtolower($className) . '.interface.php'))
	{	include_once($file);
	}
} // end of __autoload


define('CONTACT_EMAIL', 'info@iidr.org');
?>