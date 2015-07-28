<?php
include_once('sitedef.php');

class IncomePerDayGraph extends Graph
{	var $month= 0;
	var $year = 0;
	
	function __construct()
	{	
		if (!$this->month = (int)$_GET['mdate'])
		{	$this->month = date('n');
		}
		if (!$this->year = (int)$_GET['ydate'])
		{	$this->year = date('Y');
		}

		$this->titleString = 'Income per day for ' . date('F Y', mktime(0,0,0,$this->month,1,$this->year));
		parent::__construct();
		
	} //end of fn __construct
	
	protected function GetData()
	{	$this->legend = array('£ per day');
		
		$ia = new IncomeAnalysis();
		foreach ($ia->IncomePerDayForMonth($this->month, $this->year) as $day)
		{	$this->data[] = array('n'=>date('j', $day['stamp']), 'y'=>array($day['income']));
		}
	} // end of GetData
	
} // end of fn IncomePerDayGraph


$graph = new IncomePerDayGraph();
$graph->OutPut();
?>