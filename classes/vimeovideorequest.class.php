<?php

class VimeoVideoRequest extends Base
{
	public $details = array();
	private $url = "http://vimeo.com/api/v2/video/%s.json";
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function Get($id = null)
	{
		if(!is_null($id))
		{
			if($data = @file_get_contents(sprintf($this->url, (string)$id)))
			{
				$data = json_decode($data, true);
				return $this->details = $data[0];
			}
		}
	}
	
	public function GetThumbnail()
	{
		return $this->details['thumbnail_large'];
	}
	
	public function GetImage()
	{
		return $this->details['thumbnail_large'];
	}
	
	public function GetDuration()
	{
		return $this->details['duration'];
	}
	
	public function GetDescription()
	{
		return $this->details['description'];
	}
	
	public function GetTitle()
	{
		return $this->details['title'];
	}
	
	
	
}

?>