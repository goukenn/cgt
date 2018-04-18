<?php
//Send mail demo
/*
class send_smtp_mail
{
	public function sendMail()
	{
		$errno = "";
		$errstr="";
		// $socket = @fsockopen("ssl://smtp.gmail.com", 465, $errno, $errstr, 10);
	  // if(!$socket)
	  // {
		// igk_wln("ERROR: smtp.gmail.com 465 - $errstr ($errno)");
	  // }
	  // else
	  // {
		// igk_wln("SUCCESS: smtp.gmail.com 465 - ok");
	  // }
	 
	  $socket = fsockopen("smtp.gmail.com", 587, $errno, $errstr, 15);
	  if(!$socket)
	  {
		igk_debug_wln("ERROR: smtp.gmail.com 587 - $errstr ($errno)");
	  }
	  else
	  {
		igk_debug_wln("SUCCESS: smtp.gmail.com 587 - ok");
	
	$user = "bondje.doue@gmail.com";
	$pass  = "bonaje123";
	$smtp_host = "smtp.gmail.com";
	$recipients = array("bondje.doue@gmail.com");
	$subject = "test";
	$message = "the message";
	$headers = "";
		
		

		if (!$this->server_parse($socket, '220')) { $this->_closeSocket($socket); return false;}   
		
		fwrite($socket, 'EHLO '.$smtp_host."\r\n");				
		if (!$this->server_parse($socket, '250')) { $this->_closeSocket($socket); return false;}    
		
		
		 
		fwrite($socket, 'STARTTLS'."\r\n");		
		if (!$this->server_parse($socket, '220')) { $this->_closeSocket($socket); return false;}    
		
		if(false == @stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT))
		{
			$this->_closeSocket($socket);
			igk_debug_wln("unable to start tls encryption");
			return false;
		}

		
			fwrite($socket, 'HELO '.$smtp_host."\r\n");		
			if (!$this->server_parse($socket, '250')) return false;


			fwrite($socket, 'AUTH LOGIN'."\r\n");    
			if (!$this->server_parse($socket, '334')) { $this->_closeSocket($socket); return false;}    


			fwrite($socket, base64_encode($user)."\r\n");    
			if (!$this->server_parse($socket, '334')) { $this->_closeSocket($socket); return false;}    

			fwrite($socket, base64_encode($pass)."\r\n");    
			if (!$this->server_parse($socket, '235')) { $this->_closeSocket($socket); return false;}    

			fwrite($socket, 'MAIL FROM: <'.$user.'>'."\r\n");    
			if (!$this->server_parse($socket, '250')) { $this->_closeSocket($socket); return false;}    
			foreach ($recipients as $email)
			{
			fwrite($socket, 'RCPT TO: <'.$email.'>'."\r\n");
			if (!$this->server_parse($socket, '250')) { $this->_closeSocket($socket); return false;}    
			}
			fwrite($socket, 'DATA'."\r\n");    
			if (!$this->server_parse($socket, '354')) { $this->_closeSocket($socket); return false;}

			fwrite($socket, 'Subject: '
			.$subject."\r\n".'To: <'.implode('>, <', $recipients).'>'
			."\r\n".$headers."\r\n\r\n".$message."\r\n");
			fwrite($socket, '.'."\r\n");
			
			if (!$this->server_parse($socket, '250')) { $this->_closeSocket($socket); return false;}
			
			$this->_closeSocket($socket);
			return true;

	  }
	  
	  

	}
	private function _closeSocket($socket)
	{
		fwrite($socket, 'QUIT'."\r\n");
		fclose($socket);
	}
	//Function to Processes Server Response Codes
	private function server_parse($socket, $expected_response)
	{
		if (igk_getv(socket_get_status($socket), "eof"))
		{			
			return false;
		}		
		$server_response = '';
		while (substr($server_response, 3, 1) != ' ')
		{
			if (!($server_response = fgets($socket, 256)))
			{			
			  igk_debug_wln('Error while fetching server response codes.');
			  return false;
			}            
		}
		igk_debug_wln('OK : "'.$server_response.'"');
		if (!(substr($server_response, 0, 3) == $expected_response))
		{
		  igk_debug_wln('Unable to send e-mail."'.$server_response.'"');
		  return false;
		}
		return true;
	}
}

//$v = (new send_smtp_mail());

IGKApp::$DEBUG = true;
//igk_wln("out : ".igk_parsebool($v->sendMail()));
$mail =  new IGKMail();
$mail->UseAuth = true;
$mail->User = "igkdevbe"; //"bondje.doue@gmail.com";
$mail->Pwd = "bonajehost1983"; //"bonaje123";
$mail->Port = 587;//  465; //587;
$mail->SmtpHost = "mail.igkdev.be";//"ssl://smtp.gmail.com";
$mail->SocketType = "ssl";
$mail->HtmlMsg = "<b>Information</b> information sur la pierre ét pui quoi ";
$mail->Title = "C un test et encore je suis gen";
$mail->From = "DARKVADOR@igkdev.be";

$mail->HtmlCharset = "utf-8";
$mail->TextCharset = "utf-8";
$mail->addTo("bondje.doue@gmail.com");

igk_debug_wln("response " . igk_parsebool($mail->sendMail()));

IGKApp::$DEBUG = false;
exit;
*/
?>