<?php
include_once("init.php");

class PayPalEar extends PayPalStandard
{	var $logfile = "ppearlog.txt";

	function __construct()
	{	parent::__construct();
	//	$this->logfile = "../" . SITE_OFFROOT . "/" . $this->logfile;
	} // end of fn __construct
	
	function Verify()
	{	$postarray = array();
		foreach($_POST as $key=>$value)
		{	$postarray[] = $key. '=' . urlencode($value);
		}
		$postarray[] = 'cmd=_notify-validate';
	//	$this->LogRecord("verify tried ... \r\n" . implode("\r\n", $postarray));
		
		if ($_POST["receiver_id"] == $this->ourpaypalid || $_POST["receiver_email"] == $this->ourpaypalemail)
		{		
			$postdata = implode("&", $postarray);
			if ($web = parse_url($this->verify_url))
			{	if ($web['scheme'] == 'https')
				{	$web['port'] = 443;
					$ssl = 'ssl://';
				} else
				{	$web['port'] = 80;
					$ssl = '';
				}
				if ($fp = @fsockopen($ssl . $web['host'], $web['port'], $errnum, $errstr, 30))
				{	fputs($fp, "POST ".$web['path']." HTTP/1.1\r\n");
					fputs($fp, "Host: ".$web['host']."\r\n");
					fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
					fputs($fp, "Content-length: ".strlen($postdata)."\r\n");
					fputs($fp, "Connection: close\r\n\r\n");
					fputs($fp, $postdata . "\r\n\r\n");
					
					while(!feof($fp))
					{	$info[] = @fgets($fp, 1024);
					}
					fclose($fp);
					$info = implode(',', $info);
					if (eregi('VERIFIED', $info))
					{	return true;
					} else
					{	$this->LogRecord("verify failed ... $postdata");
					}
				} else
				{	$logtextarray = array();
					foreach ($web as $k=>$v)
					{	$logtextarray[] = "$k=>$v";
					}
					$this->LogRecord("fsockerror ... $errnum:$errstr ... " . implode("; ", $logtextarray));
				}
			}
		} else
		{	$this->LogRecord("mismatched receiver_email ... posted:{$_POST["receiver_id"]}!={$this->ourpaypalid}");
		}
		return false;
 	} // end of fn Verify
	
	function LogRecord($text = '')
	{	//mail('tim@websquare.co.uk', 'diagnostics from ppear iidr', $text);
	/*	if ($fhandle = fopen($this->logfile, "a"))
		{	$line = date("Y-m-d H:i -- ") . $text . "\n";
			fputs($fhandle, $line);
			fclose($fhandle);
		}*/
	} // end of fn LogRecord
	
	function Action()
	{	
		switch ($_POST['txn_type'])
		{	case 'cart':
			case 'web_accept':
				if ($orderid = (int)$_POST['custom'])
				{	$order = new StoreOrder($orderid);
					$result = $order->RecordPaypalPayment($_POST);
					if ($result['failmessage'])
					{	ob_start();
						echo $result['failmessage'], "\n";
						print_r($_POST);
						$this->LogRecord(ob_get_clean());
					}
				}
				break;
			default:
				$this->LogFullPost('post from paypal not processed');
		}
	} // end of fn Action
	
	function StringFromPost()
	{	$post = array();
		foreach ($_POST as $key=>$value)
		{	$post[] = "$key=$value";
		}
		return implode("\r\n", $post);
	} // end of fn StringFromPost
	
	function LogFullPost($text = "")
	{	$this->LogRecord($text . "\r\n" . $this->StringFromPost());
	} // end of fn LogFullPost
	
} // end of defn PayPalEar

$pp_ear = new PayPalEar();
if ($pp_ear->Verify())
{	$pp_ear->Action();
}
?>