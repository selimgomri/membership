<?php
include "database.php";

use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\SMTP;

//Create a new PHPMailer instance
$mail = new PHPMailer;

echo "Created Object<br>";


$mail->isSendmail();
echo "isSendmail() Success<br>";
/*$mail->SMTPDebug = 2;                                 // Enable verbose debug output
$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.gmail.com';  											// Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'chris.heppell@chesterlestreetasc.co.uk';                 // SMTP username
$mail->Password = 'akzfqpihysbxqgvo';                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587;                                    // TCP port to connect to
*/
//Set who the message is to be sent from
$mail->setFrom('noreply@heppellit.com', 'Chester-le-Street ASC');
//Set an alternative reply-to address
$mail->addReplyTo('hello@chesterlestreetasc.co.uk', 'CLS ASC Enquiries');
//Set who the message is to be sent to
$mail->addAddress('clheppell1@sheffield.ac.uk', 'Chris Heppell');
//Set the subject line
$mail->Subject = 'PHPMailer sendmail test';
echo "->Subject() Success<br>";
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->msgHTML(file_get_contents('README.md'), __DIR__);
//Replace the plain text body with one created manually
$mail->AltBody = 'This is a plain-text message body';
//Attach an image file
//$mail->addAttachment('images/phpmailer_mini.png');
//send the message, check for errors
echo "->BeforeSend() Success<br>";
$mail->send();
echo "->AfterSend() Success<br>";
/*if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}*/
