<?php
class StoreProductDownload extends BlankItem
{	protected $file_dir = '/product_downloads/';
	public $valid_types = array('pdf'=>array('Content-Type'=>'application/pdf', 'label'=>'pdf'));
	
	function __construct($id = '')
	{	parent::__construct($id, 'storeproductfiles', 'pfid');
		$this->file_dir = CITDOC_ROOT . $this->file_dir;
	} // fn __construct
	
	public function FileLocation()
	{	if (file_exists($filename = $this->FileFilename()))
		{	return $filename;
		}
		return '';
	} // end of fn FileLocation
	
	protected function FileFilename()
	{	return $this->file_dir . $this->id . '.pdl';
	} // end of fn FileFilename
	
	public function FileExists()
	{	return file_exists($this->FileFilename());
	} // end of fn FileExists
	
	public function DownloadName()
	{	return $this->details['fileslug'] . '.' . $this->details['filetype'];
	} // end of fn DownloadName
	
	public function CanDownload($user = false)
	{	return $user->id && $user->StoreProductPurchased($this->details['prodid']);
	} // end of fn CanDownload
	
/*	
	public function RecordDownload()
	{	
	} // end of fn RecordDownload
	
	public function DownloadCount($timeback = '')
	{	$sql = 'SELECT COUNT(viewed) AS viewcount FROM multimediaviews WHERE mmid=' . $this->id;
		if ($timeback)
		{	$sql .= ' AND viewed>"' . $this->datefn->SQLDateTime($timeback) . '"';
		}
		if ($result = $this->db->Query($sql))
		{	if ($row = $this->db->FetchArray($result))
			{	return (int)$row['viewcount'];
			}
		}
		return 0;
	} // end of fn ViewCount
	*/
	public function DownloadLink()
	{	return SITE_URL . 'product_download/' . $this->id . '/' . $this->DownloadName();
	} // end of fn Link
	
} // end of defn StoreProductDownload
?>