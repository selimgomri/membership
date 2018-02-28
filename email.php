<?php
//require 'config.php';
$sendGridApiKey = "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";
// using SendGrid's PHP Library
// https://github.com/sendgrid/sendgrid-php
// If you are using Composer (recommended)
require 'vendor/autoload.php';
// If you are not using Composer
// require("path/to/sendgrid-php/sendgrid-php.php");
$from = new SendGrid\Email("Chester-le-Street ASC", "noreply@chesterlestreetasc.co.uk");
$subject = "Sending with SendGrid is Fun";
$to = new SendGrid\Email("Example User", "clheppell1@sheffield.ac.uk");
$content = new SendGrid\Content("text/plain", "and easy to do anywhere, even with PHP");
$mail = new SendGrid\Mail($from, $subject, $to, $content);
$sg = new \SendGrid($sendGridApiKey);
$response = $sg->client->mail()->send()->post($mail);
echo $response->statusCode();
print_r($response->headers());
echo $response->body();
