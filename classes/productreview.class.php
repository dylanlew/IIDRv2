<?php
class ProductReview extends BlankItem
{	protected $deftitle = 'Review title';

	public function __construct($id = null)
	{	parent::__construct($id, 'productreviews', 'prid');
	} // fn __construct
	
	public function GetAuthor()
	{	return new Student($this->details['sid']);
	} // end of fn GetAuthor
	
	public function DisplayForReviewer()
	{	ob_start();
		echo '<div class="yourReview"><h4>Reviewed by you ', $this->AgoDateString(strtotime($this->details['revdate'])), '</h4><span class="rating"><span style="width:', $rating = (int)($this->details['rating'] * 100), '%">', $rating, '%</span></span><p>', nl2br($this->InputSafeString($this->details['review'])), '</p></div>';
	//	$this->VarDump($this->details);
		return ob_get_clean();
	} // end of fn DisplayForReviewer
	
	public function ReviewnameFromProduct($producttype = 'store')
	{	switch ($producttype)
		{	case 'store':
				return 'review';
				break;
			default:
				return 'testimonial';
		}
	} // end of fn ReviewnameFromProduct
	
	public function CreateForm($productid = 0, $producttype = 'store', $canreview = true, $student = '')
	{	ob_start();
		if (!$rating = $_POST['rating'])
		{	$rating = 0.6;
		}
		echo '<div class="reviewFormContainer"><h3>Write your ', $reviewname = $this->ReviewnameFromProduct($producttype), ' ...</h3><form onsubmit="return false;"><div class="submitRating"><label>Click your stars to rate</label><div class="submitRatingStars"><input type="hidden" name="rating" id="submitRatingStarsValue" value="', $rating, '" />';
		for ($i = 1; $i <=5; $i++)
		{	echo '<div class="submitRatingStar', ($i / 5) <= $rating ? ' submitRatingStarOn' : '', '" id="ratStar_', $i, '" onclick="RatingStar(', $i, ');"></div>';
		}
		echo '</div><div class="clear"></div></div><p><input type="text" value="', $this->InputSafeString($_POST['revtitle']), '" id="revformTitle" onfocus="SRevFieldClear(\'Title\');" onblur="SRevFieldAddDefault(\'Title\');" /></p><p><textarea id="revformText" onkeydown="return ToggleCantReviewOverlay(true);" onfocus="SRevFieldClear(\'Text\');" onblur="SRevFieldAddDefault(\'Text\');">', $this->InputSafeString($_POST['text']), '</textarea></p>';
		if (is_a($student, 'Student'))
		{	$names = array();
			if ($student->details['city'])
			{	$names[] = $student->details['firstname'] . ', ' . $student->details['city'];
				$names[] = $student->details['firstname'] . ' ' . $student->details['surname'] . ', ' . $student->details['city'];
			}
			$names[] = $student->details['firstname'];
			$names[] = $student->details['firstname'] . ' ' . $student->details['surname'];
			echo '<p><label>Post as</label><select id="revformName">';
			foreach ($names as $name)
			{	echo '<option value="', $name = $this->InputSafeString($name), '">', $name, '</option>';
			}
			echo '</select></p>';
		}
		echo '<p><a class="addtobasket" onclick="SubmitReview(', (int)$productid, ',\'', $this->InputSafeString($producttype), '\');">Submit your ', $reviewname, '</a></p></form>';
	/*	if (!$canreview)
		{	echo '<div class="rfcOverlay" onmouseover="ToggleCantReviewOverlay(true);" onkeyup="ToggleCantReviewOverlay(true);" onmouseout="ToggleCantReviewOverlay(false);" onclick="ToggleCantReviewOverlay();"><div id="rfcOverlayInner">', $this->InputSafeString($overlaytext), '</div></div>';
		}*/
		echo '</div>';
		if (!$_POST['revtitle'])
		{	echo '<script>SRevFieldAddDefault("Title");</script>';
		}
		if (!$_POST['text'])
		{	echo '<script>SRevFieldAddDefault("Text");</script>';
		}
		return ob_get_clean();
	} // end of fn CreateForm
	
} // end of class defn ProductReview
?>