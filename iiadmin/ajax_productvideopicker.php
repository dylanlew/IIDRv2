<?php
include_once('sitedef.php');

class AjaxProductPicker extends AdminProductsPage
{	private $allowed_types = array('youtube', 'vimeo');
	private $vid_picked = 0;

	function __construct()
	{	parent::__construct();
	} //  end of fn __construct
	
	function ProductsLoggedInConstruct()
	{	parent::ProductsLoggedInConstruct();
		
		$this->product = new AdminStoreProduct($_GET['id']);
		$this->vid_picked = (int)$_GET['vidid'];
		
		echo $this->VideoList();
		
	} // end of fn ProductsLoggedInConstruct
	
	public function VideoList()
	{	ob_start();
		if ($cats = $this->GetVideoList())
		{	echo '<ul class="mmChooser">';
			foreach ($cats as $catid=>$cat)
			{	echo '<li><h4><a onclick="ToggleVidList(', $catid, ');">', $cat['name'], '</a></h4><ul style="display:', $cat['vids'][$this->vid_picked] ? 'block' : 'none', ';" class="mmChooserVidList" id="mmChooserVidList', $catid, '">';
				foreach ($cat['vids'] as $vid_row)
				{	$vid = new AdminMultiMedia($vid_row);
					echo '<li><a onclick="PickVideo(', $this->vid_picked == $vid->id ? '0, \'none\'' : ($vid->id . ',\'' . $vid->AdminDescription() . '\''), ');">', $this->vid_picked == $vid->id ? 'remove video' : 'use this', '</a>&nbsp;-&nbsp;<span>', $vid->AdminDescription(), '</span></li>';
				}
				echo '</ul></li>';
			}
			echo '</ul>';
		} else
		{	echo '<p>no videos available</p>';
		}
		return ob_get_clean();
	} // end of fn VideoList
	
	public function GetVideoList()
	{	$cats  = $this->GetPossibleCats();
		$sql = 'SELECT * FROM multimedia';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$mm = new MultiMedia($row);
				if (in_array($mm->MediaType(), $this->allowed_types))
				{	foreach ($mm->cats as $catid=>$cat)
					{	if ($cats[$catid])
						{	$cats[$catid]['vids'][$mm->id] = $row;
						}
					}
				}
			}
		}
		foreach ($cats as $catid=>$cat)
		{	if (!$cat['vids'])
			{	unset($cats[$catid]);
			}
		}
		return $cats;
	} // end of fn GetVideoList
	
	function GetPossibleCats($parentid = 0, $pretext = '')
	{
		$cats = array();
		$sql = 'SELECT * FROM libcats WHERE parentid=' . $parentid . ' ORDER BY lcorder';
		
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	if (!$this->subcats[$row['lcid']])
				{	$cats[$row['lcid']] = array('name'=>$pretext . $this->InputSafeString($row['lcname']), 'vids'=>array());
					if ($subcats = $this->GetPossibleCats($row['lcid'], $cats[$row['lcid']]['name'] . '&nbsp;-&nbsp;'))
					{	foreach ($subcats as $subid=>$subcat)
						{	$cats[$subid] = $subcat;
						}
					}
				}
			}
		}
		
		return $cats;
		
	} // end of fn GetPossibleCats
	
} // end of defn AjaxProductPicker

$page = new AjaxProductPicker();
?>