<?php
include_once('sitedef.php');

class MultimediaEmbedPage extends AdminMultimediaPage
{	private $data = array('width'=>600, 'height'=>380, 'auto'=>false);

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function MMLoggedinConstruct()
	{	parent::MMLoggedinConstruct('embed');
		$this->breadcrumbs->AddCrumb('multimediaembed.php?id=' . $this->multimedia->id, 'Embed code');
	} // end of fn MMLoggedinConstruct
	
	protected function MMConstructFunctions()
	{	if ($width = (int)$_POST['width'])
		{	$this->data['width'] = $width;
		}
		if ($height = (int)$_POST['height'])
		{	$this->data['height'] = $height;
		}
		if ($_POST['auto'])
		{	$this->data['auto'] = true;
		}
	} // end of fn MMConstructFunctions
	
	protected function MMBodyMain()
	{	parent::MMBodyMain();
		echo $this->DisplayEmbedCode(), $this->multimedia->EmbedCodeForm($this->data);
	} // end of fn MMBodyMain
	
	private function DisplayEmbedCode()
	{	if ((int)$this->data['width'] && $this->data['height'])
		{	echo '<form id="mmEmbedForm" onsubmit="return false;"><label>Your embed code</label><textarea onclick="this.select();">', $this->multimedia->IFrameEmbedCode($this->data), '</textarea><br /></form><div class="clear"></div>';
		}
	} // end of fn DisplayEmbedCode
	
} // end of defn MultimediaEmbedPage

$page = new MultimediaEmbedPage();
$page->Page();
?>