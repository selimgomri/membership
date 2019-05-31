<?php

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Response\QrCodeResponse;

$text = (isset($_GET["text"])?$_GET["text"]:"0");
$size = (isset($_GET["size"])?$_GET["size"]:"300");
$margin = (isset($_GET["margin"])?$_GET["margin"]:10);
$ec = false;
if ($_GET["error"] == "true") {
	$ec = true;
}

// Create a basic QR code
$qrCode = new QrCode($text);
$qrCode->setSize($size);

// Set advanced options
$qrCode->setWriterByName('png');
$qrCode->setMargin((int) $margin);
$qrCode->setEncoding('UTF-8');
if ($ec) {
	$qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH);
}
$qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
$qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 127]);
//$qrCode->setLogoPath("/customers/9/d/e/chesterlestreetasc.co.uk/httpd.www/apple-touch-icon-ipad-retina.png");
//$qrCode->setLogoWidth($size/6);
$qrCode->setRoundBlockSize(true);
$qrCode->setValidateResult(false);

// Directly output the QR code
header('Content-Type: '.$qrCode->getContentType());
echo $qrCode->writeString();
