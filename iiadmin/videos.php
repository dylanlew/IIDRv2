<?php
include_once("sitedef.php");

class VideosListPage extends AdminPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function LoggedInConstruct()
	{	
		parent::LoggedInConstruct();
		$this->breadcrumbs->AddCrumb("videos.php", "Videos");
	} 
	
	function AdminBodyMain()
	{	$this->FilterForm();
		$this->VideoList();
	} 
	
	function VideoList()
	{
		$v = new AdminVideo;	
		$videos = $v->Filter($_GET['category']);
		
		if(sizeof($videos))
		{
			echo "<table><tr><th>Title</th><th>Image</th><th>Actions</th></tr>";
			
			foreach($videos as $v)
			{
				echo "<tr><td>". $this->InputSafeString($v->details['vtitle']) ."</td><td>". ($v->HasImage('thumbnail') ? "<img src='". $v->GetImageSRC('thumbnail') ."' alt='' />" : '') ."</td><td><a href='videoedit.php?id=".(int)$v->id."'>Edit</a>";
				if ($histlink = $this->DisplayHistoryLink("videos", $v->id))
				{	echo " | ", $histlink;
				}
				if ($v->CanDelete())
				{	echo " | <a href='videoedit.php?id=", $v->id, "&delete=1'>Delete</a>";
				}
				echo "</td></tr>";	
			}
			
			echo "</table>";
		}
		else
		{
			echo "<p>No videos exist.</p>";	
		}
	}
	
	
	function FilterForm()
	{	
		class_exists("Form");
		$v = new AdminVideo;
		$contselect = new FormLineSelect("", "category", $_GET["category"], "", $v->GetCategoryList(), true, 0, "", "-- all --");
		echo "<form id='orderSelectForm' action='", $_SERVER["SCRIPT_NAME"], 
					"' method='get'>\n<label for='category' class='from'>Category</label>";
		$contselect->OutputField();
		echo "<input type='submit' class='submit' value='Get' /><div class='clear'></div>\n</form>\n";
	} // end of fn FilterForm
	
	
	function CtryList()
	{	echo "<table><tr><th></th><th>Code</th><th>Cont.</th><th>Curr.</th><th>Course<br />listings</th><th>Paypal</th><th>Confirm details</th><th>Actions</th></tr>";
		foreach ($this->Countries() as $ctry)
		{	
			if ($this->user->CanAccessCountry($ctry->code))
			{
				echo "<tr class='stripe", $i++ % 2, "' id='tr", $ctry->code, "'>\n<td>", $this->InputSafeString($ctry->details["shortname"]), "</td>\n<td>", $ctry->details["shortcode"], "</td>\n<td>", $this->user->CanUserAccess("administration") ? "<a href='continentedit.php?continent={$ctry->details["continent"]}'>" : "", $ctry->details["continent"], $this->user->CanUserAccess("administration") ? "</a>" : "", "</td>\n<td>";
				if ($ctry->details["currency"])
				{	if ($accounts = $this->user->CanUserAccess("accounts"))
					{	echo "<a href='currency.php?code=", $ctry->details["currency"], "'>";
					}
					echo $ctry->details["currency"];
					if ($accounts)
					{	echo "</a>";
					}
				} else
				{	echo "($)";
				}
				echo "</td>\n<td>", $ctry->details["toplist"] ? $ctry->details["toplist"] : "", "</td>\n<td>";
				if ($ctry->details["paypalac"])
				{	$ppacc = new PaypalAccount($ctry->details["paypalac"]);
					echo $this->user->CanUserAccess("accounts") ? "<a href='ppedit.php?id={$ppacc->id}'>" : "", $this->InputSafeString($ppacc->details["username"]), $this->user->CanUserAccess("accounts") ? "</a>" : "";
				}
				echo "</td>\n<td>", $ctry->details["userconfirm"] ? "Yes" : "", "</td><td><a href='ctryedit.php?ctry=", $ctry->code, "'>edit</a>";
				if ($histlink = $this->DisplayHistoryLink("countries", $ctry->code))
				{	echo "&nbsp;|&nbsp;", $histlink;
				}
				if ($ctry->CanDelete($ctry->details["toplist"]))
				{	echo "&nbsp;|&nbsp;<a href='ctryedit.php?ctry=", $ctry->code, "&delete=1'>delete</a>";
				}
				
				echo "</td>\n</tr>\n";
			}
		}
		echo "</table>\n";
		if ($this->CanAdminUser("admin"))
		{	echo "<p><a href='ctryedit.php'>Create new country</a></p>\n";
		}
	} // end of fn CtryList
	
	function Countries()
	{	$countries = array();
		$where = array();
		if ($_GET["continent"])
		{	$where[] = "continent='{$_GET["continent"]}'";
		}
		
		$sql = "SELECT countries.*, IF(toplist > 0, 0, 1) AS istoplist FROM countries";
		if ($wstr = implode(" AND ", $where))
		{	$sql .= " WHERE $wstr";
		}
		$sql .= " ORDER BY istoplist, toplist, shortname";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$countries[] = new AdminCountry($row);
			}
		}
		
		return $countries;
	} // end of fn Countries
	
} // end of defn CountriesListPage

$page = new VideosListPage();
$page->Page();
?>