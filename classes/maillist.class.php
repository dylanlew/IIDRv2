<?php
class MailList extends Base
{
	function __construct()
	{	parent::__construct();
	} // end of fn __construct
	
	function Save($data = array())
	{	$fields = array();
		$fail = array();
		$success = array();
		
		if ($listname = $this->SQLSafe($data['listname']))
		{	$fields[] = 'listname="' . $listname . '"';
		} else
		{	$fail[] = 'You must enter your name.';
		}
		
		if ($listemail = $data['listemail'])
		{	if ($this->ValidEMail($listemail))
			{	// check already registered
				if (!$mlid = $this->AlreadyRegistered($listemail))
				{	$fields[] = 'registered="' . $this->datefn->SQLDateTime() . '"';
				}
				$fields[] = 'listemail="' . $listemail . '"';
			} else
			{	$fail[] = 'Your email is not valid.';
			}
		} else
		{	$fail[] = 'Your must enter your email address.';
		}
		
		if (!$fail && ($set = implode(', ', $fields)))
		{	if ($mlid = (int)$mlid)
			{	$sql = 'UPDATE maillist SET ' . $set . ' WHERE mlid=' . $mlid;
			} else
			{	$sql = 'INSERT INTO maillist SET ' . $set;
			}
			if ($result = $this->db->Query($sql))
			{	$success[] = 'You have been added to our mailing list.';
			}
		}
		
		return array('failmessage'=>implode('<br /><br />', $fail), 'successmessage'=>implode('<br /><br />', $success));
		
	} // end of fn Save
	
	public function AlreadyRegistered($listemail  = '')
	{	$mlid = 0;
		$sql = 'SELECT mlid FROM maillist WHERE listemail="' . $this->SQLSafe($listemail) . '"';
		if ($result = $this->db->Query($sql))
		{	if ($row = $this->db->FetchArray($result))
			{	return $row['mlid'];
			}
		}
	} // end of fn AlreadyRegistered
	
	function InputForm()
	{	ob_start();
		
		echo '<form method="post" onsubmit="MailListFormSubmit(); return false;"><div id="mlfeedback" class="maillistfeedback"></div><p><input class="text" type="text" onfocus="clearField(this, \'Name\')" onblur="fillField(this, \'Name\');" id="ml_listname" value="Name" /></p><p><input class="text" type="text" onfocus="clearField(this, \'Email\')" onblur="fillField(this, \'Email\');" id="ml_listemail" value="Email" /></p><p><input type="submit" class="submit" value="Sign up to our newsletter" /></p></form>';
		
		echo "<script>function MailListFormSubmit() {
					$('.maillistfeedback').on('mouseover', function () {
						$('.maillistfeedback').animate({top : -100,  opacity: 0 }, 3000);
					});
					
					$.ajax({ 
						type: 'POST', 
						url: jsSiteRoot + 'ajax_maillist.php', 
						dataType: 'json', 
						data: { listname: $('#ml_listname').val(), listemail: $('#ml_listemail').val() }
					}).done(function(response) {
					  if(response != null)
					  { 
						$('.maillistfeedback').stop().animate({top : 0,  opacity: 1 });
						if(response.status == 1)
						{ 
						  $('#mlfeedback').html('<div class=\'successmessage\'>'+response.message+'</div>');
						  $('#ml_listname').val('');
						  $('#ml_listemail').val('');
						}
						else
						{ $('#mlfeedback').html('<div class=\'failmessage\'>'+response.message+'</div>');
						}
					  }
					}); }</script>";
		return ob_get_clean();
	} // end of fn InputForm
	
} // end of defn MailList
?>