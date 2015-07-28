<?php
class AdminBundle extends Bundle
{	var $admintitle = '';
	
	function __construct($id = '')
	{	parent::__construct($id);
		$this->GetAdminTitle();
	} // fn __construct
	
	function GetAdminTitle()
	{	if ($this->id)
		{	$this->admintitle = $this->details['bname'];
		}
	} // end of fn GetAdminTitle
	
	function CanDelete()
	{	
		if ($this->id)
		{	// check for bundles purchased
			//$sql = 'SELECT * FROM course_mm WHERE mmid=' . $this->id;
			if ($result = $this->db->Query($sql))
			{	if ($row = $this->db->FetchArray($result))
				{	return false;
				}
			}
			return true;
		}
		
		return false;

	} // end of fn CanDelete
	
	function Delete()
	{	if ($this->CanDelete())
		{	if ($result = $this->db->Query('DELETE FROM bundles WHERE bid=' . (int)$this->id))
			{	if ($this->db->AffectedRows())
				{	$this->db->Query('DELETE FROM bundleproducts WHERE bid=' . (int)$this->id);
					$this->RecordAdminAction(array('tablename'=>'bundles', 'tableid'=>$this->id, 'area'=>'bundles', 'action'=>'deleted'));
					$this->Reset();
					return true;
				}
			}
		}
		return false;
	} // end of fn Delete
	
	public function AddProduct($prodid = 0, $ptype = '')
	{	$fail = array();
		$success = array();
		
		if (!$this->IsProductBundled($prodid, $ptype))
		{	$fields = array();
			
			if ($bid = (int)$this->id)
			{	$fields[] = 'bid=' . $bid;
			} else
			{	$fail[] = 'bundle not found';
			}
			
			$fields[] = 'ptype="' . $this->SQLSafe($ptype) . '"';
			
			if (($product = $this->GetProduct($prodid, $ptype)) && ($pid = (int)$product->id))
			{	$fields[] = 'pid=' . $pid;
			} else
			{	$fail[] = 'product not found';
			}
			
			// put at end of list
			$listorder = 10;
			if ($last_product = array_pop($dup_product = $this->products))
			{	$listorder = $last_product['listorder'] + 10;
			}
			
			$fields[] = 'listorder=' . $listorder;
			
			if (!$fail)
			{	$sql = 'INSERT INTO bundleproducts SET ' . implode(',', $fields);
				if ($result = $this->db->Query($sql))
				{	if ($this->db->AffectedRows())
					{	$this->GetProducts();
						$success[] = 'product added to bundle';
					}
				}
			}
			
		} else
		{	$fail[] = 'product already bundled';
		}
		
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));
		
	} // end of fn AddProduct
	
	public function RemoveProduct($bpid = 0)
	{	$fail = array();
		$success = array();
		
		if ($this->products[$bpid])
		{	$sql = 'DELETE FROM bundleproducts WHERE bpid=' . $bpid;
			if ($result = $this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	$this->GetProducts();
					$success[] = 'product removed from bundle';
				}
			}
		} else
		{	$fail[] = 'product not bundled';
		}
		
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));
		
	} // end of fn RemoveProduct
	
	public function AmendProducts($data = array())
	{	$fail = array();
		$success = array();
		
		if (is_array($data['listorder']))
		{	$amended = 0;
			foreach ($data['listorder'] as $pbid=>$listorder)
			{	if ($this->products[$pbid])
				{	$fields = array();
					$fields[] = 'listorder=' . (int)$listorder;
					$sql = 'UPDATE bundleproducts SET ' . implode(', ', $fields) . ' WHERE pbid=' . (int)$pbid;
					if ($result = $this->db->Query($sql))
					{	if ($this->db->AffectedRows())
						{	$amended++;
						}
					}
				}
			}
			if ($amended)
			{	$success[] = (int)$amended . ' products updated';
			}
		}
		
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));
		
	} // end of fn AmendProducts
	
	private function ValidSlug($slug = '')
	{	$rawslug = $slug = $this->TextToSlug($slug);
		while ($this->SlugExists($slug))
		{	$slug = $rawslug . ++$count;
		}
		return $slug;
	} // end of fn ValidSlug
	
	private function SlugExists($slug = '')
	{	$sql = 'SELECT bid FROM bundles WHERE bslug="' . $slug . '"';
		if ($this->id)
		{	$sql .= ' AND NOT bid=' . $this->id;
		}
		if ($result = $this->db->Query($sql))
		{	if ($row = $this->db->FetchArray($result))
			{	return $row['bid'];
			}
		}
		return false;
	} // end of fn SlugExists
	
	function Save($data = array())
	{	$fail = array();
		$success = array();
		$fields = array();
		$admin_actions = array();
		
		if ($bname = $this->SQLSafe($data['bname']))
		{	$fields[] = 'bname="' . $bname . '"';
			if ($this->id && ($data['bname'] != $this->details['bname']))
			{	$admin_actions[] = array('action'=>'Title', 'actionfrom'=>$this->details['bname'], 'actionto'=>$data['bname']);
			}
		} else
		{	$fail[] = 'title missing';
		}
	
		if ($bslug = $this->ValidSlug($this->id ? $data['bslug'] : $bname))
		{	$fields[] = 'bslug="' . $bslug . '"';
			if ($this->id && ($data['bslug'] != $this->details['bslug']))
			{	$admin_actions[] = array('action'=>'Slug', 'actionfrom'=>$this->details['bslug'], 'actionto'=>$data['bslug']);
			}
		} else
		{	if ($bname)
			{	$fail[] = 'slug missing';
			}
		}
		
		$bdesc = $this->SQLSafe($data['bdesc']);
		$fields[] = 'bdesc="' . $bdesc . '"';
		if ($this->id && ($data['bdesc'] != $this->details['bdesc']))
		{	$admin_actions[] = array('action'=>'Description', 'actionfrom'=>$this->details['bdesc'], 'actionto'=>$data['bdesc']);
		}
		
		if ($discount = round($data['discount'], 2))
		{	$fields[] = 'discount=' . $discount;
			if ($this->id && ($data['discount'] != $this->details['discount']))
			{	$admin_actions[] = array('action'=>'Discount', 'actionfrom'=>$this->details['discount'], 'actionto'=>$data['discount']);
			}
		} else
		{	$fail[] = 'discount missing';
		}
		
		
		$live = ($data['live'] ? '1' : '0');
		$fields[] = 'live=' . $live;
		if ($this->id && ($live != $this->details['live']))
		{	$admin_actions[] = array('action'=>'Live?', 'actionfrom'=>$this->details['live'], 'actionto'=>$live, 'actiontype'=>'boolean');
		}

		if ($this->id || !$fail)
		{	$set = implode(", ", $fields);
			if ($this->id)
			{	$sql = 'UPDATE bundles SET ' . $set . ' WHERE bid=' . $this->id;
			} else
			{	$sql = 'INSERT INTO bundles SET ' . $set;
			}
			if ($this->db->Query($sql))
			{	if ($this->db->AffectedRows())
				{	if ($this->id)
					{	$record_changes = true;
						$success[] = 'Changes saved';
					} else
					{	$this->id = $this->db->InsertID();
						$success[] = 'New bundle created';
						$this->RecordAdminAction(array('tablename'=>'bundles', 'tableid'=>$this->id, 'area'=>'bundles', 'action'=>'created'));
					}
					$this->Get($this->id);
				}
			
				if ($record_changes)
				{	$base_parameters = array('tablename'=>'bundles', 'tableid'=>$this->id, 'area'=>'bundles');
					if ($admin_actions)
					{	foreach ($admin_actions as $admin_action)
						{	$this->RecordAdminAction(array_merge($base_parameters, $admin_action));
						}
					}
				}
			}
		}
		
		return array('failmessage'=>implode(', ', $fail), 'successmessage'=>implode(', ', $success));
		
	} // end of fn Save
	
	function InputForm()
	{	
		ob_start();

		if (!$data = $this->details)
		{	$data = $_POST;
		}
		
		$form = new Form($_SERVER['SCRIPT_NAME'] . '?id=' . $this->id);
		$form->AddTextInput('Name', 'bname', $this->InputSafeString($data['bname']), 'long', 255, 1);
		if ($this->id)
		{	$form->AddTextInput('Slug', 'bslug', $this->InputSafeString($data['bslug']), 'long', 255, 1);
		}
		
		$form->AddCheckBox('Live', 'live', '1', $data['live']);
		$form->AddTextInput('Discount', 'discount', number_format($data['discount'], 2, '.', ''), 'short number', 10, 1);
		$form->AddTextArea('Description', 'bdesc', $this->InputSafeString($data['bdesc']), '', 0, 0, 10, 40);
		
		$form->AddSubmitButton('', $this->id ? 'Save Changes' : 'Create New Bundle', 'submit');
		if ($histlink = $this->DisplayHistoryLink('bundles', $this->id))
		{	echo '<p>', $histlink, '</p>';
		}
		if ($this->id)
		{	if ($this->CanDelete())
			{	echo '<p><a href="', $_SERVER['SCRIPT_NAME'], '?id=', $this->id, '&delete=1', $_GET['delete'] ? '&confirm=1' : '', '">', $_GET['delete'] ? 'please confirm you really want to ' : '', 'delete this bundle</a></p>';
			}
		}
		$form->Output();
		return ob_get_clean();
	} // end of fn InputForm
	
	public function ProductsDisplay()
	{	ob_start();
		if ($this->id)
		{
			echo '<div class="mmdisplay"><div id="bpContainer">', $this->ProductsTable(), '</div><script type="text/javascript">$().ready(function(){$("body").append($(".jqmWindow"));$("#bp_modal_popup").jqm();});</script>',
				'<!-- START instructor list modal popup --><div id="bp_modal_popup" class="jqmWindow" style="padding-bottom: 5px; width: 640px; margin-left: -320px; top: 10px; height: 600px; "><a href="#" class="jqmClose submit">Close</a><div id="bpModalInner" style="height: 500px; overflow:auto;"></div></div></div>';
			}
		return ob_get_clean();
	} // end of fn ProductsDisplay
	
	public function ProductsTable()
	{	ob_start();
		echo '<form action="', $_SERVER['SCRIPT_NAME'], '?id=', $this->id, '" method="post"><table><tr class="newlink"><th colspan="6"><a onclick="ProductsPopUp(', $this->id, ',\'course\');">Add course</a>&nbsp;&mdash;&nbsp;<a onclick="ProductsPopUp(', $this->id, ',\'store\');">Add store product</a></th></tr><tr><th>Type</th><th>Details</th><th>Price</th><th>With tax</th><th>List order</th><th>Actions</th></tr>';
		if ($this->products)
		{	$taxrates = $this->GetAllTaxRates();
			$totalprice = 0;
			$totalpricetax = 0;
			foreach ($this->products as $product_row)
			{	$product = $this->GetProduct($product_row['pid'], $product_row['ptype']);
				switch ($product_row['ptype'])
				{	case 'store': 
								$totalprice += $product->details['price'];
								$totalpricetax += $pricetax = $product->details['price'] * (1 + ($taxrates[$product->details['taxid']]['rate'] / 100));
								echo '<tr><td>', $product_row['ptype'], '</td><td>', $this->InputSafeString($product->details['title']), '</td><td class="num">', number_format($product->details['price'], 2), '</td><td class="num">', number_format($pricetax, 2), '</td><td><input type="text" class="short number" name="listorder[', $product_row['bpid'], ']" value="', $product_row['listorder'], '" /></td><td><a onclick="RemoveProduct(', $this->id, ',', $product_row['bpid'], ');">remove</a></td></tr>';
								break;
					case 'course': 
								$totalprice += $product->ticket->details['tprice'];
								$totalpricetax += $pricetax = $product->ticket->details['tprice'] * (1 + ($taxrates[$product->ticket->details['taxid']]['rate'] / 100));
								echo '<tr><td>', $product_row['ptype'], '</td><td>', $this->InputSafeString($product->course->content['ctitle']), ' - ', $product->course->DatesDisplay('d/m/y'), '<br />Ticket: ', $this->InputSafeString($product->ticket->details['tname']), '</td><td class="num">', number_format($product->ticket->details['tprice'], 2), '</td><td class="num">', number_format($pricetax, 2), '</td><td><input type="text" class="short number" name="listorder[', $product_row['bpid'], ']" value="', $product_row['listorder'], '" /></td><td><a onclick="RemoveProduct(', $this->id, ',', $product_row['bpid'], ');">remove</a></td></tr>';
								break;
				}
			}
			echo '<tr><th colspan="2">Totals</th><th class="num">', number_format($totalprice, 2), '</th><th class="num">', number_format($totalpricetax, 2), '</th><th><input type="submit" class="submit" value="Save changes" /></th><th></th></tr>';
		}
		echo '</table></table>';
		return ob_get_clean();
	} // end of fn ProductsTable
	
	public function ProductTextList($sep = ', ')
	{	$products = array();
		$taxrates = $this->GetAllTaxRates();
		$totalpricetax = 0;
		foreach ($this->products as $product_row)
		{	$product = $this->GetProduct($product_row['pid'], $product_row['ptype']);
			switch ($product_row['ptype'])
			{	case 'store': 
							$totalpricetax += $pricetax = $product->details['price'] * (1 + ($taxrates[$product->details['taxid']]['rate'] / 100));
							$products[] = $this->InputSafeString($product->details['title']) . ' - &pound;' . number_format($pricetax, 2);
							break;
				case 'course': 
							$totalpricetax += $pricetax = $product->ticket->details['tprice'] * (1 + ($taxrates[$product->ticket->details['taxid']]['rate'] / 100));
							$products[] = $this->InputSafeString($product->course->content['ctitle']) . ' - ' . $product->course->DatesDisplay('d/m/y') . ', Ticket: ' . $this->InputSafeString($product->ticket->details['tname']) . ' - &pound;' . number_format($pricetax, 2);
							break;
			}
		}
		$products[] = 'Total price (inc. tax) - &pound;' . number_format($totalpricetax, 2);
		return implode($sep, $products);
	} // end of fn ProductTextList
	
} // end of defn AdminBundle
?>