<?php
class BlankItem extends Base
{	var $details = array();
	var $id = '';
	var $tablename = '';
	var $idfield = '';
	
	public function __construct($id = 0, $tablename = '', $idfield = '')
	{	parent::__construct();
		$this->tablename = $tablename;
		$this->idfield = $idfield;
		if ($this->tablename && $this->idfield)
		{	$this->Get($id);
		}
	} // fn __construct
	
	public function Reset()
	{	$this->details = array();
		$this->id = '';
		$this->ResetExtra();
	} // end of fn Reset
	
	protected function ResetExtra()
	{	
	} // end of fn ResetExtra
	
	public function Get($id = '')
	{	$this->Reset();
		if (is_array($id))
		{	$this->details = $id;
			if ($this->id = $id[$this->idfield])
			{	$this->GetExtra();
			}
		} else
		{	if ($id = (int)$id)
			{	if ($result = $this->db->Query('SELECT * FROM ' . $this->tablename . ' WHERE ' . $this->idfield . '=' . $id))
				{	if ($row = $this->db->FetchArray($result))
					{	$this->Get($row);
					}
				}
			}
		}
		
	} // end of fn Get
	
	public function Refresh()
	{	$this->Get($this->id);
	} // end of fn Refresh
	
	protected function GetExtra()
	{	
	} // end of fn GetExtra
	
	public function CanDelete()
	{	return false;
	} // end of fn CanDelete
	
	public function Delete()
	{	if ($this->CanDelete())
		{	$sql = 'DELETE FROM ' . $this->tablename . ' WHERE ' . $this->idfield . '=' . (int)$this->id;
			if ($result = $this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	$this->DeleteExtra();
					$this->Reset();
					return true;
				}
			} //else echo '<p>', $sql, ': ', $this->db->Error(), '</p>';
		} //else echo 'cant delete';
	} // end of fn Delete
	
	protected function DeleteExtra()
	{	
	} // end of fn DeleteExtra
	
} // end of defn BlankItem
?>