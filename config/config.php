<?php
// check if we are on a subsite
$script_name = $_SERVER['SCRIPT_NAME'];
$site_sub = '';
while ($slashpos = strpos($script_name, '/', 1))
{	if ($subsite = substr($script_name, 1, $slashpos))
	{	if (strstr($subsite, 'iiadmin'))
		{	break;
		} else
		{	$site_sub .= '/' . substr($subsite, 0, -1);
			$script_name = substr($script_name, $slashpos);
		}
	}
}
define('SITE_SUB', $site_sub);

define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST'] . SITE_SUB . '/');
define('CITDOC_ROOT', $_SERVER['DOCUMENT_ROOT'] . SITE_SUB);

define('SITE_EMAIL', 'info@iidr.org');

define('COMPANY_NAME', 'IIDR');
define('SITE_NAME', 'Islamic Institute For Development &amp; Research');
define('LIVE_USER_NAME', 'st');
define('CIT_FULLLINK', SITE_URL);
define('CSS_ROOT', SITE_SUB . '/css/');
define('JS_ROOT', SITE_SUB . '/js/');
define('CIT_HOST', $_SERVER['HTTP_HOST'] . SITE_SUB);

/*define('DB_HOST', 'db557936051.db.1and1.com');
define('DB_USER', 'dbo557936051');
define('DB_PASS', 'bQ0kVpY3BeIO');
define('DB_NAME', 'db557936051');*/

define('DB_HOST', 'db582466017.db.1and1.com');
define('DB_USER', 'dbo582466017');
define('DB_PASS', 'Bq0KvPy3bEio');
define('DB_NAME', 'db582466017');


if (!defined('SITE_TEST'))
{	define('SITE_TEST', false);
}
if (!defined('DEFAULT_LANGUAGE'))
{	define('DEFAULT_LANGUAGE', 'en');
}

?>