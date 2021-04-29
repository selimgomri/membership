<?php

$tenant = app()->tenant;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$client = new S3Client([
  'version'     => 'latest',
  'region'      => getenv('AWS_S3_REGION'),
  'visibility' => 'public',
]);

$file = '';
try {
  $file = $tenant->getFilePath();
} catch (Exception $e) {
  halt(404);
}
$file = $file . $filename;

$key = $tenant->getId() . '/' . $filename;

if (true) {

  if (substr($filename, 0, 18) !== "notify/attachments") {
    halt(404);
  }

  $cmd = $client->getCommand('GetObject', [
    'Bucket' => getenv('AWS_S3_BUCKET'),
    'Key' => $key
  ]);

  $exists = $client->doesObjectExist(getenv('AWS_S3_BUCKET'), $key);

  if ($exists) {
    $request = $client->createPresignedRequest($cmd, '+5 minutes');

    $presignedUrl = (string)$request->getUri();

    http_response_code(302);
    header("location: " . $presignedUrl);
  } else {
    halt(404);
  }
} else {

  if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: ' . mime_content_type($file));
    if (mime_content_type($file) == 'application/pdf' || mb_substr(mime_content_type($file), 0, mb_strlen('image')) === 'image') {
      header('Content-Disposition: inline');
    } else {
      header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    }
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
  } else {
    halt(404);
  }
}
