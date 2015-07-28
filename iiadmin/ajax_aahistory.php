<?php
include_once("sitedef.php");
$aa = new AdminActions($_GET["tablename"], $_GET["tableid"]);
if ($aa->CanSeeHistory())
{	echo "<!--aahistory-->", $aa->DisplayTable();
}
?>