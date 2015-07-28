<?php
include_once('sitedef.php');

class IncomePerMonthGraph extends Graph
{	var $ystart = 0;
	var $mstart = 0;
	var $yend = 0;
	var $mend = 0;
	
	function __construct()
	{	
		if (($ys = (int)$_GET['ystart']) && ($ms = (int)$_GET['mstart']))
		{	$this->ystart = $ys;
			$this->mstart = $ms;
		} else
		{	$this->ystart = date('Y', strtotime('-11 months'));
			$this->mstart = date('m', strtotime('-11 months'));
		}
		if (($ye = (int)$_GET['yend']) && ($me = (int)$_GET['mend']))
		{	$this->yend = $ye;
			$this->mend = $me;
		} else
		{	$this->yend = date('Y');
			$this->mend = date('m');
		}

		$this->titleString = 'Income per month ' . date('M Y', mktime(0,0,0,$this->mstart,1,$this->ystart)) . ' to ' . date('M Y', mktime(0,0,0,$this->mend,1,$this->yend));
		parent::__construct();
		
	} //end of fn __construct
	
	protected function GetData()
	{	$this->legend = array('£ per month');
		
		$ia = new IncomeAnalysis();
		foreach ($ia->IncomePerMonth($this->mstart, $this->ystart, $this->mend, $this->yend) as $month)
		{	$this->data[] = array('n'=>date('M', $month['stamp']), 'y'=>array($month['income']));
		}
	} // end of GetData
	
} // end of fn IncomePerMonthGraph

$graph = new IncomePerMonthGraph();
$graph->OutPut();
?>