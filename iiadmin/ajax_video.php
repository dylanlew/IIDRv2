<?php
include_once("sitedef.php");

class AjaxVideo extends Base
{
	public function __construct()
	{	
		$vimeo = new VimeoVideoRequest();
		
		if($vimeo->Get($_GET['id']))
		{
			$json = array(
						'title' => $vimeo->GetTitle(),
						'description' => $vimeo->GetDescription(),
						'duration' => $vimeo->GetDuration(),
						'image' => $vimeo->GetImage(),
						'thumbnail' => $vimeo->GetThumbnail()
						);
			
			header('Content-type: application/json');
			echo json_encode($json);
		}
	}
}

$ajax = new AjaxVideo;

?>