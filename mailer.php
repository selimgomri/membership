<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load composer's autoloader
require 'vendor/autoload.php';

set_time_limit(600);

$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
try {
    //Server settings
    $mail->SMTPDebug = 2;                                 // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
		$mail->SMTPKeepAlive = true; // SMTP connection will not close after each email sent, reduces SMTP overhead
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'chris.heppell@chesterlestreetasc.co.uk';                 // SMTP username
    $mail->Password = 'Yokogawa1';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    //Recipients
    $mail->setFrom('noreply@chesterlestreetasc.co.uk', 'Chester-le-Street');
    $mail->addAddress('clheppell1@sheffield.ac.uk', 'Chris Heppell');     // Add a recipient
    $mail->addReplyTo('support@chesterlestreetasc.co.uk', 'Team');
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');

    //Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Testing Testing 123';
    $mail->Body    = 'This is a test message from Chester-le-Street ASC. We are testing our <strong>bulk mail sending service</strong>.';
    $mail->AltBody = 'This is a test message from Chester-le-Street ASC. We are testing our bulk mail sending service.';

    //$mail->send();
    //echo 'Message has been sent';

		for ($i=0; $i<250; $i++) {
	    $mail->addAddress('clheppell1@sheffield.ac.uk', 'Chris Heppell');
	    if (!$mail->send()) {
	        echo "Mailer Error (" . str_replace("@", "&#64;", "clheppell1@sheffield.ac.uk") . ') ' . $mail->ErrorInfo . '<br />';
	        break; //Abandon sending
	    } else {
	        echo "Message sent to :" . $row['full_name'] . ' (' . str_replace("@", "&#64;", "clheppell1@sheffield.ac.uk") . ')<br />';
	    }
	    // Clear all addresses and attachments for next loop
	    $mail->clearAddresses();
	    $mail->clearAttachments();
		}

} catch (Exception $e) {
    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
}
