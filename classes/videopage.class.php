<?php

class VideoPage extends BasePage
{
	function __construct($video)
	{	parent::__construct("video");
	
		$this->video = new Video($video);
	
		if (!$this->video->id || !$this->video->CanView())
		{	$this->Redirect('video.php');
		}
		
		$this->AddBreadcrumb('Multimedia', $this->link->GetLink('multimedia.php'));
		$this->AddBreadcrumb($this->video->details['vtitle']);
		
		$this->title .= ' - ' . $this->InputSafeString($this->video->details['vtitle']);
	}
	
	function MainBodyContent()
	{
		echo "<div class='col3-layout'>";
		echo "<div class='col2-wrapper'>";	
		echo "<h1>". $this->InputSafeString($this->video->details["vtitle"]) ."</h1>\n";
		echo $this->video->Output();
		echo "<h3>". $this->InputSafeString($this->video->details["vtitle"]) ."</h3>";
		echo "<p>Uploaded ". date("d F Y", strtotime($this->video->details["dateadded"])) ."</p>";
		echo $this->video->details["vdesc"];
		echo "</div>";
		
		echo "<div class='col'>";
		echo "<h3>Related Videos</h3>";
		echo "</div>";
		
		echo "</div>";
		
		echo "<div class='clear'></div>";
		
		echo $this->video->CategoryListing();
		
	}
	
	function PopularVideos()
	{
		
	}
	
	
}

?>