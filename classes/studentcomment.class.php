<?php
class StudentComment extends BlankItem
{	
	public function __construct($id = null)
	{	parent::__construct($id, 'studentcomments', 'scid');
	} // fn __construct
	
	public function GetAuthor()
	{	return new Student($this->details['sid']);
	} // end of fn GetAuthor
	
	public function CreateForm($productid = 0, $producttype = 'askimamquestions', $canreview = true)
	{	ob_start();
		echo '<div class="reviewFormContainer"><h3>Your comments ...</h3><form id="revformForm" onsubmit="return false;"><textarea id="revformText" onkeydown="return ToggleCantReviewOverlay(true);"></textarea>';
		/*echo '<p class="submitRating">Your rating (1 to 5) ';
		if (!$rating = $_POST['rating'])
		{	
			$rating = 0.6;
		}
		for ($i = 0.2; $i <=1; $i+=0.2)
		{	echo '<input type="radio" name="rating" id="rating_', (int)$count++, '" value="', $i, '" ', (round($i, 1) == $rating) ? 'checked="checked" ' : '', '/>';
		}
		echo '</p>';*/
		echo '<p><a class="addtobasket" onclick="SubmitComment(', (int)$productid, ',\'', $this->InputSafeString($producttype), '\');">Post your comments</a></p></form>';
		if (!$canreview)
		{	echo '<div class="rfcOverlay" onmouseover="ToggleCantReviewOverlay(true);" onkeyup="ToggleCantReviewOverlay(true);" onmouseout="ToggleCantReviewOverlay(false);" onclick="ToggleCantReviewOverlay();"><div id="rfcOverlayInner">You must be registered and logged in to leave comments</div></div>';
		}
		echo '</div>';
		return ob_get_clean();
	} // end of fn CreateForm
	
} // end of class defn ProductReview
?>