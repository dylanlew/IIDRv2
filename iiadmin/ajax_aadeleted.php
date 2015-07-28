<?php
include_once("sitedef.php");
$aa = new DeletedAdminActions($_GET["tablename"]);
if ($aa->CanSeeHistory())
{	echo "<!--aahistory-->", $aa->DisplayTable();
}
?>