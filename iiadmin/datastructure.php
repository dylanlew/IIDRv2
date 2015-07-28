<?php
include_once("sitedef.php");

class DataStructurePage extends MapBuildPage
{	var $fieldnoshow = array("Privileges"=>1, "Collation"=>1);
	var $indexnoshow = array("Table"=>1, "Index_type"=>1, "Collation"=>1, "Sub_part"=>1, "Packed"=>1);

	function __construct() // constructor
	{	parent::__construct();
	} // end of fn __construct (constructor)
	
	function AdminMapBuildLoggedInConstruct()
	{	parent::AdminMapBuildLoggedInConstruct();
		$this->js[] = "mb_collapse.js";
		$this->breadcrumbs->AddCrumb("datastructure.php", "database");
	} // end of fn AdminMapBuildLoggedInConstruct

	function AdminMapBuildBody()
	{	$this->Tables();
	} // end of fn AdminMapBuildBody

	function Tables()
	{	echo "<ul class='tableList'>\n";
		if ($result = $this->db->Query("SHOW TABLE STATUS LIKE '%'"))
		{	while ($row = $this->db->FetchArray($result))
			{	echo "<li>\n";
				$this->DescribeTable($row["Name"], $row);
				echo "</li>\n";
			}
		}
		echo "</ul>\n";
	} // end of fn Tables

	function DescribeTable($table = "", $details = "")
	{	
		echo "<b>", $table, "</b> ", $details["Comment"], "<ul class='tableDetails'>\n<li>\n";
		$this->FieldTable($table);
		echo "</li>\n<li>\n";
		$this->IndexTable($table);
		echo "</li>\n</ul>\n";
	} // end of fn DescribeTable

	function FieldTable($table = "")
	{
		echo "<a onclick='CollpaseObject(\"", $id = "f_{$table}", 
				"\");'>fields +</a>\n<table class='tfieldsList' id='", $id, "' style='display:none'>";
		$sql = "SHOW FULL COLUMNS IN $table";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if (!$header++)
				{	echo "<tr>\n";
				  	foreach ($row as $field=>$value)
					{	if (!$this->fieldnoshow[$field])
						{	echo "<th>", $field, "</th>\n";
						}
					}
					echo "</tr>\n";
				}
				echo "<tr>\n";
			  	foreach ($row as $field=>$value)
				{	if (!$this->fieldnoshow[$field])
					{	echo "<td>", $value, "</td>\n";
					}
				}
				echo "</tr>\n";
			}
		}
		echo "</table>\n";
	} // end of fn FieldTable

	function IndexTable($table = "")
	{
		echo "<a onclick='CollpaseObject(\"", $id = "i_{$table}", 
				"\");'>indices +</a>\n<table class='tfieldsList' id='", $id, "' style='display:none'>";
		$sql = "SHOW INDEXES IN $table";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if (!$header++)
				{	echo "<tr>\n";
				  	foreach ($row as $field=>$value)
					{	if (!$this->indexnoshow[$field])
						{	echo "<th>", $field, "</th>\n";
						}
					}
					echo "</tr>\n";
				}
				echo "<tr>\n";
			  	foreach ($row as $field=>$value)
				{	if (!$this->indexnoshow[$field])
					{	echo "<td>", $value, "</td>\n";
					}
				}
				echo "</tr>\n";
			}
		}
		echo "</table>\n";
	} // end of fn IndexTable

} // end of class defn DataStructurePage

$page = new DataStructurePage();
$page->Page();
?>