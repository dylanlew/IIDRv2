<?php
include_once('sitedef.php');

class CourseEditPage extends AdminCourseEditPage
{
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function CoursesLoggedInConstruct()
	{	parent::CoursesLoggedInConstruct();
		
		echo $this->ListVideos();
		
	} // end of fn CoursesLoggedInConstruct
	
	private function ListVideos()
	{	ob_start();
		if ($videos = $this->GetVideos())
		{	echo '<ul>';
			$file_types = array('youtube', 'vimeo', 'mp3', 'mp4');
			foreach ($videos as $video_row)
			{	$video = new MultiMedia($video_row);
				if (in_array($video->MediaType(), $file_types))
				{	echo '<li>', $mmname = $this->InputSafeString($video->details['mmname']), ' - ', $video->id == $_GET['picked'] ? '{this is the current video} ' : '', '<a onclick="CVideoChoose(\'', $video->id == $_GET['picked'] ? '0' : $video->id, '\',\'', $video->id == $_GET['picked'] ? 'none' : addslashes($video->details['mmname']), '\');">', $video->id == $_GET['picked'] ? 'remove' : 'add this', '</a></li>';
				}
			}
			echo '</ul>';
		} else
		{	echo '<h3>No Videos Available</h3>';
		}
		return ob_get_clean();
	} // end of fn ListVideos
	
	private function GetVideos()
	{	$videos = array();
		$sql = 'SELECT * FROM multimedia ORDER BY mmname';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$videos[] = $row;
			}
		}
		return $videos;
	} // end of fn GetVideos
	
} // end of defn CourseEditPage

$page = new CourseEditPage();
?>