<?php
class MapSearcher extends Base
{	var $db = Object;

	function __construct() // constructor
	{	parent::__construct();
	} // end of fn __construct (constructor)

	function Directories()
	{	$dirlist = array();
		$sql = "SELECT directory, MAX(scandate) AS lastscan, COUNT(fileid) AS filecount FROM mbfiles 
								GROUP BY directory ORDER BY directory";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$dirlist[$row["directory"]] = $row;
			}
		}
		
		return $dirlist;
		
	} // end of fn Directories

	function FileList($dir = "")
	{	$filelist = array();
		$sql = "SELECT fileid, filename FROM mbfiles WHERE directory='$dir' ORDER BY filename";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$filelist[$row["fileid"]] = $row["filename"];
			}
		}
		
		return $filelist;
		
	} // end of fn FileList

	function SubDirList($dir = "")
	{	$subdirs = array();
		$sql = "SELECT subdir FROM mbdirs WHERE directory='$dir' ORDER BY subdir";
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$subdirs[$row["subdir"]] = $row["subdir"];
			}
		}
		
		return $subdirs;
		
	} // end of fn SubDirList

	function GetFileByID($fileid = 0)
	{	$file = array("filename"=>"", "directory"=>"", "classes"=>array(), "includes"=>array(), "objects"=>array());
		$fsql = "SELECT * FROM mbfiles WHERE fileid=$fileid";
		if ($fresult = $this->db->Query($fsql))
		{	while ($frow = $this->db->FetchArray($fresult))
			{	$file["filename"] = $frow["filename"];
				$file["directory"] = $frow["directory"];
			}
		}
		
		$csql = "SELECT * FROM mbclasses WHERE fileid=$fileid ORDER BY classid";
		if ($cresult = $this->db->Query($csql))
		{	while ($crow = $this->db->FetchArray($cresult))
			{	$file["classes"][$cid = $crow["classid"]] = array("name"=>$crow["classname"], "extendname"=>$crow["extendname"], "extendid"=>$crow["extendid"], "vars"=>array(), "functions"=>array());
				$vsql = "SELECT * FROM mbvars WHERE classid=$cid";
				if ($vresult = $this->db->Query($vsql))
				{	while ($vrow = $this->db->FetchArray($vresult))
					{	$file["classes"][$cid]["vars"][$vrow["varid"]] = $vrow["vardec"];
					}
				}
				$fsql = "SELECT * FROM mbfunctions WHERE classid=$cid ORDER BY functionid";
				if ($fresult = $this->db->Query($fsql))
				{	while ($frow = $this->db->FetchArray($fresult))
					{	$file["classes"][$cid]["functions"][$frow["functionid"]] = $frow["funcdec"];
					}
				}
			}
		}
		
		$isql = "SELECT * FROM mbincludes WHERE fileid=$fileid ORDER BY includedec";
		if ($iresult = $this->db->Query($isql))
		{	while ($irow = $this->db->FetchArray($iresult))
			{	$file["includes"][$irow["includeid"]] = $irow["includedec"];
			}
		}
		
		$isql = "SELECT * FROM mbobjects WHERE fileid=$fileid ORDER BY objectname";
		if ($iresult = $this->db->Query($isql))
		{	while ($irow = $this->db->FetchArray($iresult))
			{	$file["objects"][$irow["objid"]] = $irow;
			}
		}
		return $file;
	} // end of fn GetFileByID
	
} // end of class defn MapSearcher
?>