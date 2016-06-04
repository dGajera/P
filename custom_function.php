<?php date_default_timezone_set('Asia/Kolkata');?>
<?php function send_mail($message, $subject, $fromEmail, $fromName, $mailtype, $attachment) {
	ob_start();
    
	
	// convert to PDF
    require_once(dirname(__FILE__).'/html2pdf/html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->writeHTML($message, isset($_GET['vuehtml']));
        $html2pdf->Output(dirname(__FILE__).'/mail_attachment/pdf'.'/'.$mailtype.'-'.time().'.pdf', 'F');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
	//echo dirname(__FILE__);
	 include('mail/PHPMailerAutoload.php');
	 //require_once(dirname(__FILE__).'/html2pdf/PHPMailerAutoload.php');

		
		
		
	
		$mail = new PHPMailer(true);
		$aemail="info@whiteorangesoftware.com"; // Recipients email ID
		//$aemail2="bmpcom9401@gmail.com"; // Recipients email ID
		$aname='whiteorange software admin'; // Recipient's name
		//$mail->IsSMTP(); 
		$mail->Host       = "mail.whiteorangesoftware.com"; // SMTP server
		$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
												   // 1 = errors and messages
												   // 2 = messages only
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->Host       = "mail.whiteorangesoftware.com"; // sets the SMTP server
		$mail->Port       = 25;                    // set the SMTP port for the GMAIL server
		$mail->Username   = "info@whiteorangesoftware.com"; // SMTP account username
		$mail->Password   = "wos@123";        // SMTP account password	
		$mail->AddAddress($aemail,$aname);
		//$mail->AddAddress($aemail2,$aname);
		try {
		  $mail->SetFrom($fromEmail,$fromName);
		  $mail->AddReplyTo($fromEmail, $fromName);
		  $mail->Subject = $subject;
		  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
		  $mail->Body =$message;
		  $mail->IsHTML(true);    
		  foreach($attachment as $file){
			$mail->addAttachment(dirname(__FILE__).'/mail_attachment/attachment'.'/'.$file); 	
		}
		$mail->addAttachment(dirname(__FILE__).'/mail_attachment/pdf'.'/'.$mailtype.'-'.time().'.pdf');
		$flag = $mail->Send();	
		  //$mail->Send();
		  
		} catch (phpmailerException $e) {
		  echo $e->errorMessage(); //Pretty error messages from PHPMailer
		} catch (Exception $e) {
		  echo $e->getMessage(); //Boring error messages from anything else!
		}			
	
	if(!$mail->Send()) {

	return false;
	

	} else {

	return true;

	}
	
	
}
add_action('init', 'clean_output_buffer');
function clean_output_buffer() {
        ob_start();
}
