<?php

//########################################################################################################################
// GMail mail send
//########################################################################################################################
//
// this_is_junk: modified to our purposes
//

function authgMail($from, $namefrom, $to, $nameto, $subject, $message, $smtp_cfg)
{

  /*  your configuration here  */

  $smtpServer = $smtp_cfg['host']; //does not accept STARTTLS
  $port       = $smtp_cfg['port'];
  $timeout    = "45"; //typical timeout. try 45 for slow servers
  $username   = $smtp_cfg['user'];
  $password   = $smtp_cfg['pass'];
  $localhost  = $_SERVER['REMOTE_ADDR']; //requires a real ip
  $newLine    = "\r\n"; //var just for newlines
 
  /*  you shouldn't need to mod anything else */

  //connect to the host and port
  $smtpConnect = fsockopen($smtpServer, $port, $errno, $errstr, $timeout);
  $logArray['connecterror'] = $errstr." - ".$errno;
  $logArray['die'] = $errstr." - ".$errno;
  $smtpResponse = fgets($smtpConnect, 4096);
  if(empty($smtpConnect))
  {
    $output = "Failed to connect: $smtpResponse";
    $logArray['die'] = $output;
    return $logArray;
  }
  else
  {
    $logArray['connection'] = "Connected to: $smtpResponse";
    $logArray['die'] = "Connected to: $smtpResponse";
    // this shouldn't be a die point :)
    //echo "connection accepted<br>".$smtpResponse."<p />Continuing<p />";
  }

  //you have to say HELO again after TLS is started
  fputs($smtpConnect, "HELO $localhost". $newLine);
  $smtpResponse = fgets($smtpConnect, 4096);
  $logArray['heloresponse2'] = "$smtpResponse";
  $logArray['die'] = "$smtpResponse";
  
  //request for auth login
  fputs($smtpConnect,"AUTH LOGIN" . $newLine);
  $smtpResponse = fgets($smtpConnect, 4096);
  $logArray['authrequest'] = "$smtpResponse";
  $logArray['die'] = "$smtpResponse";

  //send the username
  fputs($smtpConnect, base64_encode($username) . $newLine);
  $smtpResponse = fgets($smtpConnect, 4096);
  $logArray['authusername'] = "$smtpResponse";
  $logArray['die'] = "$smtpResponse";

  //send the password
  fputs($smtpConnect, base64_encode($password) . $newLine);
  $smtpResponse = fgets($smtpConnect, 4096);
  $logArray['authpassword'] = "$smtpResponse";
  $logArray['die'] = "$smtpResponse";

  //email from
  fputs($smtpConnect, "MAIL FROM: <$from>" . $newLine);
  $smtpResponse = fgets($smtpConnect, 4096);
  $logArray['mailfromresponse'] = "$smtpResponse";
  $logArray['die'] = "$smtpResponse";

  // send to all recipients
  $temp = explode(",", $to);
  foreach($temp as $mailto)
  {
    //email to
    fputs($smtpConnect, "RCPT TO: <".$mailto.">" . $newLine);
    $smtpResponse = fgets($smtpConnect, 4096);
    $logArray['mailtoresponse'] = "$smtpResponse";
  }

  //the email
  fputs($smtpConnect, "DATA" . $newLine);
  $smtpResponse = fgets($smtpConnect, 4096);
  $logArray['data1response'] = "$smtpResponse";
  $logArray['die'] = "$smtpResponse";

  //construct headers
  $headers = "MIME-Version: 1.0" . $newLine;
  $headers .= 'Content-type: text/html; charset="iso-8859-1"' . $newLine;
  $headers .= "To: $nameto <$to>" . $newLine;
  $headers .= "From: $namefrom <$from>" . $newLine;

  //observe the . after the newline, it signals the end of message
  fputs($smtpConnect, "To: $to\r\nFrom: $from\r\nSubject: $subject\r\n$headers\r\n\r\n$message\r\n.\r\n");
  $smtpResponse = fgets($smtpConnect, 4096);
  $logArray['data2response'] = "$smtpResponse";
  $logArray['die'] = "$smtpResponse";

  // say goodbye
  fputs($smtpConnect,"QUIT" . $newLine);
  $smtpResponse = fgets($smtpConnect, 4096);
  $logArray['quitresponse'] = "$smtpResponse";
  $logArray['quitcode'] = substr($smtpResponse,0,3);
  fclose($smtpConnect);
  //a return value of 221 in $retVal["quitcode"] is a success
  return($logArray);
}

?>