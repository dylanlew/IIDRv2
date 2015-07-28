<?php
class DbConnect
{	var $server; // name of database to connect to
	var $handle; // database handle for object
	var $database;
	var $phpver = 0;
	// reinstate below for ver 5
	static $st_handle = 0; // static handle to be reused
	static $st_parameters = array();
	public static $instance;

	function __construct() // constructor
	{	
		// below is ver 5 code
		if (self::$st_parameters)
		{	$parameters = self::$st_parameters;
		} else
		{	$parameters = $this->GetParameters();
			self::$st_parameters = $parameters;
		}

		
		$this->Connect($parameters["host"], $parameters["user"], $parameters["password"]);
		@mysql_select_db($this->database = $parameters["database"]);
	} // end of fn __construct

	function GetParameters() // assign database connection parameters
	{	$parameters = array("host" => DB_HOST, 
							"user" => DB_USER,
							"password" => DB_PASS,
							"database" => DB_NAME);
		/*$parameters = array("host" => "localhost", 
							"user" => "root",
							"password" => "websq1234",
							"database" => "nzf");*/
		return $parameters;
	} // end of fn GetParameters
	
	public static function GetInstance()
	{
		if (!isset(self::$instance))
		{	self::$instance = new DbConnect();	
		}
		
		return self::$instance;
	} // end of fn GetInstance

	function Query($sql = "")
	{	//echo $sql, "<br />";
		if ($this->handle)
		{	$result = mysql_query($sql);
		//	if ($fh = fopen("sqllog.txt", "a"))
		//	{	fputs($fh, "" . $sql . " - " . $_SERVER["SCRIPT_NAME"] . implode(".", explode(" ",microtime())) . "\r\n");
		//		fclose($fh);
		//	}
			return $result;
		} else
		{	return false;
		}
	} // end of fn Query

	function PHP4Connect($server, $user, $pass)
	{	if ($this->handle) $this->Close;
		$this->handle = @mysql_connect($server, $user, $pass);
	} // end of fn PHP4Connect

	// below id for version 5 only
	function Connect($server, $user, $pass)
	{	if (self::$st_handle)
		{	$this->handle = self::$st_handle;
		} else
		{	$this->handle = @mysql_connect($server, $user, $pass);
			self::$st_handle = $this->handle;
		}
	} // end of fn Connect

	function Close()
	{	if ($this->handle) @mysql_close($this->handle);
	} // end of fn Close

	function FetchArray($result)
	{	if ($result) return mysql_fetch_array($result, MYSQL_ASSOC);
		return false;
	} // end of fn FetchArray

	function NumRows($result)
	{	if ($result) return mysql_num_rows($result);
		return 0;
	} // end of fn NumRows

	function AffectedRows()
	{	return mysql_affected_rows($this->handle);
	} // end of fn AffectedRows
	
	function InsertID()
	{	return mysql_insert_id($this->handle);
	} // end of fn InsertID

	function Error()
	{	return mysql_error();
	} // end of fn Error

	function FreeResult($result)
	{	return @mysql_free_result($result);
	} // end of fn FreeResult
	
	function Version()
	{	if ($ver = @mysql_get_server_info($this->handle))
		{	$verstr = substr($ver, 0, strpos($ver, "-"));
			return $verstr;
			
		} else return "";
	} // end of fn Version

} // end of class defn DbConnect
?>