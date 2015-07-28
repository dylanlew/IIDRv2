<?php
class MapBuilder extends Base
{	var $db = Object;

	function __construct() // constructor
	{	parent::__construct();
	} // end of fn __construct (constructor)

	function ScanDirectory($dir = "")
	{	$filecount = 0;
		if (substr($dir, -1) != "/")
		{	$dir = "$dir/";
		}
		if ($dhandle = @opendir($dir))
		{	// first clear all sub - directories
			$this->db->Query("DELETE FROM mbdirs WHERE directory='$dir'");
			while (false !== ($file = readdir($dhandle)))
			{	if ($file != "." && $file != "..")
				{	if (is_dir($dir . $file))
					{	$this->db->Query("INSERT INTO mbdirs SET directory='$dir', subdir='$file'");
					} else
					{  	if (substr($file, -4) === ".php")
						{	if ($results = $this->ScanFile($file, $dir))
							{	$this->SaveFile($results);
								$filecount++;
							}
						}
					}
				}
			}
			
			closedir($dhandle);
		}
		return $filecount;
	} // end of fn ScanDirectory
	
	function ScanFile($filename, $directory = "")
	{	if ($lines = file($directory . $filename))
		{	$classes = array();
			$classcount = 0;
			$includes = array();
			$objects = array();
			foreach ($lines as $line)
			{	//$line = strtolower(trim($line));
				
				// check for continuation of comment
				if ($comment_cont)
				{	$endcomm_pos = strpos($line, "*/");
					if ($endcomm_pos === false)
					{	continue; // whole line is part of comment, check next one
					} else
					{	$comment_cont = false;
						if (!($line = substr($line, $endcomm_pos + 2)))
						{	continue;
						}
					}
				}
				
				// check for start of long comment
				$comm_pos = strpos($line, "/*");
				if ($comm_pos !== false)
				{	$comment_cont = true;
					if (!($line = substr($line, 0, $comm_pos)))
					{	continue;
					}
				}
				
				if ($cont)
				{	$line = "$cont\n$line";
				} else
				{	$line = trim($line, "@");
				}
				// ignore any comment lines
				if (substr($line, 0, 2) == "//")
				{	continue;
				}
				
				// strip comments from end
				if ($comm_pos = strpos($line, "//"))
				{	$line = substr($line, 0, $comm_pos);
				}
				$line = trim($line, "{}");
				$line = trim($line);
				// now only interested if line is function/class/var/include/require
				if (substr(strtolower($line), 0, 6) == "class ")
				{	$name = explode(" extends ", trim(substr($line, 6)));
					$classcount++;
					$classes[$classcount] = array("name"=>$name[0], "extendname"=>$name[1], "var"=>array(), "functions"=>array(), "objects"=>array());
				} else
				{	// check for functions
					if (stristr(strtolower($line), "function"))
					{	//echo $line, "**", substr($line, -1), "**<br />\n";
						if (substr($line, -1) == ")")
						{	$classes[$classcount]["functions"][] = $line;
						} else
						{	$cont = $line;
							continue;
						}
					} else // check for var
					{	if (substr(strtolower($line), 0, 4) == "var ")
						{	if (substr($line, -1) == ";")
							{	$classes[$classcount]["var"][] = $line;
							} else
							{	$cont = $line;
								continue;
							}
						} else
						{	//check for creation of new object
							if (($newline = stristr($line, "= new")) || ($newline = stristr($line, "=new")))
							{	if ($newline = trim(str_replace(array("= new", "=new"), "", $newline)))
								{	if ($bracket_pos = strpos($newline, '('))
									{	if (($obj_name = substr($newline, 0, $bracket_pos)) && ($obj_name != "Array"))
										{	$objects[$obj_name] = $obj_name;
										}
									}
								}
							} else // check for includes
							{	if (substr(strtolower($line), 0, 7) == "include")
								{	if (substr($line, -1) == ";")
									{	$includes[] = $line;
									} else
									{	$cont = $line;
										continue;
									}
								}
								if (substr(strtolower($line), 0, 7) == "require")
								{	if (substr($line, -1) == ";")
									{	$includes[] = $line;
									} else
									{	$cont = $line;
										continue;
									}
								}
							}
						}
					}
				}
				$cont = "";
				
				//echo htmlentities($line), "**<br />\n";
				
			}
			return array("directory"=>$directory, "file"=>$filename, "includes"=>$includes, "classes"=>$classes, "objects"=>$objects);
			
		}
	} // end of defn ScanFile
	
	function SaveFile($file)
	{	// first check if already saved
		if ($result = $this->db->Query("SELECT fileid FROM mbfiles 
						WHERE filename='{$file["file"]}' AND directory='{$file["directory"]}'"))
		{	if ($row = $this->db->FetchArray($result))
			{	if ($fileid = $row["fileid"])
				{	$this->db->Query("DELETE FROM mbclasses WHERE fileid=$fileid");
					$this->db->Query("DELETE FROM mbfunctions WHERE fileid=$fileid");
					$this->db->Query("DELETE FROM mbincludes WHERE fileid=$fileid");
					$this->db->Query("DELETE FROM mbvars WHERE fileid=$fileid");
					$this->db->Query("DELETE FROM mbobjects WHERE fileid=$fileid");
				}
			}
		}
		
		if (!$fileid)
		{	if ($result = $this->db->Query("INSERT INTO mbfiles SET filename='{$file["file"]}', 
								directory='{$file["directory"]}', scandate=NOW()"))
			{	$fileid = $this->db->InsertID();
			}
		} else
		{	$this->db->Query("UPDATE mbfiles SET scandate=NOW() WHERE fileid=$fileid");
		}
		
		foreach ($file["classes"] as $class)
		{	if ($result = $this->db->Query("INSERT INTO mbclasses SET fileid=$fileid, 
								classname='{$class["name"]}', extendname='{$class["extendname"]}'"))
			{	if ($classid = $this->db->InsertID())
				{	// now write vars
					if ($class["var"])
					{	foreach ($class["var"] as $var)
						{	$this->db->Query("INSERT INTO mbvars SET fileid=$fileid, 
									classid=$classid, vardec='$var'");
						}
					}
					// now write functions
					if ($class["functions"])
					{	foreach ($class["functions"] as $func)
						{	$this->db->Query("INSERT INTO mbfunctions SET fileid=$fileid, 
									classid=$classid, funcdec='$func'");
						}
					}
				}
			}
		}
		
		// now update file ids for parent classes
		if ($file["classes"])
		{	$sql = "SELECT * FROM mbclasses WHERE fileid=$fileid";
			if ($result = $this->db->Query($sql))
			{	while ($row = $this->db->FetchArray($result))
				{	$fid_sql = "SELECT * FROM mbclasses WHERE classname='{$row["extendname"]}'";
					if ($fid_result = $this->db->Query($fid_sql))
					{	if (($fid_row = $this->db->FetchArray($fid_result)) && ($fid = (int)$fid_row["fileid"]))
						{	$this->db->Query("UPDATE mbclasses SET extendid=$fid WHERE classid={$row["classid"]}");
						}
					}
					
				}
			}
		}

		// now write objects created
		if ($file["objects"])
		{	foreach ($file["objects"] as $object)
			{	$objfile = 0;
				if ($result = $this->db->Query("SELECT * FROM mbclasses WHERE classname='$object'"))
				{	if ($row = $this->db->FetchArray($result))
					{	$objfile = (int)$row["fileid"];
					}
				}
				$this->db->Query("INSERT INTO mbobjects SET fileid=$fileid, objectname='$object', objectid=$objfile");
			}
		}
		
		// now write includes
		if ($file["includes"])
		{	foreach ($file["includes"] as $inc)
			{	$this->db->Query("INSERT INTO mbincludes SET fileid=$fileid, includedec='$inc'");
			}
		}
		
	} // end of defn SaveFile
	
	function DeleteObsolete()
	{	$sql = "SELECT * FROM mbfiles ORDER BY directory, filename";
		$deleted = 0;
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if (!file_exists($row["directory"] . $row["filename"]))
				{	if ($this->DeleteFile($row["fileid"]))
					{	echo "\"", $row["directory"], $row["filename"], "\" removed<br />";
						$deleted++;
					}
				}
			}
		}
		return $deleted;
	} // end of fn DeleteObsolete
	
	function DeleteFile($fileid = 0)
	{	if ($result = $this->db->Query("DELETE FROM mbfiles WHERE fileid=$fileid"))
		{	if ($this->db->AffectedRows())
			{	$this->db->Query("DELETE FROM mbclasses WHERE fileid=$fileid");
				$this->db->Query("DELETE FROM mbfunctions WHERE fileid=$fileid");
				$this->db->Query("DELETE FROM mbincludes WHERE fileid=$fileid");
				$this->db->Query("DELETE FROM mbvars WHERE fileid=$fileid");
				$this->db->Query("DELETE FROM mbobjects WHERE fileid=$fileid");
				return 1;
			}
		}
	} // end of fn DeleteFile
	
} // end of class defn MapBuilder
?>