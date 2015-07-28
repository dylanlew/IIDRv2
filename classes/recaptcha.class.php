<?php
class ReCaptcha extends Base
{	private $keys = array('public'=>'6Ld2XOsSAAAAAAzy9_9c9tEeB4HIc5kGeq9Q6rX2', 'private'=>'6Ld2XOsSAAAAALPdDxQUqwrJnZmKoBqIWWAuk9PQ');
	private $domain_name = 'iidr.websquarehost.co.uk ';
	private $verify_url = 'http://www.google.com/recaptcha/api/verify';
	private $verify_host = 'www.google.com';
	private $verify_path = '/recaptcha/api/verify';
	private $verify_port = '80';
	public $error = '';
	
	public function __construct()
	{	parent::__construct();
	} // fn __construct

	public function OutputInForm()
	{	ob_start();
		if ($this->error)
		{	$errorpart = '&error=' . $this->error;
		}
		echo '<script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k=', $this->keys['public'], $errorpart, '"></script>
	<noscript>
		<iframe src="http://www.google.com/recaptcha/api/noscript?k=', $this->keys['public'], $errorpart, '" height="300" width="500" frameborder="0"></iframe><br>
		<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
		<input type="hidden" name="recaptcha_response_field" value="manual_challenge" />
	</noscript>';
		return ob_get_clean();
	} // end of fn OutputInForm
		
	public function VerifyInput()
	{	$end = "\r\n";
	
		$data = array('privatekey' => $this->keys['private'],
					 'remoteip' => $_SERVER['REMOTE_ADDR'],
					 "challenge" => $_POST['recaptcha_challenge_field'],
					 "response" => $_POST['recaptcha_response_field']);
		
		$req = $this->ArrayToQueryString($data);

		ob_start();
		echo 'POST ', $this->verify_path, ' HTTP/1.0', $end, 'Host: ', $this->verify_host, $end, 'Content-Type: application/x-www-form-urlencoded;', $end, 'Content-Length: ', strlen($req), $end, 'User-Agent: reCAPTCHA/PHP', $end, $end, $req;
		$http_request = ob_get_clean();

		if ($fs = @fsockopen($this->verify_host, $this->verify_port, $errno, $errstr, 10))
		{	fwrite($fs, $http_request);
			$response_str = '';
			while (!feof($fs))
			{	$response_str .= fgets($fs, 1160); // One TCP-IP packet
			}
			fclose($fs);

			$response_array = explode("\r\n\r\n", $response_str, 2);
			$response = explode("\n", $response_array[1], 2);
			if ($response[0] == 'true')
			{	return true;
			} else
			{	$this->error = $response[1];
			}
			
		} else
		{	return false;
		}

	} // end of fn VerifyInput
	
	public function ArrayToQueryString($data = array())
	{	$req = array();
		if (is_array($data) && $data)
		{	foreach ($data as $key=>$value)
			{	$req[] = $key . '=' . urlencode( stripslashes($value) );
			}
		}
		return implode('&', $req);
	} // end of fn ArrayToQueryString

} // end of class ReCaptcha
?>