<?php
include_once("sitedef.php");

class PageQuotePage extends AdminPageQuotesPage
{	
	function __construct()
	{	parent::__construct();
	} //  end of fn __construct

	protected function PageQuotesConstruct()
	{	parent::PageQuotesConstruct();
	} // end of fn PageQuotesConstruct
	
	protected function PageQuotesMainContent()
	{	ob_start();
		echo '<table><tr class="newlink"><th colspan="4"><a href="pagequote.php">New Quote</a></th></tr><tr><th>Quote ID</th><th>Quote sample</th><th>Live?</th><th>Actions</th></tr>';
		foreach ($this->GetQuotes() as $quote_row)
		{	$quote = new AdminPageQuote($quote_row);
			echo '<tr><td>', $quote->id, '</td><td>', $quote->SampleText(), ' ...</td><td>', $quote->details['live'] ? 'Yes' : '', '</td><td><a href="pagequote.php?id=', $quote->id, '">edit</a>&nbsp;|&nbsp;<a href="pagequote.php?id=', $quote->id, '&delete=1">delete</a></td></tr>';
		}
		echo '</table>';
		return ob_get_clean();
	} // end of fn PageQuotesMainContent

	private function GetQuotes()
	{	$quotes = array();
		$sql = 'SELECT * FROM pagequotes ORDER BY pqid';
		if ($result = $this->db->Query($sql))
		{	while ($row = $this->db->FetchArray($result))
			{	$quotes[] = $row;
			}
		}
		return $quotes;
	} // end of fn GetQuotes
	
} // end of defn PageQuotePage

$page = new PageQuotePage();
$page->Page();
?>